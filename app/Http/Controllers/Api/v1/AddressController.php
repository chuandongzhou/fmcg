<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/10
 * Time: 16:46
 */
namespace App\Http\Controllers\Api\v1;

use DB;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    public function street(Request $request)
    {
        $addressList = DB::table('address')->where('pid', $request->input('pid'))->lists('name', 'id');
        return $this->success($addressList);
    }
}