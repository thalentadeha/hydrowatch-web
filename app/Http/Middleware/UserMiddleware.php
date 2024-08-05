<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AuthController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Factory;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    protected $auth;
    protected $db;

    public function __construct(Firestore $firestore, Auth $auth)
    {
        $this->auth = $auth;
        $this->db = $firestore->database();
    }
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $idToken = session('idToken');

            $verifiedIdToken = $this->auth->verifyIdToken($idToken, 360);
            $uid = $verifiedIdToken->claims()->get('sub');
            $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

            if ($userData['role'] === 'user') {
                return $next($request);
            }

            $this->logout($request);

            return redirect()->route('login_GET')->withErrors(['error' => 'Unauthorized, Please re-Login']);
        } catch (AuthException $e) {
            $this->logout($request);
            return response()->view('auth.sessionExpired');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->logout($request);
            return redirect()->route('login_GET')->withErrors(['error' => 'Unauthorized']);
        }
    }

    public function logout($request)
    {
        $request->session()->forget('idToken');
    }
}
