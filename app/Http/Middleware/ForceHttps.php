<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::debug('message333');
        if(array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER)){
            Log::debug('message000');
            if (App::environment(['production']) && $_SERVER["HTTP_X_FORWARDED_PROTO"] != 'https') {
                Log::debug('message111');
                return redirect()->secure($request->getRequestUri());
            }
        }
        Log::debug('message222');
        return $next($request);
    }
}
