<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserDashboardController extends Controller
{
    protected $auth;
    protected $db;
    protected $tokenController;
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
        $userData = $this->db->collection('user')->document($uid)->snapshot();

        $containerQuery = $this->db->collection('container')->where('userID', '=', $uid);
        $containerDocs = $containerQuery->documents();

        $containerList = [];
        foreach ($containerDocs as $containerData) {
            if ($containerData->exists()) {
                $containerList[$containerData->id()] = $containerData->data();
            }
        }

        $maxDrink = 0;
        if(!empty($userData['maxDrink'])){
            $maxDrink = $userData['maxDrink'];
        }

        $drankWater = 0;
        if (!empty($userDoc['drankWater'])) {
            $drankWater = $userData['drankWater'];
        }
        $targetDrink = 2500;

        $percentage = ($drankWater / $targetDrink) * 100;

        return view('user.dashboard', [
            'userData' => $userData,
            'drankWater' => $drankWater,
            'percentage' => $percentage,
            'containerList' => $containerList,
            'maxDrink' => $maxDrink,
        ]);
    }
}
