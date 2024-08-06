<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserManagerController extends Controller
{
    protected $auth;
    protected $db;
    protected $authController;

    public function __construct(Firestore $firestore, Auth $auth, AuthController $authController)
    {
        $this->auth = $auth;
        $this->db = $firestore->database();
        $this->authController = $authController;
    }
    public function showRegister()
    {
        // return view('auth.register');
        return view('admin.register_temp');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string',
            'nickname' => 'required|string|max:20',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',      //atleast 1 uppercase
                'regex:/[0-9]/',      //atleast 1 number
                'regex:/[@$!%*?&]/', //atleast 1 symbol
            ],
            'role' => 'required|string'
        ]);

        $user = $this->auth->createUserWithEmailAndPassword($validated['email'], $validated['password']);

        try {
            $userData = [
                'fullname' => $validated['fullname'],
                'nickname' => $validated['nickname'],
                'role' => $validated['role'],
            ];

            $this->db->collection('user')->document($user->uid)->set($userData);

            return back()->with('success', 'User registered successfully!');
        } catch (\Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }
    public function deleteUser(Request $request)
    {
        $email = $request->input('email');

        try {
            $user = $this->auth->getUserByEmail($email);

            $this->db->collection('user')->document($user->uid)->delete();
            $this->auth->deleteUser($user->uid);

            return back()->with('status', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'User deletion failed']);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPassword' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',      //atleast 1 uppercase
                'regex:/[0-9]/',      //atleast 1 number
                'regex:/[@$!%*?&]/', //atleast 1 symbol
            ],
            'newPassword' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',      //atleast 1 uppercase
                'regex:/[0-9]/',      //atleast 1 number
                'regex:/[@$!%*?&]/', //atleast 1 symbol
            ],
            'rePassword' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',      //atleast 1 uppercase
                'regex:/[0-9]/',      //atleast 1 number
                'regex:/[@$!%*?&]/', //atleast 1 symbol
            ],
        ], [
            'oldPassword.required' => 'Old password is required.',
            'oldPassword.min' => 'The old password field must be at least 8 characters.',
            'oldPassword.regex' => 'The old password must include at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'newPassword.required' => 'New password is required.',
            'newPassword.min' => 'The new password must be at least 8 characters.',
            'newPassword.regex' => 'The new password must include at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'rePassword.required' => 'Re-entered password is required.',
            'rePassword.min' => 'Re-entered password must be at least 8 characters.',
            'rePassword.regex' => 'Re-entered password must include at least one lowercase letter, one uppercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        $userAuth = $this->auth->getUser($uid);
        $userEmail = $userAuth->email;


        try {
            $signInResult = $this->auth->signInWithEmailAndPassword($userEmail, $request->oldPassword);

            if ($request->newPassword === $request->oldPassword) {
                return response()->json(['errors' => ['The new password must be different from the old password.']], 422);
            }

            if ($request->rePassword !== $request->newPassword) {
                return response()->json(['errors' => ['Re-entered password does not match the new password.']], 422);
            }

            $request->session()->forget('idToken');

            $idToken = $signInResult->idToken();
            session(['idToken' => $idToken]);

            $validated = $validator->validate();

            $this->auth->changeUserPassword($uid, $validated['newPassword']);

            return response()->json(['success' => 'Change password successful']);
        } catch (\Kreait\Firebase\Auth\SignIn\FailedToSignIn $e) {
            return response()->json(['errors' => ['Invalid old password.']], 422);
        }
    }

    public function changeNickname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => [
                'string',
                'max:20',
            ]
        ], [
            'nickname.max' => 'Nickname exceeded maximum characters (20).',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        try {
            $validated = $validator->validate();

            $userDoc = $this->db->collection('user')->document($uid);
            $userDoc->update([
                ['path' => 'nickname', 'value' => $validated['nickname']]
            ]);

            return response()->json(['success' => 'Change nickname successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }

    public function setMaxDrink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'maxDrink' => [
                'numeric',
                'min:100',
                'max:6000'
            ]
        ], [
            'maxDrink.min' => 'mL should not be less than 100.',
            'maxDrink.max' => 'mL should not be more than 6000.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        try {
            $validated = $validator->validate();

            $userDoc = $this->db->collection('user')->document($uid);

            $userDoc->update([
                ['path' => 'maxDrink', 'value' => $validated['maxDrink']]
            ]);

            return response()->json(['success' => 'Set max drink successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }
}
