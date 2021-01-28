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
        if(array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER)){
            if (App::environment(['production']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] != 'https') {
                return redirect()->secure($request->getRequestUri());
            }
            return $next($request);
        }else{
            if (App::environment(['local'])){
                return $next($request);
            }else{
                return redirect()->secure($request->getRequestUri());
            }
        }
       
    }
}
