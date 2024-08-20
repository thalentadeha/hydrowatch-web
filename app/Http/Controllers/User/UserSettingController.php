<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserSettingController extends Controller
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

    public function index(Request $request)
    {
        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        $userData = $this->db->collection('user')->document($uid)->snapshot();

        $userAuth = $this->auth->getUser($uid);
        $email = $userAuth->email;

        $maxDrink = 0;
        if(!empty($userData['maxDrink'])){
            $maxDrink = $userData['maxDrink'];
        }

        $isNotificationEnabled = false;
        if(!empty($userData['isNotificationEnabled'])){
            $isNotificationEnabled = $userData['isNotificationEnabled'];
        }

        return view('user.setting', [
            'userData' => $userData,
            'email' => $email,
            'maxDrink' => $maxDrink,
            'isNotificationEnabled' => $isNotificationEnabled
        ]);
    }
}
