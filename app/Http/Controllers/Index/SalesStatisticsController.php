<?php

namespace App\Http\Controllers\Index;

use App\Models\GoodsPieces;
use App\Models\Salesman;
use App\Services\GoodsService;
use App\Services\OrderService;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;


class SalesStatisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $startAt = array_get($data, 'start_at');
        $endAt = array_get($data, 'end_at');

        $user = auth()->user();

        $query = $user->shop->orderGoods()->ofTime($startAt, $endAt)->whereHas('order', function ($query) {
            return $query->useful()->nonRefund()->whereIn('status', [1, 2, 3]);
        })->with(['goods', 'order.shop']);

        if (!is_null($goodsType = array_get($data, 'goods_type'))) {
            $query->where('order_goods.type', $goodsType);
        }
        if (!is_null($orderType = array_get($data, 'order_type'))) {
            $query->whereHas('order', function ($query) use ($orderType) {
                return $query->where('type', $orderType);
            });
        }

        if ($salesmanId = array_get($data, 'salesman')) {
            $query->with('order.salesmanVisitOrder')->whereHas('order.salesmanVisitOrder',
                function ($query) use ($salesmanId) {
                    return $query->where('salesman_id', $salesmanId);
                });


        }

        $searchType = array_get($data, 'search_type');
        if (($value = array_get($data, 'value')) && $searchType) {
            if ($searchType == 'goods') {
                $query->whereHas('goods', function ($query) use ($value) {
                    return $query->ofGoodsName($value);
                });
            } else {
                $query->whereHas('order.shop', function ($query) use ($value) {
                    return $query->ofName($value);
                });
            }
        }

        $goods = $query->orderBy('order_goods.created_at', 'desc')->get();

        if (is_null($goodsType)) {
            $goodsList = (new OrderService())->explodeOrderGoods($goods);
            $orderGoods = $this->_formatGoodsNum($this->_formatGoods($goodsList['orderGoods']));
            $mortgageGoods =  $this->_formatGoodsNum($this->_formatGoods($goodsList['mortgageGoods']));
            $giftGoods =  $this->_formatGoodsNum($this->_formatGoods($goodsList['giftGoods']));

        } else {
            $orderGoods =  $this->_formatGoodsNum($this->_formatGoods($goods));
        }

        //需要转换单位的商品

        $goodsPieces = GoodsPieces::whereIn('goods_id', array_keys($orderGoods))->get()->toArray();

        foreach ($goodsPieces as $piece) {
            if ($goodsItem = array_get($orderGoods, $piece['goods_id'])) {
                $orderGoods[$piece['goods_id']]['num'] = GoodsService::formatGoodsPieces($piece, $goodsItem['num']);
            }
        }

        $salesmen = $user->shop->salesmen;

        return view('index.sales-statistics.index',
            compact('salesmen', 'salesmanId', 'startAt', 'endAt', 'goodsType', 'orderType', 'searchType', 'value',
                'orderGoods', 'mortgageGoods', 'giftGoods'));
    }

    /**
     * 导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function export(Request $request)
    {
        $data = $request->all();
        $startAt = array_get($data, 'start_at');
        $endAt = array_get($data, 'end_at');

        $query = auth()->user()->shop->orderGoods()->ofTime($startAt, $endAt)->whereHas('order', function ($query) {
            return $query->useful()->nonRefund()->whereIn('status', [1, 2, 3]);
        })->with(['goods', 'order.shop']);

        if (!is_null($goodsType = array_get($data, 'goods_type'))) {
            $query->where('order_goods.type', $goodsType);
        }
        if (!is_null($orderType = array_get($data, 'order_type'))) {
            $query->whereHas('order', function ($query) use ($orderType) {
                return $query->where('type', $orderType);
            });
        }

        if ($salesmanId = array_get($data, 'salesman')) {
            $query->with('order.salesmanVisitOrder')->whereHas('order.salesmanVisitOrder',
                function ($query) use ($salesmanId) {
                    return $query->where('salesman_id', $salesmanId);
                });
        }

        $searchType = array_get($data, 'search_type');
        if (($value = array_get($data, 'value')) && $searchType) {
            if ($searchType == 'goods') {
                $query->whereHas('goods', function ($query) use ($value) {
                    return $query->ofGoodsName($value);
                });
            } else {
                $query->whereHas('order.shop', function ($query) use ($value) {
                    return $query->ofName($value);
                });
            }
        }

        $goods = $query->orderBy('order_goods.created_at', 'desc')->get();

        $mortgageGoods = null;
        $giftGoods = null;
        if (is_null($goodsType)) {
            $goodsList = (new OrderService())->explodeOrderGoods($goods);
            $orderGoods = $this->_formatGoodsNum($this->_formatGoods($goodsList['orderGoods']));
            $mortgageGoods =  $this->_formatGoodsNum($this->_formatGoods($goodsList['mortgageGoods']));
            $giftGoods =  $this->_formatGoodsNum($this->_formatGoods($goodsList['giftGoods']));

        } else {
            $orderGoods =  $this->_formatGoodsNum($this->_formatGoods($goods));
        }


        Excel::create('商品销售统计', function ($excel) use ($orderGoods, $goodsType, $mortgageGoods, $giftGoods, $request) {

            $excel->sheet('筛选条件', function (LaravelExcelWorksheet $sheet) use ($request) {
                $sheet->setWidth(array(
                    'A' => 30,
                    'B' => 20,
                    'D' => 20,
                    'E' => 20,
                ));

                $sheet->row(1, ['时间', '业务员名称', '商户名称', '订单类型'])->setStyle([
                    'alignment' => ['vertical' => 'center']
                ]);


                if ($salesmanId = $request->input('salesman')) {
                    $salesman = Salesman::find($salesmanId);
                }

                $sheet->row(2, [
                    $request->input('start_at', '--') . '至' . $request->input('end_at', '--'),
                    isset($salesman) && !is_null($salesman) ? $salesman->name : '--',
                    $request->input('search_type') == 'shops' ? $request->input('value') : '--',
                    $this->_getOrderType($request->input('order_type'))
                ])->setStyle([
                        'alignment' => ['vertical' => 'center']
                    ]);
            });

            $excel->sheet($this->_getSheetName($goodsType), function (LaravelExcelWorksheet $sheet) use ($orderGoods) {
                $sheet->setWidth(array(
                    'A' => 20,
                    'B' => 20,
                    'C' => 60,
                    'D' => 20,
                    'E' => 20,
                ));

                $sheet->row(1, ['商品ID', '商品条形码', '商品名称', '销售数量', '销售金额'])->setStyle([
                    'alignment' => ['vertical' => 'center']
                ]);

                foreach ($orderGoods as $goods) {
                    $salesNum = $this->_getGoodsSalesNum($goods['num']);
                    $sheet->appendRow([
                        $goods['id'],
                        $goods['barcode'],
                        $goods['name'],
                        $salesNum,
                        $goods['amount']
                    ])->setStyle([
                        'alignment' => ['vertical' => 'center']
                    ]);;
                }
            });

            if (is_null($goodsType)) {
                $excel->sheet('陈列统计', function (LaravelExcelWorksheet $sheet) use ($mortgageGoods) {

                    $sheet->setWidth(array(
                        'A' => 20,
                        'B' => 20,
                        'C' => 60,
                        'D' => 20,
                        'E' => 20,
                    ));

                    $sheet->row(1, ['商品ID', '商品条形码', '商品名称', '销售数量', '销售金额'])->setStyle([
                        'alignment' => ['vertical' => 'center']
                    ]);

                    foreach ($mortgageGoods as $goods) {
                        $salesNum = $this->_getGoodsSalesNum($goods['num']);
                        $sheet->appendRow([
                            $goods['id'],
                            $goods['barcode'],
                            $goods['name'],
                            $salesNum,
                            $goods['amount']
                        ])->setStyle([
                            'alignment' => ['vertical' => 'center']
                        ]);;
                    }
                });

                $excel->sheet('赠品统计', function (LaravelExcelWorksheet $sheet) use ($giftGoods) {
                    $sheet->setWidth(array(
                        'A' => 20,
                        'B' => 20,
                        'C' => 60,
                        'D' => 20,
                        'E' => 20,
                    ));

                    $sheet->row(1, ['商品ID', '商品条形码', '商品名称', '销售数量', '销售金额'])->setStyle([
                        'alignment' => ['vertical' => 'center']
                    ]);

                    foreach ($giftGoods as $goods) {
                        $salesNum = $this->_getGoodsSalesNum($goods['num']);
                        $sheet->appendRow([
                            $goods['id'],
                            $goods['barcode'],
                            $goods['name'],
                            $salesNum,
                            $goods['amount']
                        ])->setStyle([
                            'alignment' => ['vertical' => 'center']
                        ]);;
                    }
                });
            }
        })->export('xls');

    }


    /**
     * 格式化商品
     *
     * @param \Illuminate\Support\Collection $goods
     * @return array
     */
    private function _formatGoods(Collection $goods)
    {
        $data = [];
        foreach ($goods as $item) {
            if (isset($data[$item->goods_id])) {
                $data[$item->goods_id]['num'][$item->pieces] = isset($data[$item->goods_id]['num'][$item->pieces]) ? $data[$item->goods_id]['num'][$item->pieces] + $item->num : $item->num;
            } else {
                $data[$item->goods_id] = [
                    'id' => $item->goods_id,
                    'barcode' => $item->goods->bar_code,
                    'name' => $item->goods->name,
                    'amount' => $item->total_price,
                    'num' => [
                        $item->pieces => $item->num
                    ]
                ];
                continue;
            }
            $data[$item->goods_id]['amount'] = bcadd($data[$item->goods_id]['amount'], $item->total_price, 2);
        }
        return $data;
    }

    /**
     * 获取销售数量
     *
     * @param $goodsNum
     * @return string
     */
    private function _getGoodsSalesNum($goodsNum)
    {
        if (!is_array($goodsNum)) {
            return '';
        }
        $goodsSalesNum = '';
        foreach ($goodsNum as $pieces => $num) {
            $goodsSalesNum .= $num . cons()->valueLang('goods.pieces', $pieces);
        }

        return $goodsSalesNum;
    }

    /**
     * 获取sheet名
     *
     * @param $goodsType
     * @return string
     */
    private function _getSheetName($goodsType)
    {
        if (is_null($goodsType) || $goodsType === '0') {
            return '商品销售统计';
        }
        if ($goodsType === '1') {
            return '陈列统计';
        }
        if ($goodsType === '2') {
            return '赠品统计';
        }
    }

    /**
     * 获取订单类型
     *
     * @param $orderType
     * @return string
     */
    private function _getOrderType($orderType)
    {
        if (is_null($orderType)) {
            return '全部';
        }
        if ($orderType === '0') {
            return '自主订单';
        }
        if ($orderType === '1') {
            return '业务订单';
        }
    }


    /**
     * 商品单位换算
     *
     * @param $orderGoods
     * @return mixed
     */
    private function _formatGoodsNum($orderGoods)
    {

        //需要转换单位的商品

        $goodsPieces = GoodsPieces::whereIn('goods_id', array_keys($orderGoods))->get()->toArray();

        foreach ($goodsPieces as $piece) {
            if ($goodsItem = array_get($orderGoods, $piece['goods_id'])) {
                $orderGoods[$piece['goods_id']]['num'] = GoodsService::formatGoodsPieces($piece, $goodsItem['num']);
            }
        }
        return $orderGoods;
    }
}
