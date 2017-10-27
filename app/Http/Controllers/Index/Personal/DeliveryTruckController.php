<?php

namespace App\Http\Controllers\Index\Personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryTruckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveryTrucks = auth()->user()->shop->deliveryTrucks;
        return view('index.personal.delivery-truck-index', compact('deliveryTrucks'));
    }


}
