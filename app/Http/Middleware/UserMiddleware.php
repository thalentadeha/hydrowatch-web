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

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

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
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $idToken = session('idToken');
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            // $userDoc = $this->firestore->collection('users')->document($uid)->snapshot();
            $snapshot = $this->database->getReference('users')->getChild($uid)->getSnapshot();
            $userDoc = $snapshot->getValue();

            if ($userDoc['isAdmin'] === 'admin') {
                return redirect()->route('admin-dashboard')->withErrors(['error' => 'Unauthorized']);
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('login_GET')->withErrors(['error' => 'Unauthorized']);
        }
    }
}
