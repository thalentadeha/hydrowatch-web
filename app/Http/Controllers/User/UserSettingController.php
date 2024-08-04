<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserSettingController extends Controller
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

        return redirect()->route('user-setting', [
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

    public function index(Request $request)
    {
        $idToken = $this->checkToken($request);

        $uid = $this->getUID($idToken);

        $userData = $this->getUserData($uid);

        $userAuth = $this->auth->getUser($uid);
        $email = $userAuth->email;

        return view('user.setting', [
            'userData' => $userData,
            'email' => $email,
        ]);
    }

    public function changeNickname(Request $request)
    {
        $idToken = session('idToken');

        $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        $uid = $verifiedIdToken->claims()->get('sub');
        $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

        $userAuth = $this->auth->getUser($uid);
        $email = $userAuth->email;

        return view('user.setting', [
            'userData' => $userData,
            'email' => $email,
        ]);
    }
}
