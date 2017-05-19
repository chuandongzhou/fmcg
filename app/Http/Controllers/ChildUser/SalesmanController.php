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

        $salesmenOrderData = (new BusinessService)->getSalesmanOrders($this->shop,
            (new Carbon($date))->startOfMonth(), (new Carbon($date))->endOfMonth());


        return view('child-user.salesman.target', [
            'date' => $date,
            'salesmen' => $salesmenOrderData,
            'target' => new SalesmanTargetService()
        ]);
    }
}
