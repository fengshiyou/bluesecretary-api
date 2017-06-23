<?php

// 错误response
if (!function_exists('resp_err')) {
    function resp_err($code=500, $msg = '')
    {

        if (empty($msg)) {
            $msg = config('errorCode.' . $code);
        }
        $result = array(
            'code' => $code,
            'detail' => $msg,
            'data' => ''
        );
        return resp_json($result);
    }
}


//成功response
if (!function_exists('resp_suc')) {
    function resp_suc($data = '',$detail='success')
    {
        $result = [
            "code" => 200,
            "detail" => $detail,
            "data" => $data
        ];
        return resp_json($result);
    }
}

//response - 返回 将所有返回值都变为字符串
if (!function_exists('resp_json')) {
    function resp_json($result)
    {

        // JSON_UNESCAPED_UNICODE 这个参数可以json不转译unicode值
        // 如果不加默认是输出如 {"hello":"\u4e16\u754c"}
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
}