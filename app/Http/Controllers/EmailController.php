<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DrinkRemainderEmail;
use Carbon\Carbon;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class EmailController extends Controller
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

    private function getUserEmail($uid) {
        return $this->auth->getUser($uid)->email;
    }

    private function signIn() {
        try {
            $signInResult = $this->auth->signInWithEmailAndPassword("worker@hydrowatch.com", "0jCQT@IF51ya0");
            $idToken = $signInResult->idToken();
            session(['idToken' => $idToken]);
            return 'Success';
        } catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
            return 'Invalid email or password.';
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            return 'User not found.';
        } catch (\Kreait\Firebase\Exception\Auth\RevokedIdToken $e) {
            return 'Your session has expired. Please log in again.';
        } catch (\Kreait\Firebase\Auth\SignIn\FailedToSignIn $e) {
            return 'Invalid credentials';
        }
    }

    public function sendReminderEmail() {
        $email = env('MAIL_FROM_ADDRESS', null);
        $bcc = [];
        if($this->signIn() === 'Success') {
            $allUser = $this->db->collection('user')
                                ->where('role', '=', 'user')
                                ->where('isNotificationEnabled', '=', true)
                                ->documents();
            
            $year = (int) Carbon::now()->setTimezone('Asia/Jakarta')->year;
            $month = (int) Carbon::now()->setTimezone('Asia/Jakarta')->month;
            $date = (int) Carbon::now()->setTimezone('Asia/Jakarta')->day;
            $currentDay = ((int) Carbon::now()->setTimezone('Asia/Jakarta')->dayOfWeekIso) - 1;
    
            foreach($allUser as $user) {
                if ($user->exists()) {
                    $userData = $user->data();
                    $userSchedule = $user->reference()->collection("schedule")->document((String) $currentDay)->snapshot();
                    if(!empty($userSchedule['timeIn']) && !empty($userSchedule['timeOut'])) {
                        $currHour = (int) Carbon::now()->setTimezone('Asia/Jakarta')->format("H");
                        $currMinute = (int) Carbon::now()->setTimezone('Asia/Jakarta')->format("i");
                        $timeIn = (int) Carbon::parse($userSchedule["timeIn"])->format("H");
                        $timeOut = (int) Carbon::parse($userSchedule["timeOut"])->format("H");
    
                        if($timeIn <= $currHour && $currHour < $timeOut) {
                            $userDrinkHistory = $user->reference()->collection("drinkHistory")->document((String) $year)->collection((String) $month)->document((String) $date)->snapshot();
                            if(!empty($userDrinkHistory['drank']) && !empty($userDrinkHistory['lastDrink']) && !empty($userDrinkHistory['targetDrink']) && !empty($userDrinkHistory['maxDrink'])) {
                                $lastDrinkHour = (int) Carbon::parse($userDrinkHistory["lastDrink"])->format("H");
                                $lastDrinkMinute = (int) Carbon::parse($userDrinkHistory["lastDrink"])->format("i");
                                $drank = (int) $userDrinkHistory["drank"];
                                $lastDrink = ($lastDrinkHour * 60) + $lastDrinkMinute;
                                $currentTime = ($currHour * 60) + $currMinute;
                                $targetDrink = (int) $userData["targetDrink"];
                                if($userData["maxDrink"] !== 0) {
                                    $targetDrink = (int) $userData["maxDrink"];
                                }
                                $shouldDrink = (int) (($currentTime - ($timeIn * 60)) * ($targetDrink / 60 / ($timeOut - $timeIn)));
                                if(($currentTime - $lastDrink >= 60 && $targetDrink > $drank) || $shouldDrink > $drank) {
                                    $bcc[] = $this->getUserEmail($user->id());
                                }
                            }
                            else {
                                $bcc[] = $this->getUserEmail($user->id());
                            }
                        }
                    }
                }
            }
        }

        if(count($bcc) > 0) {
            $response = Mail::to($email)->bcc($bcc)->send(new DrinkRemainderEmail());
        }
    }
}
