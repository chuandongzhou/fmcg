<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use Illuminate\Support\Facades\Request;

class MessageController extends Controller
{

    /**
     * 消息列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex()
    {
        return view('index.personal.message');
    }


    public function getKit(Request $request)
    {

        return view('index.personal.message-kit');
    }

    public function getGoodsDetail(Request $request)
    {

        return view('index.personal.message-goods-detail');
    }
}
