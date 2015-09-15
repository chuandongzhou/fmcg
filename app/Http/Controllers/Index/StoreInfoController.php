<?php
/**
 * Created by PhpStorm.
 * User: wh
 * Date: 2015/9/7
 * Time: 19:21
 */

namespace App\Http\Controllers\Index;


use Illuminate\Http\Request;
use App\Models\Shop;

class StoreInfoController extends Controller
{

    public function getIndex(Request $request)
    {
        $shopId = $request->input('shop_id');
        $storeInfo = Shop::with('user', 'logo', 'deliveryArea', 'images')->find($shopId);
        if ($storeInfo) {
            return view('index.wholesaler.Home.store-info', [
                'shop' => $storeInfo->toArray()
            ]);
        }

        return back();
    }
}