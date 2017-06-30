<?php
namespace App\Service;


/**
 * Created by PhpStorm.
 * User: win7
 * Date: 2017/6/22
 * Time: 16:52
 */
Class AliyunSMS
{
    //发送验证码
    public static function send_captcha_sms($call, $captcha)
    {
        $host = "http://sms.market.alicloudapi.com";
        $path = "/singleSendSms";
        $method = "GET";
        $appcode = "7dd849fa09d5469cad148d671d6de0cb";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $ParamString = "{'captcha':'$captcha'}";
        $querys = "ParamString=$ParamString&RecNum=$call&SignName=蓝秘书&TemplateCode=SMS_74765015";
        $bodys = "";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
//        var_dump(curl_exec($curl));
        curl_exec($curl);
    }
}