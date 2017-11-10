<?php

namespace App\Http\Controllers\Index;

use App\Models\AddressData;
use App\Models\Advert;
use App\Models\Goods;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Services\AddressService;
use App\Services\GoodsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $addressData = (new AddressService())->getAddressData();
        $data = array_except($addressData, 'address_name');
        $adverts = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime()->ofAddress($data, true)->get();
        return view('index.index.index', [
            'goodsColumns' => GoodsService::getNewGoodsColumn(),
            'adverts' => $adverts,
        ]);
    }

    /**
     * 关于我们
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function about()
    {
        return view('index.index.about');
    }

    /**
     * app下载
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function download()
    {
        return view('index.index.download');
    }

    public function test(Request $request)
    {

        dd($request->server());

        dd('test');
        return view('index.index.test');
        $agentPay = app('union.pay')->batchTransfer();

        dd($agentPay);

        /*
        $companyAdd = app('union.pay')->subCompanyAdd();
        dd($companyAdd);*/

        $order = Order::find(324);
        $data = $request->all();
        $result = app('union.pay')->pay($order, $data['pay_type'], $data['sub_pay_type']);


        dd($result);

        /* delivery_auth()->loginUsingId(1);

         return view('index.index.test');*/

        /* dd(session(auth()->getName()));
         dd($request->cookie(auth()->getRecallerName()));*/

        $first = ['02', '05', '13', '22', '27', '32'];
        $second = ['03', '05', '11', '22', '29', '31'];

        dd(array_intersect($first, $second));
        //$this->changeTest($request);
        $shopId = $request->input('from', -1);
        $toShopId = $request->input('to', -1);
        $shop = Shop::find($shopId);
        $toShop = Shop::find($toShopId);
        if (is_null($shop) || is_null($toShop)) {
            dd('店铺不存在');
        }
        $shopDelivery = $toShop->deliveryArea->toArray();
        $areas = [];
        foreach ($shopDelivery as $data) {
            unset($data['coordinate']);
            $areasModal = new AddressData($data);
            if (!in_array($areasModal, $areas)) {
                $areas[] = $areasModal;
            }
        }
        $goods = $shop->goods()->with('goodsPieces')->paginate(100);

        if ($goods->count() == 0) {
            dd('完了');
        }
        foreach ($goods as $item) {
            $item->load('attr');
            $result = $this->buildGoodsData($item);
            $newGoods = $toShop->goods()->create($result['data']);
            if ($newGoods->exists) {
                $newGoods->goodsPieces()->create($result['goodsPieces']);
                $newGoods->attr()->sync($result['attr']);
                $newGoods->deliveryArea()->saveMany($areas);
            }
        }
    }


    private function changeTest(Request $request)
    {
        $shopId = $request->input('shop_id');
        $shop = Shop::find($shopId);
        if (is_null($shop)) {
            dd('店铺不存在');
        }
        $shopType = $shop->user_type;

        $goods = $shop->goods()->with('goodsPieces')->paginate(100);

        foreach ($goods as $item) {
            $tag = false;
            if ($item->specification_retailer != ($specificationRetailer = GoodsService::getPiecesSystem2($item,
                    $item->pieces_retailer))
            ) {
                $item->specification_retailer = $specificationRetailer;
                $tag = true;
            }

            if ($shopType == 3) {
                if ($item->specification_wholesaler != ($specificationWholesaler = GoodsService::getPiecesSystem2($item,
                        $item->pieces_wholesaler))
                ) {
                    $item->specification_wholesaler = $specificationWholesaler;
                    $tag = true;
                }
            }
            if ($tag) {
                $item->save();
            }
        }
        return view('index.index.test', compact('goods'));
    }

    private function buildGoodsData(Goods $goods)
    {
        $data = $goods->toArray();

        $goodsPieces = array_except(array_get($data, 'goods_pieces'), ['id', 'goods_id']);

        $attr = [];
        foreach ($data['attr'] as $item) {
            $attr[$item['attr_id']] = ['attr_pid' => $item['pid']];
        }
        return compact('data', 'goodsPieces', 'attr');
    }

}
