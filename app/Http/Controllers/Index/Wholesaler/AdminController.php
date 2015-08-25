<?php

namespace App\Http\Controllers\Index\Wholesaler;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Index\Controller;
use App\Models\Order;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        //
        $payType = cons()->valueLang('pay_type');

        return view('index.wholesaler.admin-admin.index', [
            'pay_type' => $payType,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getSearch(Request $request)
    {
        //todo:获取当前用户ID号
        $sellerId = 2;
        $search = $request->all();

        $orders = Order::where('seller_id', $sellerId)->where('payable_type', $search['pay_type'])->where('status',
            $search['status'])->paginate();
        dd($orders);

        return $request->all();
    }
}
