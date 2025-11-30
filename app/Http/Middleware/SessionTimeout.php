<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $loginTime = session('login_time');
            $lifetime  = session('session_lifetime');

            if (!$loginTime || !$lifetime) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/login')->with('msgError', '<strong>Session</strong> expired.');
            }

            $expiryTime = $loginTime->copy()->addMinutes($lifetime);
            
            if (now()->greaterThanOrEqualTo($expiryTime)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('msgError', '<strong>Session</strong> expired.');
            }
        }

        return $next($request);
    }
}
