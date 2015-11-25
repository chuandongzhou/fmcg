<?php

namespace App\Http\Controllers\Index\Personal;


use App\Http\Controllers\Index\Controller;
use App\Http\Requests;
use App\Models\SystemTradeInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InfoController extends Controller
{

    public function index()
    {

        $shop = auth()->user()->shop->load('shopAddress', 'deliveryArea')->setAppends(['logo_url']);
        $coordinate = $shop->deliveryArea->each(function ($area) {
            $area->coordinate;
        });
        return view('index.personal.info', [
            'shop' => $shop,
            'coordinates' => $coordinate
        ]);
    }
}
