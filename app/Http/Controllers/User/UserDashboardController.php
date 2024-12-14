<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserDashboardController extends Controller
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

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $userDrankHistory[(string) $i] = 0;
            $userMaxDrinkHistory[(string) $i] = 0;
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

        foreach ($listDrinkHistory as $drinkHistory) {
            if ($drinkHistory->exists()) {
                $data = $drinkHistory->data();
                if (!empty($data['drank'])) {
                    $userDrankHistory[$drinkHistory->id()] = (int) $data['drank'];
                }
                if (!empty($data['maxDrink'])) {
                    if ((int) $data['maxDrink'] > 0) {
                        $userMaxDrinkHistory[$drinkHistory->id()] = (int) $data['maxDrink'];
                    } else {
                        $userMaxDrinkHistory[$drinkHistory->id()] = $targetDrink;
                    }
                } else {
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
        if ($maxDrink != 0 && $maxDrink < $targetDrink) {
            $percentage = (int) ($drankWater * 100 / $maxDrink);
        }
        $percentage = $percentage > 100 ? 100 : $percentage;

        // User Container
        $containerQuery = $this->db->collection('container')->where('userID', '=', $uid);
        $containerDocs = $containerQuery->documents();

        $containerList = [];
        foreach ($containerDocs as $containerData) {
            if ($containerData->exists()) {
                $containerList[$containerData->id()] = $containerData->data();
            }
        }

        $monthName = $this->getMonthName($month);

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
            'monthName' => $monthName,
            'year' => $year,
        ]);
    }

    public function updateMonth(Request $request)
    {
        $idToken = session('idToken');
        // Retrieve user UID and Firestore document
        $uid = $this->authController->getUID($idToken);
        $userDoc = $this->db->collection('user')->document($uid);
        $userData = $userDoc->snapshot();

        $action = $request->input('action');
        $year = $request->input('year');
        $month = $request->input('month');
        $date = (int) date('d');
        $daysInMonth = (int) date('t');
        // $currentMonth = session('currentMonth', now()->month);
        // $currentYear = session('currentYear', now()->year);

        // Adjust month and year based on action
        if ($action === 'prev') {
            $month--;
            if ($month < 1) {
                $month = 12;
                $year--;
            }
        } elseif ($action === 'next') {
            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
        }

        // Store updated month and year in session
        // session(['currentMonth' => $month, 'currentYear' => $year]);

        // Fetch data for the new month
        $listDrinkHistory = $userDoc->collection('drinkHistory')
            ->document($year)
            ->collection($month)
            ->documents();

        $userDrankHistory = [];
        $userMaxDrinkHistory = [];
        $targetDrink = 0;

        if (!empty($userData['targetDrink'])) {
            $targetDrink = $userData['targetDrink'];
        }

        foreach ($listDrinkHistory as $drinkHistory) {
            if ($drinkHistory->exists()) {
                $data = $drinkHistory->data();
                if (!empty($data['drank'])) {
                    $userDrankHistory[$drinkHistory->id()] = (int) $data['drank'];
                }
                if (!empty($data['maxDrink'])) {
                    if ((int) $data['maxDrink'] > 0) {
                        $userMaxDrinkHistory[$drinkHistory->id()] = (int) $data['maxDrink'];
                    } else {
                        $userMaxDrinkHistory[$drinkHistory->id()] = $targetDrink;
                    }
                } else {
                    $userMaxDrinkHistory[$drinkHistory->id()] = $targetDrink;
                }
            }
        }

        $monthName = $this->getMonthName($month);

        return response()->json([
            'success' => true,
            'month' => $month,
            'monthName' => $monthName,
            'year' => $year,
            'userDrankHistory' => $userDrankHistory,
            'userMaxDrinkHistory' => $userMaxDrinkHistory,
        ]);
    }

    function getMonthName($monthNumber) {
        $date = DateTime::createFromFormat('!m', $monthNumber);
        return $date ? $date->format('F') : null;
    }

    function getDatesForMonth($year, $month)
    {
        $dates = [];
        $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($day = 1; $day <= $numDays; $day++) {
            $dates[] = $day;
        }
        return $dates;
    }
}
