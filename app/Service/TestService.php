<?php
namespace App\Service;
use App\Model\testModel;
use Illuminate\Support\Facades\Redis;

/**
 * Created by PhpStorm.
 * User: win7
 * Date: 2017/6/22
 * Time: 16:52
 */
Class TestService
{
    public static function test(){
//        $z = testModel::get();
        $redis = Redis::connection();
        $z = $redis->get('a');
        return $z;
    }
}