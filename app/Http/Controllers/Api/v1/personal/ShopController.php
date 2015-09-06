<?php

namespace App\Http\Controllers\Api\v1\personal;

use App\Http\Controllers\Api\v1\Controller;
use App\Models\Shop;
use App\Http\Requests;

class ShopController extends Controller
{
    /**
     * 保存店铺信息
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shop(Requests\Index\UpdateShopRequest $request)
    {
        // TODO: 更改shopid
        $shopId = 1;
        $shop = Shop::find($shopId);
        if ($shop->fill($request->all())->save()) {
            return $this->success('保存店铺成功');
        }
        return $this->error('保存店铺时出现错误');
    }

}
