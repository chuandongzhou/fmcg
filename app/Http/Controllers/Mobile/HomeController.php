<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Advert;
use App\Services\AddressService;
use App\Services\GoodsService;
use Cache;

class HomeController extends Controller
{
    public function index()
    {
        //广告
        $addressData = (new AddressService())->getAddressData();
        $data = array_except($addressData, 'address_name');
        $adverts = Advert::with('image')->where('type', cons('advert.type.app'))->OfTime()->ofAddress($data, true)->get();
        return view('mobile.index.index', [
            'goodsColumns' => GoodsService::getNewGoodsColumn(),
            'adverts' => $adverts,
        ]);
    }

}
