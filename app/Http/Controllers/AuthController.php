<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Contract\Auth;

class AuthController extends Controller
{
    protected $auth;
    protected $db;

    public function __construct(Firestore $firestore, Auth $auth)
    {
        $this->auth = $auth;
        $this->db = $firestore->database();
    }

    public function getUID($idToken)
    {
        $verifiedIdToken = $this->auth->verifyIdToken($idToken, 360);
        $uid = $verifiedIdToken->claims()->get('sub');

        return $uid;
    }

    public function showLogin()
    {
        $idToken = session('idToken');

        if ($idToken !== null) {
            $uid = $this->getUID($idToken);
            $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

            if ($userData['role'] === 'admin') {
                return redirect()->route('admin-dashboard', [
                    'idToken' => $idToken
                ]);
            } elseif ($userData['role'] === 'user') {
                return redirect()->route('user-dashboard', [
                    'idToken' => $idToken
                ]);
            }
        }


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

            $uid = $this->getUID($idToken);
            $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

            if ($userData['role'] === 'admin') {
                return redirect()->route('admin-dashboard', [
                    'idToken' => $idToken
                ]);
            } else {
                return redirect()->route('user-dashboard', [
                    'idToken' => $idToken
                ]);;
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

    public function logout(Request $request)
    {
        if (session() != null) {
            $request->session()->forget('idToken');
        }

        return redirect()->route('login_GET');
    }
}
