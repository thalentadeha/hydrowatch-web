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

    public function __construct(Firestore $firestore, Auth $auth)
    {
        $this->auth = $auth;
        $this->db = $firestore->database();
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
            'role' => 'required|string'
        ]);

        $user = $this->auth->createUserWithEmailAndPassword($validated['email'], $validated['password']);

        try {
            $userData = [
                'fullname' => $validated['fullname'],
                'nickname' => $validated['nickname'],
                'role' => $validated['role'],
            ];

            $this->db->collection('user')->document($user->uid)->set($userData);

            return back()->with('success', 'User registered successfully!');
        } catch (\Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }
    public function deleteUser(Request $request)
    {
        $email = $request->input('email');

        try {
            $user = $this->auth->getUserByEmail($email);

            $this->db->collection('user')->document($user->uid)->delete();
            $this->auth->deleteUser($user->uid);

            return back()->with('status', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'User deletion failed']);
        }
    }
}
