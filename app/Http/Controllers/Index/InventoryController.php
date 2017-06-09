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

    public function __construct()
    {
        $this->inventoryService = new InventoryService();
        $this->shop = auth()->user()->shop;
    }

    /**
     * 库存管理
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {

        $data = $request->only('nameOrCode', 'category_id');

        $result = GoodsService::getShopGoods($this->shop, $data);
        $goods = $result['goods']->orderBy('updated_at', 'DESC')->paginate();

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
        $data = $request->only('start_at', 'end_at', 'number');
        $inventory = $this->shop->inventory()->OfIn()->groupBy('inventory_number')->orderBy('created_at', 'desc');
        $result = $this->inventoryService->search($inventory, $data, ['user'], true);
        $errorCount = $this->inventoryService->getInError();
        return view('index.inventory.in', [
            'lists' => $result->paginate(),
            'data' => $data,
            'errorCount' => $errorCount->count()
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
        //订单ID存在为处理异常
        if ($order_id = $request->input('order_id')) {
            //拿到卖家商品信息
            $sellerGoods = Goods::find($goods_id);
            //查询商品库得到对应商品
            $goods = $shopGoodsModel->where('bar_code', $sellerGoods->bar_code)->first();
            $outRecord = $this->inventoryService->_checkRepeated($sellerGoods->inventory()->OfOut()->where('order_number',
                $order_id)->get());
        } else {
            $goods = $shopGoodsModel->find($goods_id);
        }
        return view('index.inventory.in-create', [
            'inventory' => $data,
            'goods' => $goods,
            'outRecord' => $outRecord
        ]);
    }

    /**
     *  出库列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOut(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'number');
        $inventory = $this->shop->inventory()->OfOut()->groupBy('inventory_number')->orderBy('created_at', 'desc');
        $result = $this->inventoryService->search($inventory, $data, ['user'], true);
        $errorCount = $this->inventoryService->getOutError();
        return view('index.inventory.out', [
            'lists' => $result->paginate(),
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
        $data = $request->only('start_at', 'end_at', 'number', 'inventory_type', 'action_type');
        $inventory = $this->shop->inventory()->groupBy('inventory_number')->orderBy('created_at', 'desc');
        $lists = $this->inventoryService->search($inventory, $data, ['user'], true);
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
        $inventory = $this->shop->inventory()->OfIn();
        $result = $this->inventoryService->search($inventory, [
            'number' => $inventory_number
        ], ['goods', 'user'])->paginate();
        if (!(count($result))) {
            return view('errors.404');
        }
        return view('index.inventory.in-detail', [
            'inventory' => $result
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
        $goods = Goods::with([
            'inventory' => function ($query) use ($data) {
                if (!empty($data['start_at']) && isset($data['start_at'])) {
                    $query->where('created_at', '>=', $data['start_at']);
                }
                if (!empty($data['end_at']) && isset($data['end_at'])) {
                    $query->where('created_at', '<=', $data['end_at']);
                }
                if (empty($data['end_at']) && !isset($data['end_at']) && empty($data['start_at']) && isset($data['start_at'])) {
                    $query->OfNowMonth();
                }
                $query->VerifyShop()->orderBy('created_at', 'desc')->paginate();
            }
        ])->find($goodsId);
        return view('index.inventory.goods-inventory-detail', [
            'goods' => $goods,
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

}
