<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;

class AdminDashboardController extends Controller
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

        $userDocs = $this->db->collection('user')->where('role', '=', 'user')->documents();
        $drinkHistories = [];

        $year = (int) date('Y');
        $month = (int) date('n');
        $date = (int) date('d');

        foreach ($userDocs as $user) {
            if ($user->exists()) {
                $users[$user->id()] = $user->data();
                $drinkHistory = $user->reference()
                                    ->collection('drinkHistory')
                                    ->document($year)
                                    ->collection($month)
                                    ->document($date)
                                    ->snapshot()
                                    ->data();
                $drinkHistories[$user->id()] = isset($drinkHistory['drank']) ? $drinkHistory['drank'] : 0;
            }
        }

        uasort($users, function ($a, $b) {
            return strcmp($a['fullname'], ($b['fullname']));
        });

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
            'drinkHistories' => $drinkHistories,
        ]);
    }
}
