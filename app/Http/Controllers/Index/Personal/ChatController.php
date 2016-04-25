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
        $shopId = auth()->user()->shop()->pluck('id');
        return view('index.personal.chat', [
            'shopId' => $shopId
        ]);
    }


    /**
     * 聊天消息弹出页面
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getKit(Request $request)
    {
        $shopId = $request->input('remote_uid', '-1');
        $shop = Shop::with(['logo', 'user'])->select(['id', 'name', 'user_id'])->find($shopId);
        return view('index.personal.chat-kit', [
            'remoteUid' => $shopId,
            'fullScreen' => $request->input('full_screen'),
            'shop' => $shop
        ]);
    }

    /**
     * 聊天弹出层商品介绍
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDetail(Request $request)
    {
        $shopId = $request->input('id', '-1');
        $shop = Shop::find($shopId);
        return view('index.personal.chat-detail', ['shop' => $shop ?: new Shop]);
    }
}
