<?php

namespace App\Services;

use Davibennun\LaravelPushNotification\Facades\PushNotification;
use App\Http\Requests;
use Riverslei\Pusher\Pusher;
use App\Models\PushDevice;


class PushOrderService
{

    /**
     * 推送信息到指定用户最近使用的移动设备
     *
     * @param $targetUserId
     * @return bool
     */
    public function push($targetUserId, $msg)
    {
        //只推送最近使用的移动设备
        $device = PushDevice::where('user_id', $targetUserId)->orderBy('updated_at', 'desc')->first();
        if (!$device) {
            return false;
        }
        $msgArray = ['title' => '', 'body' => $msg];
        if ($device->type == cons('push_device.iphone')) {
            $res = $this->pushIos($device->token, $msgArray);
        } else {
            $res = $this->pushAndroid($device->token, $msgArray);
        }
        if ($res) {//推送成功,推送条数+1
            $device->send_count += $device->send_count;
            $device->save();
        }

        return $res;

    }

    /**
     * 推送到android设备
     *
     * @param $channelId
     * @param array $msg
     * @return mixed
     */
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
            'msg_type' => cons('push_type.notice')
        ];

        // 向目标设备发送一条消息
        return Pusher::pushMsgToSingleDevice($channelId, $message, $opts);

    }

    /**
     * 推送到Ios设备
     *
     * @param $channelId
     * @param array $msg
     * @return mixed
     */
    public function pushIos($channelId, array $msg)
    {
        return PushNotification::app('IOS')->to($channelId)->send($msg['title'] . $msg['body']);
    }
}
