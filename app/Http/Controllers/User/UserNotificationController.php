<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;

class UserNotificationController extends Controller
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

            if($isNotificationEnabled){
                return response()->json(['success' => 'Notification enabled.']);
            } else {
                return response()->json(['success' => 'Notification disabled.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => $e->getMessage()]);
        }
    }
}
