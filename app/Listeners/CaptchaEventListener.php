<?php

namespace App\Listeners;

use App\Events\CaptchaEvent;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Service\AliyunSMS;

class CaptchaEventListener implements ShouldQueue
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
