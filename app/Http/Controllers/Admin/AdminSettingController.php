<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class AdminSettingController extends Controller
{
    protected $auth;
    protected $db;

    public function __construct(Firestore $firestore, Auth $auth)
    {
        $this->auth = $auth;
        $this->db = $firestore->database();
    }

    public function passToken(Request $request)
    {
        $idToken = session('idToken');

        return redirect()->route('admin-setting', [
            'idToken' => $idToken
        ]);
    }

    public function checkToken($request){
        $idToken = session('idToken');

        if(!$request->has('idToken')){
            $request->session()->forget('idToken');

            return redirect()->route('login_GET')->withErrors(['error' => 'No session found. Please login first']);
        }

        return $idToken;
    }

    public function getUID($idToken){
        $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        $uid = $verifiedIdToken->claims()->get('sub');

        return $uid;
    }

    public function getUserData($uid){
        $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

        return $userData;
    }

    public function showSetting(Request $request)
    {
        $idToken = $this->checkToken($request);

        $uid = $this->getUID($idToken);

        $userData = $this->getUserData($uid);

        $userAuth = $this->auth->getUser($uid);
        $email = $userAuth->email;

        return view('admin.setting', [
            'userData' => $userData,
            'email' => $email,
        ]);
    }

    public function changePassword(Request $request){
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

        $uid = $this->getUID($idToken);

        $userAuth = $this->auth->getUser($uid);
        $userEmail = $userAuth->email;


        try{
            $signInResult = $this->auth->signInWithEmailAndPassword($userEmail, $request->oldPassword);

            if($request->newPassword === $request->oldPassword){
                return response()->json(['errors' => ['The new password must be different from the old password.']], 422);
            }

            if($request->rePassword !== $request->newPassword){
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
}
