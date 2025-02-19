<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    public function handle(Request $request, Closure $next): Response
    {
        $idToken = session('idToken');
        $idTokenURL = $request->input('idToken');

        if (!$idToken) {
            return response()->view('auth.sessionExpired');
        }

        if (!$idTokenURL) {

            $request->session()->forget('idToken');
            Log::debug("id token is not in url");
            return redirect()->route('login_GET')->withErrors(['error' => 'You need to log in.']);
        }

        return $next($request);
    }
}
