<?php

namespace App\Http\Controllers;

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
        $validator = Validator::make($request->all(), [
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
        ], [
            'fullname.required' => 'The fullname field is required.',
            'fullname.string' => 'The fullname must be a string.',

            'nickname.required' => 'The nickname field is required.',
            'nickname.string' => 'The nickname must be a string.',
            'nickname.max' => 'The nickname may not be greater than 20 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',

            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.regex' => 'The password must include at least one lowercase letter, one uppercase letter, one number, and one symbol.',

            'role.required' => 'The role field is required.',
            'role.string' => 'The role must be a string.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $validated = $validator->validate();

        $user = $this->auth->createUserWithEmailAndPassword($validated['email'], $validated['password']);

        try {
            $userData = [
                'fullname' => $validated['fullname'],
                'nickname' => $validated['nickname'],
                'role' => $validated['role'],
            ];

            $this->db->collection('user')->document($user->uid)->set($userData);

            return response()->json(['success' => 'User registered successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['errors' => ['Something went wrong.']], 422);
        }
    }
    public function deleteUser(Request $request)
    {
        $email = $request->input('email');

        try {
            $user = $this->auth->getUserByEmail($email);

            $this->deleteAllUserContainer($request, $user->uid);

            $this->db->collection('user')->document($user->uid)->delete();
            $this->auth->deleteUser($user->uid);

            return response()->json(['success' => 'All datas associated with the user have been deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['errors' => ['User Not Found.']], 422);
        }
    }

    private function deleteAllUserContainer(Request $request, $uid)
    {
        try {
            $containers = $this->db->collection('container')->where('userID', '=', $uid)->documents();

            if ($containers->isEmpty()) {
                return response()->json(['errors' => ['No containers found for the given user.']], 422);
            }

            foreach ($containers as $container) {
                $container->reference()->delete();
            }
        } catch (Exception $e) {
            return response()->json(['errors' => ['User Not Found.']], 422);
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

            //save in user document
            $userDoc = $this->db->collection('user')->document($uid);
            $userDoc->update([
                ['path' => 'maxDrink', 'value' => $validated['maxDrink']]
            ]);

            //save in drinkHistory document
            $year = (string) date('Y');
            $month = (string) date('n');
            $date = (string) date('d');
            $drinkHistoryDoc = $this->db->collection('user')->document($uid)->collection('drinkHistory')->document($year)->collection($month)->document($date);
            $drinkHistoryDoc->set([
                'maxDrink' => $validated['maxDrink']
            ], ['merge' => true]);

            return response()->json(['success' => 'Set max drink successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }
}
