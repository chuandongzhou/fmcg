<?php

namespace App\Jobs;

use App\Services\OrderService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushOrderMsg extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $targetUserId;
    protected $msgBody;

    /**
     * @param $targetUserId
     */
    public function __construct($targetUserId, $msg)
    {
        $this->targetUserId = $targetUserId;
        $this->msgBody = $msg;
    }

    /**
     * 执行任务
     *
     * @param \App\Services\OrderService $push
     */
    public function handle(OrderService $push)
    {
        $push->push($this->targetUserId, $this->msgBody);
    }

    /**
     * 删除失败的任务
     */
    public function failed()
    {
        $this->delete();
    }
}
