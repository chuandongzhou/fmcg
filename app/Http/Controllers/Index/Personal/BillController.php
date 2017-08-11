<?php

namespace App\Http\Controllers\Index\Personal;

use App\Models\Shop;
use App\Services\BillService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Index\Controller;

class BillController extends Controller
{
    protected $billService;

    public function __construct(BillService $billService)
    {
        $this->billService = $billService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $shops = $this->billService->getShop($name)->paginate();
        return view('index.personal.bill-index', [
            'shops' => $shops,
            'data' => $name
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $time = $request->input('time');

        $shop = Shop::find($id);

        $timeInterval = $this->billService->timeHandler($time);

        $result = $this->billService->buyer($shop, $timeInterval);
        if ($request->input('act')) {
            return $this->billService->exportSeller($result,$shop,$timeInterval);
        }
        return view('index.personal.bill-detail', [
            'timeInterval' => $timeInterval,
            'shop' => $shop,
            'bill' => $result,
        ]);
    }


}
