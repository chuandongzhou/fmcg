<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Inventory;
use App\Models\OrderGoods;
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
    public function getGoodsList(Request $request)
    {
        $conditions = $request->only('nameOrCode', 'ids', 'page');
        $shop = auth()->user()->shop;
        $goods = $shop->goods()->with('goodsPieces')->where(function ($query) use ($conditions) {
            // 名称or条形码
            if ($nameOrCode = (array_get($conditions, 'nameOrCode'))) {
                $query->OfNameOrCode($nameOrCode);
            }
            if (isset($conditions['ids'])) {
                $query->whereNotIn('id', $conditions['ids']);
            }
        })->orderBy('updated_at', 'DESC')->paginate();
        $goods->each(function ($item) {
            $item->goodsPieces->pieces_level_1_lang = cons()->valueLang('goods.pieces',
                $item->goodsPieces->pieces_level_1);
            $item->goodsPieces->pieces_level_2_lang = cons()->valueLang('goods.pieces',
                $item->goodsPieces->pieces_level_2);
            $item->goodsPieces->pieces_level_3_lang = cons()->valueLang('goods.pieces',
                $item->goodsPieces->pieces_level_3);
        });
        return $this->success([
            'goods' => $goods->toArray(),
        ]);
    }

    /**
     * 获取商品入库错误
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postGoodsInError(Request $request)
    {
        $orderGoodsId = $request->input('orderGoods');
        $orderGoods = OrderGoods::find($orderGoodsId);
        $where = [
            'source' => $this->inventoryService->_getSource($orderGoods->type),
            'goods_id' => $orderGoods->goods_id,
            'order_number' => $orderGoods->order_id
        ];
        $inventory = Inventory::with(['goods'])
            ->where($where)
            ->OfOut()
            ->get();
        $inventory->each(function ($item) {
            $item->setAppends(['transformation_quantity']);
        });
        return $this->success(['inventory' => $inventory->toArray()]);
    }
}
