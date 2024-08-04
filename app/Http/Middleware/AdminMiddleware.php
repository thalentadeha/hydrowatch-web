<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Factory;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

            if ($userData['role'] === 'admin') {
                return $next($request);
            }

            $request->session()->forget('idToken');

            return redirect()->route('login_GET')->withErrors(['error' => 'Unauthorized, Please re-Login']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('login_GET')->withErrors(['error' => 'Unauthorized']);
        }
    }
}
