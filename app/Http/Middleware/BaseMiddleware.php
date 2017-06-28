<?php

namespace App\Http\Middleware;

use Closure;

class BaseMiddleware
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
        $result = $this->match();

        if ($result === 0){
            return $next($request);
        }else{
            return $this->noMatch($result);
        }

    }
    public function match(){

    }
    public function noMatch($result){
        return $result;
    }
}
