<?php

namespace App\Http\Controllers;

use App\Model\UserModel;
use App\Service\CaptchaService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function login()
    {
        $pro = array(
            'call' => 'required|regex:/^1[34578][0-9]{9}$/',
            'passwd' => 'required|digits_between:6,16',
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        $acount = $p['call'];

        $user_info = UserModel::where('call', $acount)->first();
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
            'call' => 'required|regex:/^1[34578][0-9]{9}$/',
            'passwd' => 'required|digits_between:6,16',
            'captcha' => 'required'
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        $user_info = UserModel::where('call', $p['call'])->first();
        if ($user_info) {
            return resp_err(1002);
        }
        $check_captcha = CaptchaService::check_captcha($p['call'], 'RG_', $p['captcha']);

        if ($check_captcha !== 1) {
            return $check_captcha;
        }

        $solt = get_rand_char(4);
        $user_model = new UserModel();
        $user_model->call = $p['call'];
        $user_model->passwd = md5($p['passwd'] . $solt);
        $user_model->solt = $solt;
        $user_model->save();

        return $this->login();
    }

    public function register_captcha()
    {
        $pro = array(
            'call' => 'required|regex:/^1[34578][0-9]{9}$/',
            'passwd' => 'required|digits_between:6,16',
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        $status = CaptchaService::make_captcha($p['call'], 'RG_');//RG  注册时的验证码
        if ($status !== 1){
            return $status;
        }

        return resp_suc('短信已成功发送至'.$p['call']);
    }

}
