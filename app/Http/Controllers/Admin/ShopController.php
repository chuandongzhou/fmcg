<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests;
use App\Services\ChatService;

class ShopController extends Controller
{
    /**
     *  Show the form for editing the specified resource.
     *
     * @param $shop
     * @return \Illuminate\View\View
     */
    public function edit($shop)
    {
        //$shop = $shop->load(['images', 'user', 'deliveryArea', 'shopAddress']);
    /*    $coordinate = $shop->deliveryArea->each(function ($area) {
            $area->coordinate;
        });*/
        return view('admin.shop.shop', ['shop' => $shop/*, 'coordinates' => $coordinate->toJson()*/]);
    }

    /**
     * 保存店铺
     *
     * @param \App\Http\Requests\Admin\UpdateShopRequest $request
     * @param $shop
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Admin\UpdateShopRequest $request, $shop)
    {
        if ($shop->fill($request->all())->save()) {

            //更新聊天远程
            $shop->load('user');
            (new ChatService())->usersHandle($shop, true);
            return $this->success('保存店铺成功');
        }
        return $this->error('保存店铺时出现错误');
    }

}
