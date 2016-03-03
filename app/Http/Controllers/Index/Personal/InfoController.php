<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use App\Http\Requests;

class InfoController extends Controller
{

    public function index()
    {
        $shop = auth()->user()->shop->load('shopAddress', 'deliveryArea', 'user')->setAppends(['logo_url']);
      /*  $coordinate = $shop->deliveryArea->each(function ($area) {
            $area->coordinate;
        });*/
        return view('index.personal.info', [
            'shop' => $shop,
           /* 'coordinates' => $coordinate*/
        ]);
    }
}
