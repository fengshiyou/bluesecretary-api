<?php
namespace App\Service;

use App\Events\CaptchaEvent;
use Event;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Support\Facades\Redis;

/**
 * Created by PhpStorm.
 * User: win7
 * Date: 2017/6/22
 * Time: 16:52
 */
Class CaptchaService
{
    //发送验证码
    public static function send_captcha($call, $captcha)
    {
        Event::setQueueResolver(function () {
            return Queue::connection('captcha');
        })->fire(new CaptchaEvent($call, $captcha));
    }

    //生成验证码
    public static function make_captcha($call, $captcha_type)
    {
        $captcha = random_int(10000, 99999);
        $redis = Redis::connection('captcha');
        //redis递增计数----计数某个手机已经申请验证码发送次数，24小时内限制已经发送次数
        $count = $redis->incrby('CAPTCHA_COUNT_' . $call . '_' . date('Y-m-d', time()), 1);
        //第一次记录需要给定有效期---1天
        if ($count == 1) {
            $redis->expire('CAPTCHA_COUNT_' . $call . '_' . date('Y-m-d', time()), 24 * 60 * 60);
        }
        //测试环境可以随意申请
        if (env('APP_ENV') == 'PRO' && intval($count) > 10) {//每日最大的验证码请求次数
            return resp_err('2002', '超过每日请求上限');
        }
        //redis判断一个手机号60秒内只能申请一次验证码
        $expire = $redis->ttl($captcha_type . $call);

        if (intval($expire) > 29 * 60) {
            return resp_err('2003', '验证码发送时间间隔不能小于60秒');
        }

        $redis->set($captcha_type . $call, $captcha);
        $redis->expire($captcha_type . $call, 30 * 60);

        self::send_captcha($call, $captcha);
        return 1;
    }

    //删除验证码-----验证码被使用过后调用
    public static function check_captcha($call, $type, $captcha)
    {
        $redis = Redis::connection('captcha');
        $redis_captcha = $redis->get($type . $call);

        if ($captcha == $redis_captcha) {
            $redis->del($type . $call);
            return 1;
        } else {
            return resp_err(2001);
        }
    }
}