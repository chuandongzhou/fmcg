<?php

namespace App\Http\Controllers\Index\Business;

use App\Models\Salesman;
use App\Services\BusinessService;
use App\Services\SalesmanTargetService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Index\Controller;

class SalesmanController extends Controller
{
    protected $shop;

    public function __construct()
    {
        $this->shop = auth()->user()->shop;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $salesmen = $this->shop->salesmen()->OfName($name, true)->paginate();
        return view('index.business.salesman-index', ['salesmen' => $salesmen, 'name' => $name]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('index.business.salesman', ['salesman' => new Salesman]);
    }


    /**
     * 业务员目标
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function target(Request $request)
    {
        $date = $request->input('date', (new Carbon())->format('Y-m'));

        $startDate = (new Carbon($date))->startOfMonth();
        $shopId = $this->shop->id;

        $salesmenOrderData = (new BusinessService)->getSalesmanOrders($this->shop,
            $startDate, (new Carbon($date))->endOfMonth());

        $salesmans = $salesmenOrderData->filter(function ($salesman) {
            return $this->shop->user_type < cons('user.type.maker') ? is_null($salesman->maker_id) : true;
        });
        //新开家
        $salesmans->each(function ($salesman) use ($startDate, $shopId) {
            $customers = $salesman->usefulOrders->pluck('salesman_customer_id')->toBase()->unique();

            //所有订单
            $lowDateOrders = $salesman->orders->filter(function ($order) use (
                $salesman,
                $startDate,
                $shopId
            ) {
                return $order->salesmanCustomer->shop_id != $shopId && $order->created_at < $startDate;
            })->pluck('salesman_customer_id')->toBase()->unique();


            $customers = $customers->filter(function ($customerId) use ($lowDateOrders) {
                return !$lowDateOrders->contains($customerId);
            });

            $salesman->newCustomers = $customers->count();
        });
        return view('index.business.salesman-target-index', [
            'date' => $date,
            'salesmen' => $salesmans,
            'target' => new SalesmanTargetService()
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function targetSet()
    {
        $salesmen = auth()->user()->shop->salesmen()->active()->get()->filter(function ($salesman) {
            return $this->shop->user_type < cons('user.type.maker') ? is_null($salesman->maker_id) : true;
        });
        return view('index.business.salesman-target-set', compact('salesmen'));
    }
}
