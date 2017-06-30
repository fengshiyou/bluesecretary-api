<?php

namespace App\Http\Controllers;

use App\Service\CaptchaService;
use App\Service\TestService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
//        $z = TestService::test();
        $z = array(
            "code" => 0,
            "msg" => "OK",
            "data"=>[
                "id"=>"1111",
                "testCode"=>"22222",
            ],
            "status"=>"OK",
            "count"=>14
        );
        return resp_json($z);
        $result = CaptchaService::makeCaptcha(15285588389, 'RG_');
        return $result;
//        $z = 1;
//        var_dump( $z);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
