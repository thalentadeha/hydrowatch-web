<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;

class AdminDashboardController extends Controller
{
    protected $auth;
    protected $db;
    protected $database;

    public function __construct(Firestore $firestore, Auth $auth)
    {
        $this->auth = $auth;
        $this->db = $firestore->database();
    }
    public function passToken(Request $request)
    {
        $idToken = session('idToken');

        return redirect()->route('admin-dashboard', [
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

        $userDocs = $this->db->collection('user')->documents();

        foreach($userDocs as $user){
            if($user->exists()){
                $users[$user->id()] = $user->data();
            }
        }


        $listUsersAuth = $this->auth->listUsers();
        $email = [];
        foreach ($listUsersAuth as $userAuth) {
            $email[$userAuth->uid] = [
                'email' => $userAuth->email,
            ];
        }

        return view('admin.dashboard', [
            'users' => $users,
            'email' => $email,
        ]);
    }
}
