<?php

namespace App\Http\Controllers\Index;

use App\Models\Promo;
use App\Models\PromoGoods;
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
        $this->promoService = new PromoService();
        $this->shop = auth()->user()->shop;
    }

    /**
     * 促销设置
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSetting(Request $request)
    {
        $data = $request->only('start_at','end_at','number_name');
        $promo = $this->shop->promo()->with(['condition','rebate']);
        $promo = $this->promoService->search($promo,$data);
        return view('index.promo.setting',[
            'promos' => $promo->paginate(),
            'data' => $data
        ]);
    }

    /**
     * 促销编辑
     * @param $promo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function edit($promo)
    {
        if (Gate::denies('validate-shop-promo', $promo)) {
            return $this->error('没有权限!');
        }
        return view('index.promo.add',[
            'promo' => $promo
        ]);
    }

    /**
     * 促销查看
     * @param $promo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function view($promo)
    {
        if (Gate::denies('validate-shop-promo', $promo)) {
            return $this->error('没有权限!');
        }
        return view('index.promo.add',[
            'promo' => $promo
        ]);
    }

    /**
     * 促销添加
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAdd()
    {
        return view('index.promo.add');
    }

    /**
     * 促销商品
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getGoods()
    {
        return view('index.promo.goods',[
            'promoGoods' => $this->shop->promoGoods()->paginate()
        ]);
    }

    /**
     * 促销申请日志记录
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getApplyLog(Request $request)
    {
        $data = $request->only('start_at','end_at','number_name','status','salesman');
        $promoApply = $this->shop->promoApply();
        $promoApply = $this->promoService->applyLogSearch($promoApply,$data);
        return view('index.promo.apply-log',[
            'promoApply' => $promoApply->paginate(),
            'salesmans' => $this->shop->salesmen,
            'data' => $data,
        ]);
    }

    public function applyLogDetail($apply)
    {
        return view('index.promo.log-detail',[
            'apply' => $apply,
        ]);
    }
}
