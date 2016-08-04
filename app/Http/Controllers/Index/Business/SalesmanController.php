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
        $salesmen = $this->shop->salesmen()->active()->OfName($name, true)->paginate();
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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


        return view('index.business.salesman-target-index', [
            'date' => $date,
            'salesmen' => $salesmenOrderData,
            'target' => new SalesmanTargetService()
        ]);
    }
}
