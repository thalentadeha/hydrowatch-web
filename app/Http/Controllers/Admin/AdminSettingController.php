<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuthController;
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
    protected $authController;

    public function __construct(
        Firestore $firestore,
        Auth $auth,
        AuthController $authController
    ) {
        $this->auth = $auth;
        $this->db = $firestore->database();
        $this->authController = $authController;
    }

    public function showSetting(Request $request)
    {
        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

        $userAuth = $this->auth->getUser($uid);
        $email = $userAuth->email;

        return view('admin.setting', [
            'userData' => $userData,
            'email' => $email,
        ]);
    }
}
