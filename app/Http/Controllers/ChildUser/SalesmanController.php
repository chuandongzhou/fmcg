<?php

namespace App\Http\Controllers\ChildUser;

use App\Services\BusinessService;
use App\Services\SalesmanTargetService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesmanController extends Controller
{
    protected $shop;

    public function __construct()
    {
        $this->shop = child_auth()->user()->shop;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $salesmen = $this->shop->salesmen()->OfName($name, true)->paginate();
        return view('child-user.salesman.index', ['salesmen' => $salesmen, 'name' => $name]);
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

        $salesmenOrderData = (new BusinessService)->getSalesmanOrders($this->shop,
            $startDate, (new Carbon($date))->endOfMonth());

        $shopId = $this->shop->id;


        //新开家
        $salesmenOrderData->each(function ($salesman) use ($startDate, $shopId) {
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

        return view('child-user.salesman.target', [
            'date' => $date,
            'salesmen' => $salesmenOrderData,
            'target' => new SalesmanTargetService()
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function targetSet()
    {
        $salesmen = child_auth()->user()->shop->salesmen()->active()->get();

        $actionUrl = url('api/v1/child-user/salesman/target-set');

        return view('index.business.salesman-target-set', compact('salesmen', 'actionUrl'));
    }
}
