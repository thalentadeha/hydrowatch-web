<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;

class AuthController extends Controller
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

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
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
        ]);

        try {
            $signInResult = $this->auth->signInWithEmailAndPassword($validated['email'], $validated['password']);
            $idToken = $signInResult->idToken();
            session(['idToken' => $idToken]);

            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            // $userDoc = $this->db->collection('users')->document($uid)->snapshot();
            $snapshot = $this->database->getReference('users')->getChild($uid)->getSnapshot();
            $userDoc = $snapshot->getValue();

            if ($userDoc['isAdmin']) {
                return redirect()->route('admin-dashboard');
            } else {
                return redirect()->route('welcome');
            }
        } catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
            return back()->withErrors(['password' => 'Invalid email or password.']);
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            return back()->withErrors(['email' => 'User not found.']);
        } catch (\Kreait\Firebase\Exception\Auth\RevokedIdToken $e) {
            return back()->withErrors(['email' => 'Your session has expired. Please log in again.']);
        } catch (\Kreait\Firebase\Auth\SignIn\FailedToSignIn $e) {
            return back()->withErrors(['error' => 'Invalid credentials']);
        }
    }

    public function logout()
    {
        if (session() != null) {
            session()->forget('idToken');
        }

        return redirect()->route('login_GET');
    }
}
