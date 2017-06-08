<?php

namespace App\Http\Controllers\Index\Personal;

use App\Models\Shop;
use App\Services\BillService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Index\Controller;

class BillController extends Controller
{
    /*
     * 对账单服务
     */
    protected $billService;
    public function __construct()
    {
        $this->billService = new BillService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $shops = $this->billService->getBillListsOfBuyer($name)->paginate();
        return view('index.personal.bill-index',[
            'shops' => $shops,
            'data' => $name
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $time = $request->input('time');
        $shop = Shop::find($id);
        $timeArray = $this->billService->timeHandler($time);
        $bill =  $this->billService->getBillDetailOfBuyer($shop,$timeArray);
        if($request->input('act')){
            return $this->billService->billExportOfSeller($bill);
        }
        return view('index.personal.bill-detail',[
            'bill' => $bill,
            'shop' => $shop,
            'time' => $time
        ]);
    }


}
