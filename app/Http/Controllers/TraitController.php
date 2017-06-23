<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;

/**
 * Created by PhpStorm.
 * User: win7
 * Date: 2017/6/23
 * Time: 16:07
 */
trait TraitController
{
    public function app_validata($validators, &$error = '', &$params = [], $messages = [])
    {
        $header = [];
        $conf = config('config.headers');//约定的信息头
        foreach ($conf as $k => $v) {
            $header[$v] = request()->header($v);
        }

        $inputs = array_merge(request()->all(), $header);
        $validator = Validator::make($inputs, $validators, $messages);
        $flag = $validator->fails();
        if ($flag){
            $error = $validator->errors()->first();
        }else{
            $params = $inputs;
        }

        return $flag;
    }
}