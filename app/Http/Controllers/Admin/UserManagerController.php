<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserManagerController extends Controller
{
    protected $auth;
    protected $db;
    protected $database;

    public function __construct(Firestore $firestore, Auth $auth, Database $database)
    {
        $this->auth = $auth;
        // $this->db = $firestore->database();
        $this->database = $database;

        // $firestore = app('firebase.firestore');
        // $this->db = $firestore->database();
    }
    public function showRegister()
    {
        // return view('auth.register');
        return view('admin.register_temp');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string',
            'nickname' => 'required|string|max:20',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',      //atleast 1 uppercase
                'regex:/[0-9]/',      //atleast 1 number
                'regex:/[@$!%*?&]/', //atleast 1 symbol
            ],
            'userType' => 'required|string'
        ]);

        // $usersCollection = $this->firestore->collection('users');
        // $isCollectionEmpty = $usersCollection->limit(1)->documents()->isEmpty();

        // if (!$isCollectionEmpty) {
        //     $isEmailEmpty = $usersCollection->where('email', '=', $validated['email'])->documents()->isEmpty();
        //     $isempIdEmpty = $usersCollection->where('emp_id', '=', $validated['emp_id'])->documents()->isEmpty();

        //     if (!$isEmailEmpty || !$isempIdEmpty) {
        //         return back()->withErrors(['email' => 'Email or Employee ID already exists']);
        //     }
        // }

        $user = $this->auth->createUserWithEmailAndPassword($validated['email'], $validated['password']);

        try {
            $userData = [
                'fullname' => $validated['fullname'],
                'nickname' => $validated['nickname'],
                'userType' => $validated['userType'],
                'container_id' =>  null,
                'water_id' => null,
                'username' => null,
                'profilepic' => null
            ];

            // $this->db->collection('users')->document($user->uid)->set($userData);

            $this->database->getReference('users')->getChild($user->uid)->set($userData);

            return redirect()->route('admin-dashboard-pass-token')->with('success', 'User registered successfully!');
        } catch (\Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }
    public function deleteUser(Request $request)
    {
        $email = $request->input('email');

        try {
            $user = $this->auth->getUserByEmail($email);

            // $this->db->collection('users')->document($user->uid)->delete();
            $this->database->getReference('users')->getChild($user->uid)->remove();
            $this->auth->deleteUser($user->uid);

            return redirect()->route('admin-dashboard-pass-token')->with('status', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'User deletion failed']);
        }
    }
}
