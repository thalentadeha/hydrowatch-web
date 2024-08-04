<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserContainerController extends Controller
{
    protected $auth;
    protected $db;

    public function __construct(Firestore $firestore, Auth $auth)
    {
        $this->auth = $auth;
        $this->db = $firestore->database();
    }
    public function passToken(Request $request)
    {
        $idToken = session('idToken');

        return redirect()->route('user-container', [
            'idToken' => $idToken
        ]);
    }

    public function index(Request $request)
    {
        $idToken = session('idToken');

        if (!$request->has('idToken')) {
            $request->session()->forget('idToken');

            return redirect()->route('login_GET')->withErrors(['error' => 'No session found. Please login first']);
        }

        $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        $uid = $verifiedIdToken->claims()->get('sub');
        $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

        $drankWater = 0;
        if(!empty($userData['drankWater'])){
            $drankWater = $userData['drankWater'];
        }

        $targetDrink = 2500;

        $percentage = ($drankWater/$targetDrink) * 100;

        return view('user.container', [
            'userData' => $userData,
            'drankWater' => $drankWater,
            'percentage' => $percentage
        ]);
    }
}
