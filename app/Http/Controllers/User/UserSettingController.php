<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        $targetDrink = 0;
        if (!empty($userData['targetDrink'])) {
            $targetDrink = $userData['targetDrink'];
        }

        $notificationDay = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        $notificationTimeIn = [];
        $notificationTimeOut = [];
        for($i = 0; $i < 7; $i++) {
            $notificationTimeIn[$notificationDay[$i]] = "OFF";
            $notificationTimeOut[$notificationDay[$i]] = "OFF";
        }

        $notificationTimeList = $this->db->collection('user')->document($uid)->collection('schedule')->documents();
        foreach ($notificationTimeList as $notificationTime) {
            $notificationTimeIn[$notificationDay[$notificationTime->id()]] = $notificationTime['timeIn'];
            $notificationTimeOut[$notificationDay[$notificationTime->id()]] = $notificationTime['timeOut'];
        }


        return view('user.setting', [
            'userData' => $userData,
            'email' => $email,
            'maxDrink' => $maxDrink,
            'targetDrink' => $targetDrink,
            'notificationTimeIn' => $notificationTimeIn,
            'notificationTimeOut' => $notificationTimeOut,
            'isNotificationEnabled' => $isNotificationEnabled
        ]);
    }

    public function changeNickname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => [
                'string',
                'min:2',
                'max:20',
            ]
        ], [
            'nickname.min' => 'Nickname need more than 2 characters.',
            'nickname.max' => 'Nickname exceeded maximum characters (20).',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        try {
            $validated = $validator->validate();

            $userDoc = $this->db->collection('user')->document($uid);
            $userDoc->update([
                ['path' => 'nickname', 'value' => $validated['nickname']]
            ]);

            return response()->json(['success' => 'Change nickname successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }

    public function setMaxDrink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'maxDrink' => [
                'numeric',
                function ($attribute, $value, $fail) {
                    if ((int) $value !== 0 && ((int) $value < 100 || (int) $value > 6000)) {
                        $fail('Max Drink should be either 0 or between 100 and 6000.');
                    }
                }
            ]
        ], [
            'maxDrink.numeric' => 'The :attribute must be a number.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        try {
            $validated = $validator->validate();

            //save in user document
            $userDoc = $this->db->collection('user')->document($uid);
            $userDoc->update([
                ['path' => 'maxDrink', 'value' => (int) $validated['maxDrink']]
            ]);

            //save in drinkHistory document
            $year = (int) date('Y');
            $month = (int) date('n');
            $date = (int) date('d');
            $drinkHistoryDoc = $this->db->collection('user')->document($uid)->collection('drinkHistory')->document($year)->collection($month)->document($date);
            $drinkHistoryDoc->set([
                'maxDrink' => (int) $validated['maxDrink']
            ], ['merge' => true]);

            return response()->json(['success' => 'Set max drink successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }

    public function setTargetDrink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'targetDrink' => [
                'numeric',
                function ($attribute, $value, $fail) {
                    if ((int) $value < 1000 || (int) $value > 6000) {
                        $fail('Target Drink should be between 1000 and 6000.');
                    }
                }
            ]
        ], [
            'targetDrink.numeric' => 'The :attribute must be a number.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        try {
            $validated = $validator->validate();

            //save in user document
            $userDoc = $this->db->collection('user')->document($uid);
            $userDoc->update([
                ['path' => 'targetDrink', 'value' => (int) $validated['targetDrink']]
            ]);

            return response()->json(['success' => 'Set target drink successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }

    public function saveSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day' => ['required', 'string'],
            'in' => ['required', 'string'],
            'out' => ['required', 'string'],
        ], [
            'day.required' => 'day should not be empty.',
            'in.required' => 'Time in should not be empty.',
            'out.required' => 'Time out should not be empty.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);
        try {
            $validated = $validator->validate();

            if($validated['in'] === "OFF") {
                $this->db->collection('user')->document($uid)->collection('schedule')->document($validated['day'])->delete();
            }
            else if($validated["in"] >= $validated["out"]) {
                return response()->json(['errors' => 'Time in must be earlier than Time out'], 422);
            }
            else {
                $scheduleData = [
                    'timeIn' => $validated["in"],
                    'timeOut' => $validated["out"],
                ];
                $this->db->collection('user')->document($uid)->collection('schedule')->document($validated['day'])->set($scheduleData);
            }
            return response()->json(['success' => 'Change schedule successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }

    public function updateNotificationStatus(Request $request)
    {
        $idToken = session('idToken');
        $uid = $this->authController->getUID($idToken);

        $isNotificationEnabled = (bool) $request->input('isNotificationEnabled');
        $userDoc = $this->db->collection('user')->document($uid);

        try {
            $userDoc->update([
                ['path' => 'isNotificationEnabled', 'value' => $isNotificationEnabled]
            ]);

            if ($isNotificationEnabled) {
                return response()->json(['success' => 'Notification enabled.']);
            } else {
                return response()->json(['success' => 'Notification disabled.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
