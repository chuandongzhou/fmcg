<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use App\Models\ShippingAddress;
use App\Models\SystemTradeInfo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{

    /**
     * 收货地址首页
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $shippingAddress = auth()->user()->shippingAddress()->with('address')->get()->toArray(); //商店详情
        $default = array_filter($shippingAddress, function ($address) {
            return $address['is_default'] == 1;
        });
        isset(array_keys($default)[0]) && array_pull($shippingAddress, array_keys($default)[0]);
        return view('index.personal.shipping-address-index',
            ['shippingAddress' => $shippingAddress, 'default' => $default]);

    }

    /**
     * 创建收货地址
     *
     * @return \Illuminate\View\View
     */
    /*public function create()
    {
        return view('index.personal.shipping-address', ['shippingAddress' => new ShippingAddress]);
    }*/

    /**
     * 编辑
     *
     * @param $shippingAddress
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($shippingAddress)
    {
        $shippingAddress->load('address');
        return view('index.personal.shipping-address',
            ['shippingAddress' => $shippingAddress]);
    }
}
