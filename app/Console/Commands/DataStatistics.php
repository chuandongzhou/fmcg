<?php

namespace App\Console\Commands;

use App\Services\DataStatisticsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DataStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:statistics';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $nowTime = Carbon::now();
        $data = DataStatisticsService::getTodayDataStatistics($nowTime);

        \App\Models\DataStatistics::create($data);
    }
}
