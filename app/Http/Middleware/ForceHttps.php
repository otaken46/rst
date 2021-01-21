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
        Log::debug('message00');
        if(array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER)){
            if (App::environment(['production']) && $_SERVER["HTTP_X_FORWARDED_PROTO"] != 'https') {
                Log::debug('message01');
                return redirect('/');
            }
            Log::debug('message02');
            return $next($request);
        }else{
            if (App::environment(['local'])){
                Log::debug('message03');
                return $next($request);
            }else{
                Log::debug('message04');
                return redirect('/');
            }
        }
        
    }
}
