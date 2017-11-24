<?php
namespace App\Services;

use App\Models\Goods;
use App\Models\Inventory;
use App\Models\OrderGoods;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class InventoryService extends BaseService
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
    public function getInventoryNumber($filed = 'inventory_number')
    {
        $inventory_number = date('YmdHis') . rand(100, 999);
        if ($this->inventory->where($filed, $inventory_number)->first()) {
            return $this->getInventoryNumber($filed);
        }
        return $inventory_number;

    }

    /**
     *
     * 入库操作
     *
     * @param $data
     */
    public function in($data)
    {
        $info['inventory_number'] = $data['inventory_number'] ?? $this->getInventoryNumber();
        $info['inventory_type'] = $data['inventory_type'] ?? cons('inventory.inventory_type.manual');
        $info['action_type'] = $data['action_type'] ?? cons('inventory.action_type.in');
        $auth = auth()->id() ? auth() : wk_auth();
        $info['user_id'] = $data['user_id'] ?? $auth->id();
        $info['operate'] = $data['operate'] ?? (wk_auth()->id() ? 'warehouseKeeper' : 'user');
        $info['shop_id'] = $data['shop_id'] ?? $auth->user()->shop_id;

        DB::beginTransaction();
        $orderGoods = array_get($data, 'orderGoods');
        if ($orderGoods) { //存在为异常处理入库
            OrderGoods::where('id', $orderGoods)->update(['inventory_state' => cons('inventory_state.disposed')]);
        }
        foreach ($data['goods'] as $key => $value) {
            foreach ($this->_formatData($key, $value) as $v) {
                $this->inventory->create(array_merge($info, $v));
            }
        }
        DB::commit();
        return true;
    }

    /**
     * 系统自动入库
     *
     * @param $order //订单
     * @return bool|string
     */
    public function autoIn($order)
    {
        if ($order->user_id < 1 || $order->user->type == cons('user.type.retailer')) {
            return true;
        }
        $inventory_number = $this->getInventoryNumber();
        foreach ($order->orderGoods as $orderGoods) {
            if ($this->orderGoodsIn($orderGoods, $inventory_number)) {
                continue;
            }
        }
        return true;
    }

    /**
     *处理入库异常
     *
     * @param $orderGoods
     */
    public function inException(OrderGoods $orderGoods)
    {
        return $this->orderGoodsIn($orderGoods, $this->getInventoryNumber());
    }

    public function orderGoodsIn($orderGoods, $inventory_number)
    {
        //买家的商店
        $buyerShop = User::find($orderGoods->order->user_id)->shop;
        //买家商品库的同款商品
        $buyerGoods = $buyerShop->goods->where('bar_code', $orderGoods->goods->bar_code)->first();
        $source = $this->_getSource($orderGoods->type); //商品来源 (订单/抵费/促销/赠品)
        $where = [
            'source' => $source,
            'goods_id' => $orderGoods->goods_id,
            'order_number' => $orderGoods->order_id
        ];
        //拿到该商品的出库记录
        $outRecord = $this->inventory->where($where)->OfOut()->get();
        //查无此商品或单位不匹配或者出库记录为空则标记为入库异常
        $except = ['id', 'goods_id'];
        if ($outRecord->isEmpty() || empty($buyerGoods) || !$this->_checkPieces(array_except($buyerGoods->goodsPieces->toArray(),
                $except),
                array_except($orderGoods->goods->goodsPieces->toArray(), $except))
        ) {
            $orderGoods->inventory_state = cons('inventory.inventory_state.in-abnormal');
            $orderGoods->save();
            return false;
        }

        //如果是处理异常则标记为已处理
        if ($orderGoods->inventory_state == cons('inventory.inventory_state.in-abnormal')) {
            $orderGoods->inventory_state = cons('inventory.inventory_state.disposed');
            $orderGoods->save();
        }
        $outRecord = $this->_checkRepeated($outRecord);

        foreach ($outRecord as $record) {
            $result = $this->inventory->create([
                'inventory_number' => $inventory_number,
                'user_id' => 0,
                'source' => $record->source,
                'operate' => 'user',
                'goods_id' => $buyerGoods->id,
                'shop_id' => $buyerShop->id,
                'inventory_type' => cons('inventory.inventory_type.system'),
                'action_type' => cons('inventory.action_type.in'),
                'order_number' => $record->order_number,
                'production_date' => $record->production_date,
                'pieces' => $record->pieces,
                'cost' => $record->cost,
                'quantity' => $record->quantity,
                'surplus' => $record->quantity,
                'remark' => '系统自动入库!',
            ]);
            if (!$result) {
                $orderGoods->update(['inventory_sate', cons('inventory.inventory_state.in-abnormal')]);
            }
        }
    }

    /**
     * 出库
     *
     * @param $data
     * @return bool|string
     */
    public function out($data)
    {
        try {
            $info['inventory_number'] = $data['inventory_number'];
            $info['inventory_type'] = $data['inventory_type'];
            $info['action_type'] = $data['action_type'];
            $info['user_id'] = $data['user_id'] ?? auth()->id();
            $info['shop_id'] = $data['shop_id'] ?? (new UserService())->getCurrentLoggedUser()->shop_id;
            $info['operate'] = array_get($data, 'operate', 'user');
            DB::beginTransaction();
            foreach ($data['goods'] as $goods_id => $value) {
                //得到该商品的库存
                $goods = Goods::find($goods_id);
                //需要出库的数量
                $quantity = $value['quantity'][0] * GoodsService::getPiecesSystem($goods, $value['pieces'][0]);
                if ($goods->total_inventory < $quantity) {
                    $this->setError($goods->name . ' 库存不足');
                    //info($goods);
                    return false;
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
            $this->setError('出库出现问题');
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
    public function autoOut($order, $customData = [])
    {
        $result = DB::transaction(function () use ($order, $customData) {
            $inventory = $this->inventory->where('order_number', $order->id)->OfOut()->first();
            $data = count($customData) ? $customData : [
                'inventory_number' => $inventory->inventory_number ?? $this->getInventoryNumber(),
                'inventory_type' => cons('inventory.inventory_type.system'),  //系统
                'action_type' => cons('inventory.action_type.out'),           //出库
                'user_id' => 0,
            ];
            foreach ($order->orderGoods as $goods) {
                $source = $this->_getSource($goods->type);
                $outGoods = [
                    'goods' => [
                        $goods->goods_id => [
                            'source' => [
                                $source
                            ],
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
                $result = $this->out(array_merge($data, $outGoods));
                if (!$result) {
                    $goods->inventory_state = cons('inventory.inventory_state.out-abnormal');
                    $goods->save();
                    return $result;
                }
            }
            return true;
        });
        return $result;
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
        $data['in_id'] = $inventory->id;
        //判断库存记录剩余是否足够本次出库
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
            $inventory->surplus = 0; //清空本条出库记录的剩余
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
            $goods = Goods::find($goodsId);
            $formatted[$key]['quantity'] = $detail['quantity'][$key] * GoodsService::getPiecesSystem($goods,
                    $detail['pieces'][$key]);  // 默认转为最小单位计数
            $formatted[$key]['surplus'] = $in ? $formatted[$key]['quantity'] : '0';
            $formatted[$key]['remark'] = $detail['remark'][$key];
            $formatted[$key]['source'] = $detail['source'][$key] ?? 0;
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
    static public function search(
        $inventory,
        array $data,
        array $with = [
            'goods.goodsPieces',
            'user',
            'order',
            'parent',
            'warehouseKeeper',

        ]
    ) {
        /*return*/
        $inventory->with($with)->where(function ($query) use ($data) {
            $start_at = array_get($data, 'start_at');
            $end_at = array_get($data, 'end_at');

            $query->where('created_at', '>=', $start_at);

            $query->where('created_at', '<=', $end_at);

            if (!empty($data['inventory_type']) && isset($data['inventory_type'])) {
                $query->where('inventory_type', $data['inventory_type']);
            }
            if (!empty($data['action_type']) && isset($data['action_type'])) {
                $query->where('action_type', $data['action_type']);
            }
            if (($goods = array_get($data, 'goods')) && !is_null($goods)) {
                $query->whereHas('goods', function ($query) use ($goods) {
                    $query->where(is_numeric($goods) ? 'bar_code' : 'name', 'LIKE', '%' . $goods . '%');
                });
            }
        });
        return $inventory;
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
            $goods = Goods::find($goods)->with('goodsPieces');
        }
        if (isset($goods->goodsPieces->pieces_level_1)) {
            $goodsPieces = $goods->goodsPieces->toArray();
            $lv1 = GoodsService::getPiecesSystem($goods, $goodsPieces['pieces_level_1']);
            $lv2 = GoodsService::getPiecesSystem($goods, $goodsPieces['pieces_level_2']);
            $lv3 = GoodsService::getPiecesSystem($goods, $goodsPieces['pieces_level_3']);
            if ($total % $lv1 === 0) {
                $result = $total / $lv1 . cons()->valueLang('goods.pieces',
                        $goodsPieces['pieces_level_1']);
            } else {
                if (floor($total / $lv1) > 0) {
                    $result = floor($total / $lv1) . cons()->valueLang('goods.pieces',
                            $goodsPieces['pieces_level_1']);
                }
                if (($total % $lv1) % $lv2 === 0) {
                    $result .= ($total % $lv1) / $lv2 . cons()->valueLang('goods.pieces',
                            $goodsPieces['pieces_level_2']);
                } else {
                    if (floor(($total % $lv1) / $lv2) > 0) {
                        $result .= floor(($total % $lv1) / $lv2) . cons()->valueLang('goods.pieces',
                                $goodsPieces['pieces_level_2']);
                    }
                    $result .= floor(($total % $lv1) % $lv2 / $lv3) . cons()->valueLang('goods.pieces',
                            $goodsPieces['pieces_level_3']);
                }
            }

        }
        return $result;
    }

    /**
     * 获取入库异常
     *
     * @return mixed
     */
    public function getInError()
    {
        $orderIds = [];
        auth()->user()->orders->each(function ($order) use (&$orderIds) {
            $orderIds[] = $order->id;
        });
        return OrderGoods::where('inventory_state', cons('inventory.inventory_state.in-abnormal'))->whereIn('order_id',
            $orderIds);
    }

    /**
     * 获取出库异常
     *
     * @return mixed
     */
    public function getOutError()
    {
        $orderIds = auth()->user()->shop->orders->pluck('id');
        return OrderGoods::where('inventory_state', cons('inventory.inventory_state.out-abnormal'))->whereIn('order_id',
            $orderIds);
    }

    /**
     * 清除出库记录
     *
     * @param $param
     * @return bool
     */
    public function clearOut($param)
    {
        if (is_numeric($param)) {
            $where = ['order_number' => $param];
        } else {
            $where = ['order_number' => $param->order_id, 'goods_id' => $param->goods_id];
        }
        $outRecord = $this->inventory->where($where)->OfOut()->get();
        if ($outRecord) {
            foreach ($outRecord as $record) {
                $inRecord = $this->inventory->where('id', $record->in_id)->OfIn()->first();
                $inRecord->surplus += $record->quantity;
                $inRecord->save();
                $record->delete();
            }
            return true;
        }
    }

    /**
     * 订单作废入库
     *
     * @param $order
     */
    public function orderReasonIn($order)
    {
        return $this->clearOut($order->orderGoods);
    }

    /**
     *  校验商品单位是否一致
     *
     * @param $goodsPieces1
     * @param $goodsPieces2
     * @return bool 校验结果
     *
     */
    private function _checkPieces($goodsPieces1, $goodsPieces2)
    {
        if (empty($goodsPieces1) || empty($goodsPieces2)) {
            return false;
        }
        foreach ($goodsPieces1 as $key => $value) {
            if ($goodsPieces2[$key] != $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查是否有重复的生产日期有且合并
     *
     * @param $record
     * @return mixed
     */
    public function _checkRepeated($record)
    {
        $tmpArr = $res = $result = [];
        foreach ($record as $key => $value) {
            if (!in_array($value->production_date, $tmpArr)) {
                $tmpArr[$key] = $value->production_date;
            } else {
                $res[$key] = $value->production_date;
            }
        }
        foreach ($res as $k => $v) {
            $record[array_search($v, $tmpArr)]->quantity += $record[$k]->quantity;
            unset($record[$k]);
        }
        return $record;
    }

    /**
     * 记录导出
     *
     * @param $inventory
     * @param $excelName
     */
    public function export($records, $excelName, $action)
    {
        Excel::create($excelName, function (LaravelExcelWriter $excel) use ($records, $action) {
            $excel->sheet('库存记录', function (LaravelExcelWorksheet $sheet) use ($records, $action) {

                // Set auto size for sheet
                $sheet->setAutoSize(true);
                //标题
                $titles = [
                    '商品名称',
                    '商品条形码',
                    '生产日期',
                ];
                switch ($action) {
                    case 'in' :
                        $sheet->setWidth(array(
                            'A' => 50,
                            'B' => 20,
                            'C' => 20,
                            'D' => 10,
                            'E' => 10,
                            'F' => 10,
                            'G' => 10,
                            'H' => 12,
                            'I' => 10,
                            'J' => 20,
                            'K' => 16,
                        ));
                        $titles[] = '入库数量';
                        $titles[] = '入库单价';
                        $titles[] = '入库类型';
                        $titles[] = '入库人';
                        $titles[] = '进货单号';
                        $titles[] = '供货商家';
                        $titles[] = '入库时间';
                        break;
                    case 'out' :
                        $sheet->setWidth(array(
                            'A' => 50,
                            'B' => 20,
                            'C' => 20,
                            'D' => 10,
                            'E' => 10,
                            'F' => 10,
                            'G' => 5,
                            'H' => 12,
                            'I' => 8,
                            'J' => 10,
                            'K' => 16,
                            'L' => 20,
                            'M' => 20,
                        ));
                        $titles[] = '出库数量';
                        $titles[] = '成本单价';
                        $titles[] = '出库单价';
                        $titles[] = '盈利';
                        $titles[] = '出库类型';
                        $titles[] = '出库人';
                        $titles[] = '出货单号';
                        $titles[] = '进货商家';
                        $titles[] = '出库时间';
                        break;
                    case 'detail-list' :
                        $sheet->setWidth(array(
                            'A' => 50,
                            'B' => 20,
                            'C' => 20,
                            'D' => 10,
                            'E' => 10,
                            'F' => 10,
                            'G' => 10,
                            'H' => 10,
                            'I' => 8,
                            'J' => 10,
                            'K' => 16,
                            'L' => 20,
                            'M' => 30,
                        ));
                        $titles[] = '出入库类型';
                        $titles[] = '出库数量';
                        $titles[] = '出库数量';
                        $titles[] = '成本单价';
                        $titles[] = '出库单价';
                        $titles[] = '出入库人';
                        $titles[] = '近出货单号';
                        $titles[] = '供进货商家';
                        $titles[] = '出入库时间';
                }
                $titles[] = '备注';
                $data = array_merge([$titles], $records);
                $sheet->rows($data);
                //单元格居中
                $sheet->cells('A1:E1', function (CellWriter $cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
        })->export('xls');
    }

    public function _getSource($type)
    {
        switch ($type) {
            case 0 :
                $source = cons('inventory.source.order');  //订单商品
                break;
            case 1 :
                $source = cons('inventory.source.mortgage'); // 促销商品
                break;
            case 2 :
                $source = cons('inventory.source.gift');   //赠品商品
                break;
            case 3 :
                $source = cons('inventory.source.promo');   //陈列商品
                break;
            default :
                $source = 0;
                break;
        }
        return $source;
    }

    /**
     * 仓管获取出库记录
     *
     * @param $order_id
     * @param $goods_id
     * @return mixed
     */
    public function getOutRecordByWK($order_id, $goods_id)
    {
        return $this->inventory->with('goods')->where([
            'shop_id' => wk_auth()->user()->shop_id,
            'order_number' => $order_id,
            'goods_id' => $goods_id
        ])->ofOut()->where('source', cons('inventory.source.order'))->orderBy('production_date', 'DESC')->get();
    }
}