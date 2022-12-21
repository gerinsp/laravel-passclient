<?php

namespace App\Http\Middleware;

use Closure;

class RefreshToken
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
        if(!$request->user() || !$request->user()->token){
            return $next($request);
        }

        if($request->user()->token->hasExpired()){
            return redirect('/auth/passport/refresh');
        }
        
        return $next($request);
    }
}
