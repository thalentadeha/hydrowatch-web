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
        $userDoc = $this->db->collection('user')->document($uid);
        $userData = $userDoc->snapshot();

        $containerQuery = $this->db->collection('container')->where('userID', '=', $uid);
        $containerDocs = $containerQuery->documents();

        $containerList = [];
        foreach ($containerDocs as $containerData) {
            if ($containerData->exists()) {
                $containerList[$containerData->id()] = $containerData->data();
            }
        }

        $maxDrink = 0;
        if (!empty($userData['maxDrink'])) {
            $maxDrink = $userData['maxDrink'];
        }

        $year = (string) date('Y');
        $month = (string) date('n');
        $date = (string) date('d');
        $userDrinkHistory = $userDoc->collection('drinkHistory')->document($year)->collection($month)->document($date)
            ->snapshot()->data();

        $lastDrinkTime = '--:--';
        if (!empty($userDrinkHistory['lastDrink'])) {
            $lastDrinkTime = substr($userDrinkHistory['lastDrink'], 0, -3);
        }

        $drankWater = 0;
        if (!empty($userDrinkHistory['drank'])) {
            $drankWater = $userDrinkHistory['drank'];
        }
        $targetDrink = 2500;

        $percentage = ($drankWater / $targetDrink) * 100;

        return view('user.dashboard', [
            'userData' => $userData,
            'drankWater' => $drankWater,
            'percentage' => $percentage,
            'containerList' => $containerList,
            'maxDrink' => $maxDrink,
            'lastDrinkTime' => $lastDrinkTime,
        ]);
    }
}
