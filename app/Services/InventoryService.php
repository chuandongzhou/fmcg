<?php
namespace App\Services;

use App\Models\Goods;
use App\Models\Inventory;
use App\Models\OrderGoods;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;

class InventoryService
{

    protected $inventory;

    public function __construct()
    {
        $this->inventory = new Inventory();
    }

    /**
     *
     * 获取不重复的入库单号
     *
     * @return string
     */
    public function getInventoryNumber()
    {
        $inventory_number = date('YmdHis') . rand(100, 999);
        if ($this->inventory->where('inventory_number', $inventory_number)->first()) {
            return $this->getInventoryNumber();
        } else {
            return $inventory_number;
        }
    }

    /**
     *
     * 入库操作
     *
     * @param $data
     */
    public function inventoryIn($data)
    {
        $info['inventory_number'] = $data['inventory_number'];
        $info['inventory_type'] = $data['inventory_type'];
        $info['action_type'] = $data['action_type'];
        $info['user_id'] = $data['user_id'] ?? auth()->user()->id;
        $info['shop_id'] = $data['shop_id'] ?? auth()->user()->shop->id;
        DB::beginTransaction();
        foreach ($data['goods'] as $key => $value) {
            foreach ($this->_formatData($key, $value, 'in') as $v) {
                $this->inventory->create(array_merge($info, $v));
            }
        }
        DB::commit();
        return true;

    }

    /**
     * 系统自动入库
     *
     * @param $goodsInfo //所有订单商品
     * @return bool|string
     */
    public function autoInventoryIn($goodsInfo)
    {
        try {
            foreach ($goodsInfo as $orderGoods) {
                //买家的商店
                $buyerShop = User::find($orderGoods->order->user_id)->shop;
                //买家商品库的同款商品
                $buyerGoods = $buyerShop->goods->where('bar_code',$orderGoods->goods->bar_code)->first();
                //查无此商品则标记为入库异常
                if (empty($buyerGoods)) {
                    $orderGoods->inventory_state = cons('inventory.inventory_state.abnormal');
                    $orderGoods->save();
                } else {
                    //如果是处理异常则标记为已处理
                    if($orderGoods->inventory_state == cons('inventory.inventory_state.abnormal')){
                        $orderGoods->inventory_state = cons('inventory.inventory_state.disposed');
                        $orderGoods->save();
                    }
                    //拿到该商品的出库记录
                    $outRecord = $this->inventory->where('goods_id', $orderGoods->goods_id)->where('order_number',
                        $orderGoods->order_id)->OfOut()->get();
                    foreach ($outRecord as $record) {
                        $data['inventory_number'] = $this->getInventoryNumber();
                        $data['user_id'] = 0;
                        $data['goods_id'] = $buyerGoods->id;
                        $data['shop_id'] = $buyerShop->id;
                        $data['inventory_type'] = cons('inventory.inventory_type.system');
                        $data['action_type'] = cons('inventory.action_type.in');
                        $data['order_number'] = $record->order_number;
                        $data['production_date'] = $record->production_date;
                        $data['pieces'] = $record->pieces;
                        $data['cost'] = $record->cost;
                        $data['quantity'] = $record->quantity;
                        $data['surplus'] = $record->quantity;
                        $data['remark'] = '系统自动入库';
                        $this->inventory->create($data);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            info($e->getMessage() . $e->getFile() . $e->getLine());
            return false;
        }
    }

    /**
     * 出库
     *
     * @param $data
     * @return bool|string
     */
    public function inventoryOut($data)
    {
        try {
            $info['inventory_number'] = $data['inventory_number'];
            $info['inventory_type'] = $data['inventory_type'];
            $info['action_type'] = $data['action_type'];
            $info['user_id'] = $data['user_id'] ?? auth()->user()->id;
            $info['shop_id'] = $data['shop_id'] ?? auth()->user()->shop->id;

            DB::beginTransaction();

            foreach ($data['goods'] as $goods_id => $value) {
                //得到该商品的库存
                $goods = Goods::find($goods_id);
                //出库数量
                $quantity = $value['quantity'][0] * GoodsService::getPiecesSystem($goods_id, $value['pieces'][0]);
                if ($goods->total_inventory < $quantity) {
                    return $goods->name . ' 库存不足';
                }
                foreach ($this->_formatData($goods_id, $value, false) as $v) {
                    $data = array_merge($info, $v);
                };
                //递归出库
                $this->goodsOutByGoodsId($goods_id, $quantity, $data);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            info($e->getMessage() . $e->getFile() . $e->getLine());
            return false;
        }
    }

    /**
     * 系统自动出库
     *
     * @param $goodsInfo
     * @return bool|string
     */
    public function autoInventoryOut($goodsInfo)
    {
        foreach ($goodsInfo as $goods) {
            $data = [
                'inventory_number' => $this->getInventoryNumber(),
                'inventory_type' => cons('inventory.inventory_type.system'),
                'action_type' => cons('inventory.action_type.out'),
                'user_id' => 0,
                'goods' => [
                    $goods->goods_id => [
                        'order_number' => [
                            $goods->order_id
                        ],
                        'quantity' => [
                            $goods->num
                        ],
                        'cost' => [
                            $goods->price
                        ],
                        'pieces' => [
                            $goods->pieces
                        ],
                        'remark' => [
                            '系统自动出库!'
                        ],
                    ]
                ]
            ];
            return $this->inventoryOut($data);
        }
    }

    /**
     * 递归出库
     *
     * @param $goods_id
     * @param $quantity
     * @return bool
     */
    private function goodsOutByGoodsId($goods_id, $quantity, $data)
    {
        //取出生产日期最前的库存记录
        $inventory = $this->getFirstData($goods_id);
        $data['production_date'] = $inventory->production_date;
        //判断库存记录是否足够本次出库
        //够
        if ($inventory->surplus >= $quantity) {
            //减掉出库的数量
            $inventory->surplus -= $quantity;
            //保存
            $inventory->save();
            //增加一条出库记录
            $data['quantity'] = $quantity;
            $this->inventory->create($data);
            return true;
        } else {
            //不够
            //减掉本条库存剩余数量拿到差值
            $quantity -= $inventory->surplus;
            $data['quantity'] = $inventory->surplus;
            $inventory->surplus = 0;
            $inventory->save();
            //增加一条出库记录

            $this->inventory->create($data);
            //递归继续出库
            $this->goodsOutByGoodsId($goods_id, $quantity, $data);
        }
    }

    /**
     *取出一条生产日期最前的入库商品记录
     *
     * @param $goods_id
     * @return mixed
     */
    private function getFirstData($goods_id)
    {
        return $this->inventory->OfIn()->where('surplus', '>', 0)->where('goods_id',
            $goods_id)->orderBy('production_date',
            'asc')->first();
    }


    /**
     * 格式化数据
     *
     * @param $goodsId
     * @param $detail
     * @param bool $in
     * @return array
     */
    private function _formatData($goodsId, $detail, $in = true)
    {
        $formatted = [];
        foreach ($detail['quantity'] as $key => $value) {
            $formatted[$key]['goods_id'] = $goodsId;
            if ($in) {
                $formatted[$key]['production_date'] = $detail['production_date'][$key];
            } else {
                $formatted[$key]['order_number'] = $detail['order_number'][$key] ?? 0;
            }
            $formatted[$key]['cost'] = $detail['cost'][$key];
            $formatted[$key]['pieces'] = $detail['pieces'][$key];
            $formatted[$key]['quantity'] = $detail['quantity'][$key] * GoodsService::getPiecesSystem($goodsId,
                    $detail['pieces'][$key]);  // 默认转为最小单位计数
            $formatted[$key]['surplus'] = $formatted[$key]['quantity'] ?? 0;
            $formatted[$key]['remark'] = $detail['remark'][$key];

        }
        return $formatted;
    }

    /**
     * 库存搜索
     *
     * @param $inventory
     * @param array $data
     * @param array $with
     * @return mixed
     */
    static public function search($inventory, array $data, array $with = [])
    {
        return $inventory->with($with)->VerifyShop()->where(function ($query) use ($data) {
            if (!empty($data['start_at']) && isset($data['start_at'])) {
                $query->where('created_at', '>=', $data['start_at']);
            }
            if (!empty($data['end_at']) && isset($data['end_at'])) {
                $query->where('created_at', '<=', $data['end_at']);
            }
            if (!empty($data['inventory_type']) && isset($data['inventory_type'])) {
                $query->where('inventory_type', $data['inventory_type']);
            }
            if (!empty($data['action_type']) && isset($data['action_type'])) {
                $query->where('action_type', $data['action_type']);
            }
            if (empty($data['end_at']) && !isset($data['end_at']) && empty($data['start_at']) && !isset($data['start_at'])) {
                $query->OfNowMonth();
            }
            if (!empty($data['number']) && isset($data['number'])) {
                $query->OfNumber($data['number']);
            }
        });
    }

    /**
     * 转换出入库数量
     *
     * @param $goods 商品模型
     * @param $total 总数量
     * @return int|string
     */
    static public function calculateQuantity($goods, $total)
    {
        $result = '';
        if (!($goods instanceof Goods)) {
            $goods = Goods::find($goods);
        }
        if (isset($goods->goodsPieces->pieces_level_1)) {
            $lv1 = GoodsService::getPiecesSystem($goods->id, $goods->goodsPieces->pieces_level_1);
            $lv2 = GoodsService::getPiecesSystem($goods->id, $goods->goodsPieces->pieces_level_2);
            $lv3 = GoodsService::getPiecesSystem($goods->id, $goods->goodsPieces->pieces_level_3);
            if ($total % $lv1 === 0) {
                $result = $total / $lv1 . cons()->valueLang('goods.pieces',
                        $goods->goodsPieces->pieces_level_1);
            } else {
                if (floor($total / $lv1) > 0) {
                    $result = floor($total / $lv1) . cons()->valueLang('goods.pieces',
                            $goods->goodsPieces->pieces_level_1);
                }
                if (($total % $lv1) % $lv2 === 0) {
                    $result .= ($total % $lv1) / $lv2 . cons()->valueLang('goods.pieces',
                            $goods->goodsPieces->pieces_level_2);
                } else {
                    if (floor(($total % $lv1) / $lv2) > 0) {
                        $result .= floor(($total % $lv1) / $lv2) . cons()->valueLang('goods.pieces',
                                $goods->goodsPieces->pieces_level_2);
                    }
                    $result .= floor(($total % $lv1) % $lv2 / $lv3) . cons()->valueLang('goods.pieces',
                            $goods->goodsPieces->pieces_level_3);
                }
            }

        }
        return $result;
    }

    /**
     * 获取入库异常商品
     * @return mixed
     */
    public function getInError()
    {
        $orderIds = [];
        auth()->user()->orders->each(function ($order) use(&$orderIds){
            $orderIds[] = $order->id;
        });
        return OrderGoods::where('inventory_state',cons('inventory.inventory_state.abnormal'))->whereIn('order_id',$orderIds);
    }

}