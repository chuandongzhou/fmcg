<?php

namespace App\Jobs;

use App\Services\PushOrderService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushOrderMsg extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $targetUserId;

    /**
     * @param $targetUserId
     */
    public function __construct($targetUserId)
    {
        $this->targetUserId = $targetUserId;
    }

    /**
     * 执行任务
     *
     * @param \App\Services\PushOrderService $push
     */
    public function handle(PushOrderService $push)
    {
        $push->push($this->targetUserId);
    }

    /**
     * 删除失败的任务
     */
    public function failed()
    {
        $this->delete();
    }
}
