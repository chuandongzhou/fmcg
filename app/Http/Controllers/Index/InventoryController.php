<?php

namespace App\Http\Controllers\Index;

use App\Models\Goods;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Services\InventoryService;
use App\Services\OrderService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;


class InventoryController extends Controller
{
    /**
     * 库存管理
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $data = $request->only('nameOrCode', 'category_id');

        $shop = auth()->user()->shop;

        $result = GoodsService::getShopGoods($shop, $data);
        $goods = $result['goods']->orderBy('updated_at', 'DESC')->paginate();

        $cate = [];
        foreach (CategoryService::getCategories() as $key => $category) {

            foreach ($goods as $item) {
                if ($item->cate_level_1 && $key == $item->cate_level_1) {
                    $cate[$item->id]['cate_level_1'] = $category['name'];
                }
                if ($item->cate_level_2 && $key == $item->cate_level_2) {
                    $cate[$item->id]['cate_level_2'] = $category['name'];
                }
                if ($item->cate_level_3 && $key == $item->cate_level_3) {
                    $cate[$item->id]['cate_level_3'] = $category['name'];
                }
            }
        }
        $cateId = isset($data['category_id']) ? $data['category_id'] : -1;
        $categories = CategoryService::formatShopGoodsCate($shop, $cateId);
        $searched = $result['searched'];
        return view('index.inventory.manage', compact(
            'goods',
            'data',
            'cate',
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
        $inventory = Inventory::with(['user'])->OfIn()->groupBy('inventory_number')->orderBy('created_at', 'desc');
        $result = InventoryService::search($inventory, $data);

        return view('index.inventory.in', [
            'lists' => $result->paginate(),
            'data' => $data
        ]);
    }

    /**
     *  入库信息填写
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getInCreate($goods_id = 0)
    {
        $goods = auth()->user()->shop->goods()->with('goodsPieces')->find($goods_id);
        $data = [
            'inventory_number' => (new InventoryService())->getInventoryNumber(),
            'type' => cons('inventory.inventory_type.manual'),
        ];

        session(['inv' => $data]);

        return view('index.inventory.in-create', [
            'inventory' => $data,
            'goods' => $goods
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
        $inventory = Inventory::with(['user'])->OfOut()->groupBy('inventory_number')->orderBy('created_at', 'desc');
        $result = InventoryService::search($inventory, $data);
        return view('index.inventory.out', [
            'lists' => $result->paginate(),
            'data' => $data
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
        $goods = auth()->user()->shop->goods()->with('goodsPieces')->find($goods_id);
        $data = [
            'inventory_number' => (new InventoryService())->getInventoryNumber(),
            'type' => cons('inventory.inventory_type.manual'),
        ];

        session(['inv' => $data]);

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
        $inventory = Inventory::with(['user'])->groupBy('inventory_number')->orderBy('created_at', 'desc');
        $lists = InventoryService::search($inventory, $data);
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
        $inventory = Inventory::OfIn();
        $result = InventoryService::search($inventory, [
            'number' => $inventory_number
        ], ['goods', 'user']);
        return view('index.inventory.in-detail', [
            'inventory' => $result->paginate()
        ]);
    }

    /**
     * 出库详情
     *
     * @param $inventory_number
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOutDetail($inventory_number)
    {
        $inventory = Inventory::OfOut()->where([
            'inventory_number' => $inventory_number
        ])->paginate();
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
            foreach ($inTransitGoods as $value) {
                $goods = Goods::find($value->goods_id);
                $value->count = InventoryService::calculateQuantity($goods,
                    $_inTransitGoods[$value->goods_id]->sum('quantity'));
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
        $goodsOutDetail = Inventory::with('goods')->OfOut()->where('goods_id', $goodsId)->whereIn('order_number',
            $orderIds)->paginate();
        $inTransitTotal = InventoryService::calculateQuantity($goodsId, $goodsOutDetail->sum('quantity'));
        return view('index.inventory.in-transit-goods-detail', [
            'goodsOutDetail' => $goodsOutDetail,
            'inTransitTotal' => $inTransitTotal
        ]);
    }

    public function getInError()
    {

        return view('index.inventory.in-error');
    }

    public function getOutError()
    {

        return view('index.inventory.out-error');
    }

}
