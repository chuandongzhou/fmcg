<?php

namespace App\Services;

use Davibennun\LaravelPushNotification\Facades\PushNotification;
use App\Http\Requests;
use Riverslei\Pusher\Pusher;


class PushOrderService
{
    //
    public function pusher($channelId,$roleType,$opts)
    {

        $channelId = '4101374783240571457';
        // 消息内容.
        $message = array (
            // 消息的标题.
            'title' => 'Hi!.',
            // 消息内容
            'description' => "hi gay,look here"
        );
        // 设置消息类型为 通知类型.
        $opts = array (
            'msg_type' => 1
        );
        // 向目标设备发送一条消息
        $rs = Pusher::pushMsgToSingleDevice($channelId, $message, $opts);

        // 判断返回值,当发送失败时, $rs的结果为false, 可以通过getError来获得错误信息.
        if($rs === false){
            print_r(Pusher::getLastErrorCode());
            print_r(Pusher::getLastErrorMsg());
        }else{
            // 将打印出消息的id,发送时间等相关信息.
            var_dump($rs);
        }

        echo "done!";
    }

    public function pushIos()
    {
       $res =  PushNotification::app('IOS')
            ->to('3060a641b835e3605cb0bd22c6bdc9bb1afbf1b05fe262981b799b41dd049eb0')
            ->send('Hello World, i`m a push message');
        dd($res);
    }
}
