<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    protected $auth;
    protected $db;

    public function __construct(Firestore $firestore, Auth $auth)
    {
        $this->auth = $auth;
        $this->db = $firestore->database();
    }

    public function showLogin()
    {
        $idToken = session('idToken');

        if ($idToken !== null) {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

            if ($userData['role'] === 'admin') {
                return redirect()->route('admin-dashboard-pass-token');
            } elseif ($userData['role'] === 'user') {
                return redirect()->route('user-dashboard-pass-token');
            }
            } catch (FailedToVerifyToken $e) {
                return view('auth.login')->with('error', $request->input('error'));
            }
        }

        if ($request->has('error')) {
            return view('auth.login')->with('error', $request->input('error'));
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

            $leewayInSeconds = 360; // 5 minutes

            $verifiedIdToken = $this->auth->verifyIdToken($idToken, $leewayInSeconds);
            $uid = $verifiedIdToken->claims()->get('sub');
            $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

            if ($userData['role'] === 'admin') {
                return redirect()->route('admin-dashboard-pass-token');
            } else {
                return redirect()->route('user-dashboard-pass-token');;
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
