<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    /**
     * 消息列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex()
    {

        return view('index.personal.chat');
    }


    public function getKit(Request $request)
    {
        $shopId = $request->input('remote_uid', '-1');
        $shop = Shop::with('logo')->select(['id' ,'name'])->find($shopId);
        return view('index.personal.chat-kit', [
            'remoteUid' => $shopId,
            'fullScreen' => $request->input('full_screen'),
            'shop' => $shop
        ]);
    }

    public function getGoodsDetail(Request $request)
    {
        return view('index.personal.chat-goods-detail');
    }
}
