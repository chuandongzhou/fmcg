<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;


class WarehouseKeeperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $warehouseKeepers = auth()->user()->shop->warehouseKeepers;

        return view('index.warehouse-keeper.index', compact('warehouseKeepers'));
    }

}
