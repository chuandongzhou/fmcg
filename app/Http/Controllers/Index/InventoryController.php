<?php

namespace App\Http\Controllers\Index;

use App\Models\Goods;
use App\Models\OrderGoods;
use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Services\InventoryService;
use App\Services\OrderService;
use Illuminate\Http\Request;

use App\Http\Requests;


class InventoryController extends Controller
{
    private $inventoryService;
    private $shop;

    public function __construct(InventoryService $inventoryService)
    {
        $this->middleware('retailer');
        $this->inventoryService = $inventoryService;
        $this->shop = auth()->user()->shop;
    }

    /**
     * 库存管理
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {

        $data = $request->only('nameOrCode', 'category_id', 'status', 'warning');

        $result = GoodsService::getShopGoods($this->shop, $data);
        $ids = [];
        $countNeedWarning = 0;
        $result['goods']->get()->each(function ($item) use (&$countNeedWarning, &$ids) {
            if ($item->need_warning) {
                $countNeedWarning++;
                $ids[] = $item->id;
            }
        });
        if ($data['warning']) {
            $goods = Goods::whereIn('id', $ids)->orderBy('updated_at', 'DESC')->paginate();
        } else {
            $goods = $result['goods']->orderBy('updated_at', 'DESC')->paginate();
        }

        $goodsCate = [];
        foreach (CategoryService::getCategories() as $key => $cate) {
            foreach ($goods as $item) {
                if ($item->cate_level_1 && $key == $item->cate_level_1) {
                    $goodsCate[$item->id]['cate_level_1'] = $cate['name'];
                }
                if ($item->cate_level_2 && $key == $item->cate_level_2) {
                    $goodsCate[$item->id]['cate_level_2'] = $cate['name'];
                }
                if ($item->cate_level_3 && $key == $item->cate_level_3) {
                    $goodsCate[$item->id]['cate_level_3'] = $cate['name'];
                }
            }
        }
        $cateId = isset($data['category_id']) ? $data['category_id'] : -1;
        $categories = CategoryService::formatShopGoodsCate($this->shop, $cateId);
        $searched = $result['searched'];
        return view('index.inventory.manage', compact(
            'countNeedWarning',
            'goods',
            'data',
            'goodsCate',
            'categories',
            'searched'
        ));
    }

    /**
     *  入库列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIn(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'goods');
        $inventory = $this->shop->inventory()->OfIn()->orderBy('created_at', 'desc');
        $result = $this->inventoryService->search($inventory, $data, ['user'], true);
        $inError = $this->inventoryService->getInError();
        return view('index.inventory.in', [
            'lists' => $result->paginate(),
            'data' => $data,
            'errorCount' => $inError->count()
        ]);
    }

    /**
     *  入库信息填写
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getInCreate(Request $request, $goods_id = 0)
    {
        $shopGoodsModel = $this->shop->goods()->with('goodsPieces');
        $data = [
            'inventory_number' => $this->inventoryService->getInventoryNumber(),
            'type' => cons('inventory.inventory_type.manual'),
        ];
        $outRecord = null;
        $order_id = $request->input('order_id');
        //订单ID存在为处理异常
        if ($order_id) {
            //拿到卖家商品信息
            $sellerGoods = Goods::find($goods_id);
            //查询商品库得到对应商品
            $goods = $shopGoodsModel->where('bar_code', $sellerGoods->bar_code)->first();
            //拿到出库记录
            $outRecord = $sellerGoods->inventory()->OfOut()->where('order_number', $order_id)->get();
            $outRecord = $outRecord->isEmpty() ? null : $this->inventoryService->_checkRepeated($outRecord);
        } else {
            $goods = $shopGoodsModel->find($goods_id);
        }
        return view('index.inventory.in-create', [
            'inventory' => $data,
            'goods' => $goods,
            'outRecord' => $outRecord,
            'orderId' => $order_id,
            'goodsId' => $goods_id
        ]);
    }

    /**
     *  出库列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOut(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'goods');
        $inventory = $this->shop->inventory()->OfOut()->orderBy('created_at', 'desc');
        $result = $this->inventoryService->search($inventory, $data, ['user'], true);
        $errorCount = $this->inventoryService->getOutError();
        $countProfit = $result->get()->sum('profit');
        return view('index.inventory.out', [
            'lists' => $result->paginate(),
            'countProfit' => $countProfit,
            'data' => $data,
            'errorCount' => $errorCount->count()
        ]);
    }

    /**
     * 出库信息填写
     *
     * @param int $goods_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOutCreate($goods_id = 0)
    {
        $goods = $this->shop->goods()->with('goodsPieces')->find($goods_id);
        $data = [
            'inventory_number' => $this->inventoryService->getInventoryNumber(),
            'type' => cons('inventory.inventory_type.manual'),
        ];
        return view('index.inventory.out-create', [
            'inventory' => $data,
            'goods' => $goods
        ]);
    }

    /**
     * 出入库明细列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDetailList(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'goods', 'inventory_type', 'action_type');
        $inventory = $this->shop->inventory()->orderBy('created_at', 'desc');
        $lists = $this->inventoryService->search($inventory, $data, ['user'], true);
        //dd($lists->paginate());
        return view('index.inventory.detail-list', [
            'lists' => $lists->paginate(),
            'data' => $data
        ]);
    }

    /**
     * 入库详情
     *
     * @param $inventory_number
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getInDetail($inventory_number)
    {
        $inventory = $this->shop->inventory()->OfIn()->where('inventory_number', $inventory_number)->with([
            'goods',
            'user'
        ])->paginate();
        if (!(count($inventory))) {
            return view('errors.404');
        }
        return view('index.inventory.in-detail', [
            'inventory' => $inventory
        ]);
    }

    /**
     * 出库详情
     *
     * @param $inventory_number
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOutDetail($inventory_number = 0)
    {
        $inventory = $this->shop->inventory()->OfOut()->where([
            'inventory_number' => $inventory_number
        ])->paginate();

        if (!count($inventory)) {
            return view('errors.404');
        }
        $inventory->each(function ($item) {
            $item->setAppends(['profit']);
        });
        return view('index.inventory.out-detail', [
            'inventory' => $inventory,
            'total_profit' => sprintf("%10.2f", $inventory->sum('profit')),
        ]);
    }


    /**
     * 商品出入库明细
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getGoodsInventoryDetail(Request $request, $goodsId)
    {

        $data = $request->only('start_at', 'end_at');
        $lists = $this->shop->inventory()->where(function ($query) use ($data) {
            if (!empty($data['start_at']) && isset($data['start_at'])) {
                $query->where('created_at', '>=', $data['start_at']);
            }
            if (!empty($data['end_at']) && isset($data['end_at'])) {
                $query->where('created_at', '<=', $data['end_at']);
            }
            if (empty($data['end_at']) && !isset($data['end_at']) && empty($data['start_at']) && !isset($data['start_at'])) {
                $query->OfNowMonth();
            }
        })->where('goods_id', $goodsId)->orderBy('created_at', 'desc')->paginate();
        $goods = Goods::find($goodsId);
        return view('index.inventory.goods-inventory-detail', [
            'goods' => $goods,
            'lists' => $lists,
            'data' => $data
        ]);
    }

    /**
     * 在途商品列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getInTransitGoods()
    {
        $orderIds = OrderService::getInTransitOrderIds();
        $inTransitGoods = [];
        if ($orderIds) {
            $data = OrderGoods::whereIn('order_id', $orderIds)->with(['goods']);
            $_inTransitGoods = $data->get()->groupBy('goods_id');
            $inTransitGoods = $data->groupBy('goods_id')->paginate();
            foreach ($inTransitGoods as $orderGoods) {
                $orderGoods->count = $this->inventoryService->calculateQuantity($orderGoods->goods_id,
                    $_inTransitGoods[$orderGoods->goods_id]->sum('quantity'));
            }
        }
        return view('index.inventory.in-transit-goods', [
            'inTransitGoods' => $inTransitGoods,
        ]);
    }

    /**
     * 在途商品详情
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getInTransitGoodsDetail($goodsId = 0)
    {
        if ($goodsId <= 0) {
            return redirect()->to(url('inventory/in-transit-goods'));
        }
        $orderIds = OrderService::getInTransitOrderIds();

        $goodsOutDetail = $this->shop->inventory()->with('goods')->OfOut()->where('goods_id',
            $goodsId)->whereIn('order_number', $orderIds)->paginate();
        $inTransitTotal = $this->inventoryService->calculateQuantity($goodsId, $goodsOutDetail->sum('quantity'));
        return view('index.inventory.in-transit-goods-detail', [
            'goodsOutDetail' => $goodsOutDetail,
            'inTransitTotal' => $inTransitTotal
        ]);
    }

    /**
     * 入库错误列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getInError()
    {
        $inError = $this->inventoryService->getInError();
        return view('index.inventory.in-error', [
            'errorLists' => $inError->paginate(),
            'shopGoods' => $this->shop->goods
        ]);
    }

    /**
     * 出库错误列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOutError()
    {
        $outError = $this->inventoryService->getOutError();
        return view('index.inventory.out-error', [
            'errorLists' => $outError->paginate()
        ]);
    }

    /**
     * 入库记录导出
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|void|\WeiHeng\Responses\IndexResponse
     */
    public function inExport(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'goods');
        $inventory = $this->shop->inventory()->OfIn()->orderBy('created_at', 'desc');
        $result = $this->inventoryService->search($inventory, $data, ['user'], true)->get();
        if ($result->isEmpty()) {
            return $this->error('没有数据!');
        }
        $records = [];
        $result->each(function ($item) use (&$records) {
            $records[] = [
                $item->goods_name,
                $item->goods_bar_code,
                $item->production_date,
                $item->transformation_quantity,
                $item->cost . ' / ' . cons()->valueLang('goods.pieces', $item->pieces),
                cons()->valueLang('inventory.inventory_type',
                    $item->inventory_type) . cons()->valueLang('inventory.action_type', $item->action_type),
                $item->user->user_name ?? '系统',
                $item->order_number > 0 ? $item->order_number : '---',
                $item->seller_name ?? '---',
                $item->created_at ?? '',
                $item->remark ?? '',
            ];
        });
        $start_at = array_get($data, 'start_at');
        $start_at = array_get($data, 'end_at');
        $goods = array_get($data, 'goods');
        $preName = '';
        return $this->inventoryService->export($records, '入库记录', 'in');
    }

    /**
     * 出库记录导出
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|void|\WeiHeng\Responses\IndexResponse
     */
    public function outExport(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'goods');
        $inventory = $this->shop->inventory()->ofOut()->orderBy('created_at', 'desc');
        $result = $this->inventoryService->search($inventory, $data, ['user'], true)->get();

        if ($result->isEmpty()) {
            return $this->error('没有数据!');
        }
        $records = [];
        $result->each(function ($item) use (&$records) {
            $records[] = [
                $item->goods_name,
                $item->goods_bar_code,
                $item->production_date,
                $item->transformation_quantity,
                $item->in_cost . ' / ' . cons()->valueLang('goods.pieces', $item->in_pieces),
                $item->cost . ' / ' . cons()->valueLang('goods.pieces', $item->pieces),
                ($item->profit > 0 ? '+' : '') . $item->profit,
                cons()->valueLang('inventory.inventory_type',
                    $item->inventory_type) . cons()->valueLang('inventory.action_type', $item->action_type),
                $item->user->user_name ?? '系统',
                $item->order_number > 0 ? $item->order_number : '---',
                $item->buyer_name ?? '---',
                $item->created_at ?? '',
                $item->remark ?? '',
            ];
        });
        return $this->inventoryService->export($records, '出库记录', 'out');
    }


    public function detailListExport(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'goods');
        $inventory = $this->shop->inventory()->orderBy('created_at', 'desc');
        $result = $this->inventoryService->search($inventory, $data, ['user'], true)->get();

        if ($result->isEmpty()) {
            return $this->error('没有数据!');
        }
        $records = [];
        $result->each(function ($item) use (&$records) {
            $records[] = [
                $item->goods_name,
                $item->goods_bar_code,
                $item->production_date,
                cons()->valueLang('inventory.inventory_type',
                    $item->inventory_type) . cons()->valueLang('inventory.action_type', $item->action_type),
                $item->action_type == cons('inventory.action_type.in') ? $item->transformation_quantity : '--',
                $item->action_type == cons('inventory.action_type.out') ? $item->transformation_quantity : '--',
                $item->action_type == cons('inventory.action_type.out') ? $item->in_cost . '/' . cons()->valueLang('goods.pieces',
                        $item->in_pieces) : $item->cost . '/' . cons()->valueLang('goods.pieces', $item->pieces),
                $item->action_type == cons('inventory.action_type.out') ? $item->cost . '/' . cons()->valueLang('goods.pieces',
                        $item->pieces) : '---',
                $item->user->user_name ?? '系统',
                $item->order_number > 0 ? $item->order_number : '---',
                $item->action_type == cons('inventory.action_type.out') ? ($item->buyer_name ?? '---') : ($item->saller_name ?? '---'),
                $item->created_at,
                $item->remark,
            ];
        });
        return $this->inventoryService->export($records, '出入库记录', 'detail-list');
    }
}
