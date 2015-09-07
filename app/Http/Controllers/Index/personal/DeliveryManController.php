<?php

namespace App\Http\Controllers\Index\personal;

use App\Http\Controllers\Index\Controller;

use App\Http\Requests;
use App\Models\DeliveryMan;

class DeliveryManController extends Controller
{


    /**
     * 配送人员列表
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // TODO: shop_id
        $deliveryMen = DeliveryMan::where('shop_id', 1)->get();
        return view('index.personal.delivery-man-index', ['deliveryMen' => $deliveryMen]);
    }

    /**
     * 添加配送人员
     *
     * @return \Illuminate\View\View
     */
    public function create(){
        return view('index.personal.delivery-man', ['deliveryMan' => new DeliveryMan]);
    }

    /**
     * 修改配送人员
     *
     * @param $deliveryMan
     * @return \Illuminate\View\View
     */
    public function edit($deliveryMan)
    {
        return view('index.personal.delivery-man', ['deliveryMan' => $deliveryMan]);
    }
}
