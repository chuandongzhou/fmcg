<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    protected $password, $appKey;

    public function __construct()
    {
        $pushConf = config('push.top');
        $this->password = $pushConf['message_password'];
        $this->appKey = $pushConf['app_key'];
    }

    /**
     * 消息列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex()
    {

        return view('index.personal.message', [
            'password' => $this->password,
            'appKey' => $this->appKey
        ]);
    }


    public function getKit(Request $request)
    {
        return view('index.personal.message-kit', [
            'password' => $this->password,
            'appKey' => $this->appKey,
            'remoteUid' => $request->input('remote_uid', '-1'),
            'fullScreen' => $request->input('full_screen')
        ]);
    }

    public function getGoodsDetail(Request $request)
    {

        return view('index.personal.message-goods-detail');
    }
}
