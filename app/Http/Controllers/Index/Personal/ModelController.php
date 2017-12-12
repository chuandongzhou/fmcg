<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use App\Models\Advert;
use App\models\ShopSignature;

class ModelController extends Controller
{

    protected $shop;

    public function __construct()
    {
        $this->shop = auth()->user()->shop;
        $this->middleware('forbid:retailer');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAdvert()
    {
        $adverts = $this->shop->adverts()->paginate();

        return view('index.personal.model-advert-index', [
            'adverts' => $adverts,
        ]);
    }

    /**
     * 显示创建广告页面
     *
     * @return \Illuminate\View\View
     */
    public function getAdvertEdit($advertId)
    {
        $advert = $this->shop->adverts()->find($advertId);

        return view('index.personal.model-advert', [
            'advert' => is_null($advert) ? new Advert : $advert
        ]);
    }

    /**
     *返回添加店铺广告界面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreate()
    {
        return view('index.personal.model-advert', ['advert' => new Advert]);
    }

    /**
     * 返回模板设置界面
     */
    public function getModelEdit()
    {
        $shop = auth()->user()->shop()->with(['shopRecommendGoods', 'shopHomeAdverts'])->first();

        $goods = $shop->shopRecommendGoods->toArray();
        $goodsId = implode(',', array_column($goods, 'goods_id'));

        return view('index.personal.model-edit', ['goodsId' => $goodsId, 'shop' => $shop]);
    }

    /**
     * 模板选择
     *   *@return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getModelChoice()
    {

        return view('index.personal.model-choice',['shop' => auth()->user()->shop]);
    }

}
