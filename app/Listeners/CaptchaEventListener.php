<?php

namespace App\Listeners;

use App\Events\CaptchaEvent;
use App\Service\AliyunSMS;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaptchaEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CaptchaEvent  $event
     * @return void
     */
    public function handle(CaptchaEvent $event)
    {
        //
        AliyunSMS::send_captcha_sms($event->call,$event->captcha);
    }
}
