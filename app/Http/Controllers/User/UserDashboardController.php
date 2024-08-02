<?php

namespace App\Http\Controllers\User;

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
    protected $database;

    public function __construct(Firestore $firestore, Auth $auth, Database $database)
    {
        $this->auth = $auth;
        // $this->db = $firestore->database();
        $this->database = $database;

        $firestore = app('firebase.firestore');
        $this->db = $firestore->database();
    }
    public function passToken(Request $request)
    {
        $idToken = session('idToken');

        return redirect()->route('user-dashboard', [
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
        // $userDoc = $this->firestore->collection('users')->document($uid)->snapshot();
        $snapshot = $this->database->getReference('users')->getChild($uid)->getSnapshot();
        $userDoc = $snapshot->getValue();

        if(empty($userDoc['drankWater'])){
            $drankWater = 0;
        }else{
            $drankWater = $userDoc['drankWater'];
        }
        $targetDrink = 2500;

        $percentage = ($drankWater/$targetDrink) * 100;

        return view('user.dashboard', [
            'userDoc' => $userDoc,
            'drankWater' => $drankWater,
            'percentage' => $percentage,
        ]);
    }
}
