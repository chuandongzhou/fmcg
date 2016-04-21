<?php
namespace App\Http\Controllers\Admin;
use App\Models\BarcodeWithoutImages;
use App\Models\DeliveryArea;
use App\Models\Goods;
use App\Http\Requests;
use App\Models\Images;
use App\Services\AddressService;
use App\Services\AttrService;
use App\Services\GoodsService;
use App\Services\ImageUploadService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Gate;
use App\Models\Shop;
class GoodsController extends Controller{

    public function create(){
        return view('admin.goods.index');
    }


}