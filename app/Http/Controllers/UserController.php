<?php

namespace App\Http\Controllers;

use App\Model\UserModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    /**
     * 用户登录
     */
    public function login()
    {
        $pro = array(
            'account' => 'required|regex:/^1[34578][0-9]{9}$/',
            'passwd' => 'required|digits_between:6,16',
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        $acount = $p['account'];

        $user_info = UserModel::where('account', $acount)->first();
        if (!$user_info) {
            return resp_err(1001);
        }
        $passwd = md5($p['passwd'] . $user_info->solt);
        if ($passwd != $user_info['passwd']) {
            return resp_err(1001);
        }

        $token = get_rand_char(16);
        $redis = Redis::connection();

        $redis->set('TK_' . $user_info->id, $token);

        $return_data['user_id'] = $user_info->id;
        $return_data['token'] = $token;
        return resp_suc($return_data);

    }

    public function register()
    {
        $pro = array(
            'account' => 'required|regex:/^1[34578][0-9]{9}$/',
            'passwd' => 'required|digits_between:6,16',
            'captcha' => 'required'
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        $user_info = UserModel::where('account', $p['account'])->first();
        if ($user_info) {
            return resp_err(1002);
        }
        $captcha = '0000';//@todo 验证码需要重写

        if ($p['captcha'] != $captcha) {
            return resp_err(2001);
        }

        $solt = get_rand_char(4);
        $user_model = new UserModel();
        $user_model->account = $p['account'];
        $user_model->passwd = md5($p['passwd'] . $solt);
        $user_model->solt = $solt;
        $user_model->save();

        return $this->login();
    }

}
