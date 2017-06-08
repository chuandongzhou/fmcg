<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Inventory;
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
    protected $inventoryService;
    public function __construct()
    {
        $this->inventoryService = new InventoryService();
    }
    /**
     * 入库操作
     *
     * @param \App\Http\Requests\Api\v1\InventoryRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postInSave(Requests\Api\v1\InventoryRequest $request)
    {
        $data = $request->all();
        if ($this->inventoryService->in($data)) {
            return $this->success('成功入库!');
        };
        return $this->error('入库失败!');
    }

    public function postOutSave(Requests\Api\v1\InventoryRequest $request)
    {
        $data = $request->all();
        if ($this->inventoryService->out($data)) {
            return $this->success('成功出库');
        };
        return $this->error($this->inventoryService->getError());
    }

    /**
     * 搜索店铺内商品
     *
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

    /**
     * 获取商品入库错误
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postGoodsInError(Request $request)
    {
        $condition = $request->only('goods_id', 'order_number');
        $inventory = Inventory::with(['goods'])
            ->where($condition)
            ->OfOut()
            ->get();
        $inventory->each(function ($item) {
            $item->setAppends(['transformation_quantity']);
        });
        return $this->success(['inventory' => $inventory->toArray()]);
    }
}
