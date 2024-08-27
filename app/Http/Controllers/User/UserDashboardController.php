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

        $maxDrink = 0;
        if (!empty($userData['maxDrink'])) {
            $maxDrink = $userData['maxDrink'];
        }

        //save in drinkHistory document
        $drinkHistoryDoc = $this->db->collection('user')->document($uid)->collection('drinkHistory')->document($year)->collection($month)->document($date);
        $drinkHistoryDoc->set([
            'maxDrink' => $maxDrink
        ], ['merge' => true]);

        $percentage = ($drankWater / $maxDrink) * 100;

        $dates = $userDoc->collection('drinkHistory')->document($year)->collection($month)->documents();
        $thisMonthDates = $this->getDatesForMonth($year, $month);

        $allDrankData = [];
        $allMaxDrinkData = [];
        $datesDrank = [];
        foreach ($thisMonthDates as $date) {
            $dateExists = false;
            foreach ($dates as $dateDoc) {
                if ($dateDoc->exists() && $dateDoc->id() == $date) {
                    $dateExists = true;
                    $data = $dateDoc->data();
                    $datesDrank[] = $date;

                    $allDrankData[] = isset($data['drank']) ? $data['drank'] : "0";
                    $allMaxDrinkData[] = isset($data['maxDrink']) ? $data['maxDrink'] : "0";

                    break;
                }
            }

            // set 0 when date is not in db
            if (!$dateExists) {
                $datesDrank[] = $date;
                $allDrankData[] = "0";
                $allMaxDrinkData[] = "0";
            }
        }

        // foreach ($years as $yearDoc) {
        //     if ($yearDoc->exists()) {
        //         $months = $userDoc->collection('drinkHistory')
        //             ->document($yearDoc->id())
        //             ->collections();

        //         foreach ($months as $monthDoc) {

        //             $dates = $userDoc->collection('drinkHistory')
        //                 ->document($yearDoc->id())
        //                 ->collection($monthDoc->id())
        //                 ->documents();

        //             foreach ($dates as $dateDoc) {
        //                 if ($dateDoc->exists()) {
        //                     $data = $dateDoc->data();
        //                     $dates[] = $dateDoc->id();

        //                     if (isset($data['drank'])) {
        //                         $allDrankData[] = $data['drank'];
        //                     }

        //                     if (isset($data['maxDrink'])) {
        //                         $allMaxDrinkData[] = $data['maxDrink'];
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        $month = (string) date('F');

        return view('user.dashboard', [
            'userData' => $userData,
            'drankWater' => $drankWater,
            'percentage' => $percentage,
            'containerList' => $containerList,
            'maxDrink' => $maxDrink,
            'lastDrinkTime' => $lastDrinkTime,
            'allDrankData' => $allDrankData,
            'allMaxDrinkData' => $allMaxDrinkData,
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
