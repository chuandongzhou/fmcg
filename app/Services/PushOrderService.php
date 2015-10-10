<?php

namespace App\Services;

use Davibennun\LaravelPushNotification\Facades\PushNotification;
use App\Http\Requests;
use Riverslei\Pusher\Pusher;
use App\Models\PushDevice;


class PushOrderService
{

    public function push($targetUserId)
    {
        //只推送最近使用的移动设备
        $device = PushDevice::where('user_id', $targetUserId)->orderBy('updated_at', 'desc')->first();
        if (!$device) {
            return false;
        }
        $msg = ['title' => 'hi', 'body' => 'you have a new order'];
        if ($device->type == cons('push_device.iphone')) {
            $res = $this->pushIos($device->token, $msg);
        } else {
            $res = $this->pushAndroid($device->token, $msg);
        }

        return $res;

    }

    public function pushAndroid($channelId, array $msg)
    {
        // 消息内容.
        $message = [
            // 消息的标题.
            'title' => $msg['title'],
            // 消息内容
            'description' => $msg['body']
        ];
        // 设置消息类型为 通知类型.
        $opts = [
            'msg_type' => 1
        ];

        // 向目标设备发送一条消息
        return Pusher::pushMsgToSingleDevice($channelId, $message, $opts);

    }

    public function pushIos($channelId, array $msg)
    {
        return PushNotification::app('IOS')->to($channelId)->send($msg['title'] . $msg['body']);
    }
}
