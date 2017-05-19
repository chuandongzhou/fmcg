<?php

namespace App\Http\Controllers\ChildUser;


use App\Models\Advert;

class ModelController extends Controller
{

    protected $shop;

    public function __construct()
    {
        $this->shop = child_auth()->user()->shop;
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
        $shop = $this->shop->load(['shopRecommendGoods', 'shopHomeAdverts']);

        $goodsId = $shop->shopRecommendGoods->pluck('goods_id')->implode(',');

        return view('child-user.model.edit', ['goodsId' => $goodsId, 'shop' => $shop]);
    }

    /**
     * 模板选择
     *   *@return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getModelChoice()
    {
        return view('child-user.model.choice',['shop' => $this->shop]);
    }

}
