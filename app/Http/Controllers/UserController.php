<?php

namespace App\Http\Controllers;

use App\Model\UserModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
            return resp_err(50005, $error);
        }
        $acount = $p['account'];
        $passwd = md5($p['passwd']);
        $user_info = UserModel::where('account', $acount)->first();
        if (!$user_info) {
            return resp_err(1001, '用户不存在');
        }

        if ($passwd != $user_info['passwd']) {
            return resp_err(2);
        }
        echo 1;
    }

}
