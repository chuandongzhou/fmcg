<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\V1\Controller;
use Gate;
use App\Http\Requests;

class ShopController extends Controller
{
    /**
     * 保存店铺
     *
     * @param \App\Http\Requests\Api\v1\UpdateShopRequest $request
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shop(Requests\Api\v1\UpdateShopRequest $request, $shop)
    {
        $attributes = $request->all();
        if (Gate::denies('validate-shop', $shop)) {
            return $this->error('保存失败');
        }
        if ($shop->fill(array_except($attributes, 'id'))->save()) {
            return $this->success('保存店铺成功');
        }
        return $this->error('保存店铺时出现错误');
    }

}
