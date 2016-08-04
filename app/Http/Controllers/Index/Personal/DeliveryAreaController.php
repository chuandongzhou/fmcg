<?php

namespace App\Http\Controllers\Index\Personal;

use App\Models\AddressData;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Index\Controller;

class DeliveryAreaController extends Controller
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
    public function index()
    {
        return view('index.personal.delivery-area-index', ['areas' => $this->shop->deliveryArea]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('index.personal.delivery-area', ['area' => new AddressData]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $deliveryArea =  $this->shop->deliveryArea()->find($id);
        if (is_null($deliveryArea)) {
            return $this->error('配送区域不存在');
        }
        return view('index.personal.delivery-area', ['area' => $deliveryArea]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
