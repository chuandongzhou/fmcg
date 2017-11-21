<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Advert;
use App\Services\AddressService;
use Illuminate\Support\Facades\Cache;

class AdvertController extends Controller
{

    /**
     * 获取移动端广告
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        $addressData = (new AddressService())->getAddressData();
        $data = array_except($addressData, 'address_name');
        $adverts = Advert::with('image')->where('type', cons('advert.type.app'))->OfTime()->ofAddress($data, true)->get();

        return $this->success(['advert' => $adverts]);
    }
}
