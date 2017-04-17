<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Http\Controllers\Api\V1\Controller;
use Gate;

class GiftController extends Controller
{

    /**
     * 获取所有赠品
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        $shop = salesman_auth()->user() ? salesman_auth()->user()->shop : auth()->user()->shop;

        $gifts = $shop->goods()->active()->where('is_gift', 1)->with('goodsPieces')->get([
            'id',
            'name'
        ])->each(function ($item) {
            $item->setHidden(['image_url', 'price']);
        });
        return $this->success(compact('gifts'));
    }

}
