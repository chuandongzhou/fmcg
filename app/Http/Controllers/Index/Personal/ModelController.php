<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use App\Models\Advert;

class ModelController extends Controller
{

    protected $shop;

    public function __construct()
    {
        $this->shop = auth()->user()->shop;
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
}
