<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserContainerController extends Controller
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

    public function index(Request $request)
    {
        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);
        $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

        $drankWater = 0;
        if (!empty($userData['drankWater'])) {
            $drankWater = $userData['drankWater'];
        }

        $targetDrink = 2500;

        $percentage = ($drankWater / $targetDrink) * 100;

        return view('user.container', [
            'userData' => $userData,
            'drankWater' => $drankWater,
            'percentage' => $percentage
        ]);
    }
}
