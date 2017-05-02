<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Services\InventoryService;
use Illuminate\Http\Request;

use App\Http\Requests;

/**
 *
 * 库存api
 * Class InventoryController
 *
 * @package App\Http\Controllers\Api\V1
 */
class InventoryController extends Controller
{
    /**
     * 入库操作
     * @param \App\Http\Requests\Api\v1\InventoryRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postInSave(Requests\Api\v1\InventoryRequest $request)
    {
        $data = $request->all();
        if ((new InventoryService())->inventoryIn($data)) {
            return $this->success('成功入库!');
        };
        return $this->error('入库失败!');
    }

    public function postOutSave(Requests\Api\v1\InventoryRequest $request)
    {
        $data = $request->all();
        $result = (new InventoryService())->inventoryOut($data);
        if ($result === true) {
            return $this->success('成功出库');
        };
        return $this->error(is_string($result) ? $result : '出库失败!');
    }

    /**
     * 搜索店铺内商品
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postGetGoods(Request $request)
    {
        $conditions = $request->only('nameOrCode', 'ids');
        $shop = auth()->user()->shop;
        $result = (new GoodsService())->getShopGoods($shop, $conditions, ['goodsPieces']);
        $goods = $result['goods']->orderBy('updated_at', 'DESC')->paginate();

        $goods->each(function ($item) {
            $item->goodsPieces->pieces_level_1_lang = cons()->valueLang('goods.pieces',
                $item->goodsPieces->pieces_level_1);
            $item->goodsPieces->pieces_level_2_lang = cons()->valueLang('goods.pieces',
                $item->goodsPieces->pieces_level_2);
            $item->goodsPieces->pieces_level_3_lang = cons()->valueLang('goods.pieces',
                $item->goodsPieces->pieces_level_3);
        });
        return $this->success(['goods' => $goods->toArray()]);
    }
}
