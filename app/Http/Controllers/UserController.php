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
     * @api {post} /login 03-用户登录
     * @apiDescription 手机号有验证11位正确格式，密码6~16位
     * @apiGroup 01-user
     * @apiName login
     *
     *
     * @apiParam {String} call 注册的手机号
     * @apiParam {String} passwd 用户密码
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
     * {
     * "code": 1001,
     * "detail": "账号或密码错误",
     * "data": ""
     * }
     * @apiSuccessExample {json} 正确返回值:
     * {
     * "code": 200,
     * "detail": "success",
     * "data": {
     * "user_id": "6",
     * "token": "4MRhjarXvynrtkmS"
     * }
     * }
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
     * @api {post} /register 02-用户注册
     * @apiDescription 手机号有验证11位正确格式，密码6~16位
     * @apiGroup 01-user
     * @apiName register
     *
     *
     * @apiParam {String} call 注册的手机号
     * @apiParam {String} passwd 用户密码
     * @apiParam {String} passwd_check 用户密码
     * @apiParam {int} captcha 验证码
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
     * {
     * "code": 1002,
     * "detail": "该手机号已经注册",
     * "data": ""
     * }
     * @apiSuccessExample {json} 正确返回值:
     * {
     * "code": 200,
     * "detail": "success",
     * "data": {
     * "user_id": "7", #用户ID
     * "token": "5oYbL2F1mDKycPQu", #token凭证
     * "user_info":""  #不知道具体业务  暂时没数据
     * }
     * }
     */
    public function register()
    {
        $pro = array(
            'call' => 'required|regex:/^1[34578][0-9]{9}$/',
            'passwd' => 'required|digits_between:6,16',
            'passwd_check' => 'required|digits_between:6,16',
            'captcha' => 'required'
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        if ($p['passwd'] != $p['passwd_check']) {
            return resp_err(1003);
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
     * @api {post} /captcha/register 01-验证码->用户注册
     * @apiDescription 手机号有验证11位正确格式，注册过的手机不可申请验证码
     * @apiGroup 01-user
     * @apiName captcha/register
     *
     *
     * @apiParam {String} call 注册的手机号
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
     * {
     * "code": 1002,
     * "detail": "该手机号已经注册",
     * "data": ""
     * }
     * @apiSuccessExample {json} 正确返回值:
     * {
     * "code": 200,
     * "detail": "success",
     * "data": "短信已成功发送至15285588389"
     * }
     */
    public function captcha_register()
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
        if ($status !== 1) {
            return $status;
        }

        return resp_suc('短信已成功发送至' . $p['call']);
    }

    /**
     * @api {post} /captcha/forgetpass 04-验证码->忘记密码
     * @apiDescription 手机号有验证11位正确格式，没有注册过的用户不可发送验证码
     * @apiGroup 01-user
     * @apiName captcha/forgetpass
     *
     *
     * @apiParam {String} call 注册的手机号
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
     * {
     * "code": 1004,
     * "detail": "该手机号并未注册",
     * "data": ""
     * }
     * @apiSuccessExample {json} 正确返回值:
     * {
     * "code": 200,
     * "detail": "success",
     * "data": "短信已成功发送至15285588389"
     * }
     */
    public function captcha_forgetpass()
    {
        $pro = array(
            'call' => 'required|regex:/^1[34578][0-9]{9}$/',
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        $user_info = UserModel::where('call', $p['call'])->first();
        if (!$user_info) {
            return resp_err(1004);
        }
        $status = CaptchaService::make_captcha($p['call'], 'FG_');//FG  忘记密码的验证码
        if ($status !== 1) {
            return $status;
        }

        return resp_suc('短信已成功发送至' . $p['call']);
    }

    /**
     * @api {post} /resetpass/forget 05-重置密码->忘记密码
     * @apiDescription 手机号有验证11位正确格式
     * @apiGroup 01-user
     * @apiName /resetpass/forget
     *
     *
     * @apiParam {String} call 注册的手机号
     * @apiParam {String} passwd 用户密码
     * @apiParam {String} passwd_check 用户密码
     * @apiParam {int} captcha 验证码
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
     * {
     * "code": 1004,
     * "detail": "该手机号并未注册",
     * "data": ""
     * }
     * @apiSuccessExample {json} 正确返回值:
     * {
     * "code": 200,
     * "detail": "success",
     * "data": "密码重置成功"
     * }
     */
    public function resetpass_forget()
    {
        $pro = array(
            'call' => 'required|regex:/^1[34578][0-9]{9}$/',
            'passwd' => 'required|digits_between:6,16',
            'passwd_check' => 'required|digits_between:6,16',
            'captcha' => 'required'
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        if ($p['passwd'] != $p['passwd_check']) {
            return resp_err(1003);
        }

        $user_info = UserModel::where('call', $p['call'])->first();
        if (!$user_info) {
            return resp_err(1004);
        }
        $check_captcha = CaptchaService::check_captcha($p['call'], 'FG_', $p['captcha']);

        if ($check_captcha !== 1) {
            return $check_captcha;
        }
        return resp_suc('密码重置成功');
    }

    /**
     * @api {post} /resetpass/set 06-重置密码->用户重置
     * @apiDescription 手机号有验证11位正确格式
     * @apiGroup 01-user
     * @apiName resetpass/set
     *
     * @apiHeader {string} token <font color=#0099ff >**用户登录token**</font>
     * @apiParam {String} user_id 用户ID
     * @apiParam {String} passwd_old 用户老密码
     * @apiParam {String} passwd 用户新密码
     * @apiParam {String} passwd_check 用户密码校验
     * @apiParam {int} captcha 验证码
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
     * {
     * "code": 1006,
     * "detail": "旧密码输入错误",
     * "data": ""
     * }
     * @apiSuccessExample {json} 正确返回值:
     * {
     * "code": 200,
     * "detail": "success",
     * "data": "密码重置成功"
     * }
     */
    public function resetpass_set()
    {
        $pro = array(
            'user_id' => 'required',
            'passwd_old' => 'required|digits_between:6,16',
            'passwd' => 'required|digits_between:6,16',
            'passwd_check' => 'required|digits_between:6,16',
        );
        if ($this->app_validata($pro, $error, $p)) {
            return resp_err(5001, $error);
        }
        if ($p['passwd'] != $p['passwd_check']) {
            return resp_err(1003);
        }

        $user_info = UserModel::where('id', $p['user_id'])->first();
        if ($user_info) {
            return resp_err(1005);
        }
        $passwd_old = md5($p['passwd_old'] . $user_info->solt);
        if ($user_info->passwd != $passwd_old) {
            return resp_err(1006);
        }


        $solt = get_rand_char(4);
        $user_model = new UserModel();
        $user_model->passwd = md5($p['passwd'] . $solt);
        $user_model->solt = $solt;
        $user_model->update();

        return resp_suc('密码重置成功');
    }
}
