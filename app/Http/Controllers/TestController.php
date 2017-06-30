<?php

namespace App\Http\Controllers;

use App\Events\TestEvent;
use Event;
use App\Service\CaptchaService;
use App\Service\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;


class TestController extends Controller
{
    /**
     * @api {get} /test 接口测试
     * @apiDescription 根据ID（id）获取列表信息
     * @apiGroup test APIs
     *
     *
     * @apiParamExample {string} 请求参数格式:
     *    ?id=123&page=1&perpage=20
     *
     * @apiVersion 1.0.0
     * @apiErrorExample {json} 错误返回值:
     *     {
     *        "code": 10003,
     *        "msg": "ParametersError [Method]:get_tests参数错误!",
     *        "error": {
     *            "id": "",
     *            "page": "",
     *            "perpage": ""
     *        },
     *       "status": "fail"
     *     }
     * @apiSuccessExample {json} 正确返回值:
     *     {
     *   "code": 0,
     *   "msg": "OK ",
     *   "data": [
     *       {
     *           "id": "622051004185471233",
     *           "testCode": "000050",
     *       }
     *   ],
     *   "status": "ok",
     *   "count": "14"
     *   }
     */
    public function index()
    {
        //
        Event::setQueueResolver(function () {
            return Queue::connection('captcha');
        })->fire(new TestEvent(55,66));
        Event::setQueueResolver()->fire(new TestEvent(55,66));
    }

}
