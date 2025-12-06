<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Http\Request;
use Closure;

class ThrottleWithSession extends ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        try {
            return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
        } catch (\Illuminate\Http\Exceptions\ThrottleRequestsException $e) {
            $errorMessage = "<b>Limit Reached</b>. You can only have <b>$maxAttempts</b> attempt(s) in <b>$decayMinutes</b> minute(s)";
            return redirect()->back()->withErrors(['username' => $errorMessage]);
        }
    }
}
