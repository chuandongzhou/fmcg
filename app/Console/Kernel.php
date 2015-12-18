<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\DataStatistics::class,
        \App\Console\Commands\SendPushToQueue::class,
        \App\Console\Commands\OrderAutoReceive::class,
        \App\Console\Commands\OrderAutoCancel::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')->hourly();
        $schedule->command('data:statistics')->daily();
        $schedule->command('queue:push')->everyFiveMinutes();
        $schedule->command('order:auto:receive')->hourly();    //订单超过3天未收货自动收货
        $schedule->command('order:auto:cancel')->hourly();     //订单超过24小时未付款自动取消
    }
}
