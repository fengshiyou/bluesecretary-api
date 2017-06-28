<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckLoginMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function match()
    {

        $user_id = request()->input('user_id');
        $token = request()->header('token');
        if (!$token){
            return resp_err(1,'无token传入');
        }

        if (!$user_id){
            return resp_err(1,'无user_id');
        }

        $redis = Redis::connection();
        $redis_token = $redis->get("TK_".$user_id);

        if ($redis_token == $token){
            return 0;
        }else{
            return resp_err(1,'token错误');
        }
    }

}
