<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\V1\Controller;
use Gate;
use App\Http\Requests;

class ShopController extends Controller
{
    /**
     * @param \App\Http\Requests\Api\v1\UpdateShopRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shop(Requests\Api\v1\UpdateShopRequest $request , $shop)
    {
        if (Gate::denies('validate-shop',$shop)){
            return $this->error('保存失败');
        }

        if ($shop->fill($request->all())->save()) {
            return $this->success('保存店铺成功');
        }
        return $this->error('保存店铺时出现错误');
    }

}
