<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $validated = $request->validate([
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
        ]);

        $idToken = $this->checkToken($request);

        $uid = $this->getUID($idToken);

        $userAuth = $this->auth->getUser($uid);
        $password = $userAuth->passwordHash;

        if(!Hash::check($validated['oldPassword'], $password)){
            return back()->with('error', 'password not matched');
        }

        if($validated['newPassword'] === $validated['oldPassword']){
            return back()->with('error', 'choose a different password');
        }

        if($validated['rePassword'] !== $validated['newPassword']){
            return back()->with('error', 'password not matched');
        }

        try {
            $this->auth->changeUserPassword($uid, $validated['newPassword']);

            return redirect()->route('admin-setting-pass-token')->with('success', 'User registered successfully!');
        } catch (\Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }
}
