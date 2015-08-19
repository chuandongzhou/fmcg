<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;

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
        return view('admin.shop.shop', ['shop' => $shop]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $shop
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $shop)
    {
        if ($shop->fill($request->all())->save()) {
            return $this->success('保存店铺成功');
        }
        return $this->error('保存店铺时出现错误');
    }

}
