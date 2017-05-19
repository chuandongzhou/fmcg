<?php

namespace App\Http\Controllers\ChildUser;

use App\Models\DeliveryMan;

class DeliveryManController extends Controller
{

    /**
     * DeliveryManController constructor.
     */
    public function __construct()
    {
        $this->middleware('deposit');
    }
    /**
     * 配送人员列表
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $deliveryMen = child_auth()->user()->shop->deliveryMans;
        return view('child-user.delivery-man.index', ['deliveryMen' => $deliveryMen]);
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
