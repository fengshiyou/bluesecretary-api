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

    public function index()
    {
        //
        $test = new TestService();
        $z = $test->test2();
        var_dump($z);die;
        $arr = array ('a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5);
        return 11111;
        return resp_suc();
        Event::setQueueResolver(function () {
            return Queue::connection('captcha');
        })->fire(new TestEvent(55,66));
        Event::setQueueResolver()->fire(new TestEvent(55,66));
    }

}
