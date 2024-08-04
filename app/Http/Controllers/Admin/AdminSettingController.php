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
    protected $database;

    public function __construct(Firestore $firestore, Auth $auth, Database $database)
    {
        $this->auth = $auth;
        // $this->db = $firestore->database();
        $this->database = $database;

        $firestore = app('firebase.firestore');
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
        // $userDoc = $this->firestore->collection('users')->document($uid)->snapshot();
        $snapshot = $this->database->getReference('users')->getChild($uid)->getSnapshot();
        $userDoc = $snapshot->getValue();

        return $userDoc;
    }

    public function showSetting(Request $request)
    {
        $idToken = $this->checkToken($request);

        $uid = $this->getUID($idToken);

        $userDoc = $this->getUserData($uid);

        $userAuth = $this->auth->getUser($uid);
        $email = $userAuth->email;

        return view('admin.setting', [
            'userDoc' => $userDoc,
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

            // $this->db->collection('users')->document($user->uid)->set($userData);

            $this->auth->changeUserPassword($uid, $validated['newPassword']);

            return redirect()->route('admin-setting-pass-token')->with('success', 'User registered successfully!');
        } catch (\Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }
}
