<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;

class ShopController extends Controller
{
    /**
     *  Show the form for editing the specified resource.
     *
     * @param $shop
     * @return \Illuminate\View\View
     */
    public function edit($shop)
    {
        $shop = $shop->load(['images', 'deliveryArea.coordinate', 'shopAddress']);
        $coordinate = $shop->deliveryArea->each(function ($area) {
            $area->coordinate;
        });
        return view('admin.shop.shop', ['shop' => $shop, 'coordinates' => $coordinate->toJson()]);
    }

}
