<?php

namespace App\Console\Commands;

use App\Jobs\PushOrderMsg;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Redis;

class SendPushToQueue extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:push';


    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Support\Facades\Redis $redis
     */
    public function __construct(Redis $redis)
    {
        parent::__construct();
        $this->redis = $redis;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //遍历redis键,过期时间小于5分钟的放入推送列表
        $targetUserIds = $this->redis->keys('push:user:*');
        $targetSellerIds = $this->redis->keys('push:seller:*');
        $ids = array_merge($targetUserIds, $targetSellerIds);
        if (!empty($ids)) {
            foreach ($ids as $id) {
                if ($this->redis->ttl($id) < 300) {
                    $job = (new PushOrderMsg(substr($id, strrpos($id, ':') + 1)))->onQueue('pushNotice');
                    $this->dispatch($job);
                    $this->redis->del($id);
                }
            }
        }
    }
}
