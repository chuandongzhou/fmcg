<?php

namespace App\Http\Controllers\Index;

use App\Services\PromoService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Gate;

class PromoController extends Controller
{
    private $promoService;
    protected $shop;

    public function __construct()
    {
        $this->middleware('forbid:retailer,wholesaler,supplier');
        $this->promoService = new PromoService();
        $this->shop = auth()->user()->shop;
    }

    /**
     * 促销设置
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSetting(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'number_name');
        $promo = $this->shop->promo()->with([
            'rebate.goods',
            'condition.goods',
            'apply.order.order',
            'apply.salesman.orders.order'
        ]);
        $promos = $this->promoService->search($promo, $data)->paginate();
        return view('index.promo.setting', [
            'promos' => $promos,
            'data' => $data
        ]);
    }

    /**
     * 促销编辑
     *
     * @param $promo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function edit($promo)
    {
        if (Gate::denies('validate-shop-promo', $promo)) {
            return view('errors.404');
        }
        return view('index.promo.add', [
            'promo' => $promo->load(['rebate.goods.goodsPieces', 'condition.goods.goodsPieces'])
        ]);
    }

    /**
     * 促销查看
     *
     * @param $promo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function view($promo)
    {
        if (Gate::denies('validate-shop-promo', $promo)) {
            return view('errors.404');
        }
        return view('index.promo.add', [
            'promo' => $promo->load(['rebate.goods.goodsPieces', 'condition.goods.goodsPieces'])
        ]);
    }

    /**
     * 促销添加
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAdd()
    {
        return view('index.promo.add');
    }

    /**
     * 促销商品
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getGoods()
    {
        return view('index.promo.goods', [
            'promoGoods' => $this->shop->promoGoods()->with(['goods.goodsPieces'])->paginate()
        ]);
    }

    /**
     * 促销申请记录
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getApplyLog(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'number_name', 'status', 'salesman');
        $promoApply = $this->shop->promoApply()->with([
            'promo.rebate.goods',
            'promo.condition.goods',
            'salesman',
            'client'
        ]);
        $promoApply = $this->promoService->applyLogSearch($promoApply, $data);
        return view('index.promo.apply-log', [
            'promoApply' => $promoApply->paginate(),
            'salesmans' => $this->shop->salesmen ?? [],
            'data' => $data,
        ]);
    }

    /**
     * 申请详情
     *
     * @param $apply
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function applyLogDetail($apply)
    {
        if (Gate::denies('validate-shop-promoApply', $apply)) {
            return view('errors.404');
        }
        return view('index.promo.log-detail', [
            'apply' => $apply,
        ]);
    }

    /**
     * 促销参与情况
     *
     * @param $promo
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function partake($promo)
    {
        if (Gate::denies('validate-shop-promo', $promo)) {
            return view('errors.404');
        }
        return view('index.promo.partake')->with([
            'promo' => $promo->load([
                'apply.client.shop',
                'apply.salesman',
                'apply.order.order'
            ])
        ]);
    }
}
