<?php

namespace App\Console\Commands;

use App\Jobs\PushOrderMsg;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Redis\Database as Redis;

class SendPushToQueue extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:push';
    protected $redis;


    /**
     * SendPushToQueue constructor.
     *
     * @param \Illuminate\Redis\Database $redis
     */
    public function __construct(Redis $redis)
    {
        parent::__construct();
        $this->redis = $redis->connection();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //遍历redis键,过期时间小于5分钟的放入推送列表push:user:*,push:seller:*,push:withdraw:*
        $ids = $this->redis->keys('push:*');
        if (!empty($ids)) {
            $whenPush = cons('push_time.when_push');
            foreach ($ids as $id) {
                if ($this->redis->ttl($id) < $whenPush) {
                    $job = (new PushOrderMsg(substr($id, strrpos($id, ':') + 1), $this->redis->get($id)))->onQueue('pushNotice');
                    $this->dispatch($job);
                    $this->redis->del($id);
                }
            }
        }
    }
}
