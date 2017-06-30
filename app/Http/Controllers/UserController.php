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
    /**
     * @api {post} /login
     * @apiDescription 用户登录
     * @apiGroup 01-user
     * @apiName login
     *
     *
     * @apiParam {String} call 注册的手机号
     * @apiParam {String} passwd 用户密码
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
    {
    "code": 1001,
    "detail": "账号或密码错误",
    "data": ""
    }
     * @apiSuccessExample {json} 正确返回值:
    {
    "code": 200,
    "detail": "success",
    "data": {
    "user_id": "6",
    "token": "4MRhjarXvynrtkmS"
    }
    }
     */
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
    /**
     * @api {post} /register
     * @apiDescription 用户注册
     * @apiGroup 01-user
     * @apiName register
     *
     *
     * @apiParam {String} call 注册的手机号
     * @apiParam {String} passwd 用户密码
     * @apiParam {int} captcha 验证码
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
    {
    "code": 1002,
    "detail": "该手机号已经注册",
    "data": ""
    }
     * @apiSuccessExample {json} 正确返回值:
    {
    "code": 200,
    "detail": "success",
    "data": {
    "user_id": "7", #用户ID
    "token": "5oYbL2F1mDKycPQu", #token凭证
    "user_info":""  #不知道具体业务  暂时没数据
    }
    }
     */
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
    /**
     * @api {post} /register/captcha
     * @apiDescription 注册验证码
     * @apiGroup 01-user
     * @apiName register/captcha
     *
     *
     * @apiParam {String} call 注册的手机号
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
    {
    "code": 1002,
    "detail": "该手机号已经注册",
    "data": ""
    }
     * @apiSuccessExample {json} 正确返回值:
    {
    "code": 200,
    "detail": "success",
    "data": "短信已成功发送至15285588389"
    }
     */
    public function register_captcha()
    {
        $pro = array(
            'call' => 'required|regex:/^1[34578][0-9]{9}$/',
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        $user_info = UserModel::where('call', $p['call'])->first();
        if ($user_info) {
            return resp_err(1002);
        }
        $status = CaptchaService::make_captcha($p['call'], 'RG_');//RG  注册时的验证码
        if ($status !== 1){
            return $status;
        }

        return resp_suc('短信已成功发送至'.$p['call']);
    }

}
