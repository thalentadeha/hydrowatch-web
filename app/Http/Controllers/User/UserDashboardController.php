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

        // User Data
        $uid = $this->authController->getUID($idToken);
        $userDoc = $this->db->collection('user')->document($uid);
        $userData = $userDoc->snapshot();

        // User Drink History
        $year = (int) date('Y');
        $month = (int) date('n');
        $date = (int) date('d');
        $daysInMonth = (int) date('t');

        $userDrankHistory = [];
        $userMaxDrinkHistory = [];
        $listDrinkHistory = $userDoc->collection('drinkHistory')
                                ->document($year)
                                ->collection($month)
                                ->documents();

        for($i = 1; $i <= $daysInMonth; $i++) {
            $userDrankHistory[(String) $i] = 0;
            $userMaxDrinkHistory[(String) $i] = 0;
        }

        $lastDrinkTime = '--:--';
        $drankWater = 0;
        $maxDrink = 0;
        $targetDrink = 0;

        if (!empty($userData['maxDrink'])) {
            $maxDrink = $userData['maxDrink'];
        }
        if (!empty($userData['targetDrink'])) {
            $targetDrink = $userData['targetDrink'];
        }

        foreach($listDrinkHistory as $drinkHistory) {
            if ($drinkHistory->exists()) {
                $data = $drinkHistory->data();
                if (!empty($data['drank'])) {
                    $userDrankHistory[$drinkHistory->id()] = (int) $data['drank'];
                }
                if (!empty($data['maxDrink'])) {
                    if((int) $data['maxDrink'] > 0) {
                        $userMaxDrinkHistory[$drinkHistory->id()] = (int) $data['maxDrink'];
                    }
                    else {
                        $userMaxDrinkHistory[$drinkHistory->id()] = $targetDrink;
                    }
                }
                else {
                    $userMaxDrinkHistory[$drinkHistory->id()] = $targetDrink;
                }

                if ((int) $drinkHistory->id() === $date) {
                    if (!empty($drinkHistory['lastDrink'])) {
                        $lastDrinkTime = $drinkHistory['lastDrink'];
                    }
                    if (!empty($drinkHistory['drank'])) {
                        $drankWater = (int) $drinkHistory['drank'];
                    }
                }
            }
        }

        $percentage =  (int) ($drankWater * 100 / $targetDrink);
        if($maxDrink != 0 && $maxDrink < $targetDrink) {
            $percentage = (int) ($drankWater * 100 / $maxDrink);
        }

        // User Container
        $containerQuery = $this->db->collection('container')->where('userID', '=', $uid);
        $containerDocs = $containerQuery->documents();

        $containerList = [];
        foreach ($containerDocs as $containerData) {
            if ($containerData->exists()) {
                $containerList[$containerData->id()] = $containerData->data();
            }
        }

        $month = (string) date('F');

        return view('user.dashboard', [
            'userData' => $userData,
            'drankWater' => $drankWater,
            'percentage' => $percentage,
            'containerList' => $containerList,
            'maxDrink' => $maxDrink,
            'targetDrink' => $targetDrink,
            'lastDrinkTime' => $lastDrinkTime,
            'userDrankHistory' => $userDrankHistory,
            'userMaxDrinkHistory' => $userMaxDrinkHistory,
            'month' => $month,
            'year' => $year,
        ]);
    }

    function getDatesForMonth($year, $month) {
        $dates = [];
        $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($day = 1; $day <= $numDays; $day++) {
            $dates[] = $day;
        }
        return $dates;
    }
}
