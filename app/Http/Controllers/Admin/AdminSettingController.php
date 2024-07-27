<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function showSetting(Request $request)
    {
        $idToken = session('idToken');
        $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        $uid = $verifiedIdToken->claims()->get('sub');
        // $userDoc = $this->firestore->collection('users')->document($uid)->snapshot();
        $snapshot = $this->database->getReference('users')->getChild($uid)->getSnapshot();
        $userDoc = $snapshot->getValue();

        $userAuth = $this->auth->getUser($uid);
        $email = $userAuth->email;

        return view('admin.setting', [
            'userDoc' => $userDoc,
            'email' => $email,
        ]);
    }
}
