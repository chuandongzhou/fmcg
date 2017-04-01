<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/11/6
 * Time: 14:56
 */

namespace App\Http\Controllers\Index\Webhook;

use App\Http\Controllers\Index\Controller;
use Illuminate\Http\Request;
use DB;

class WechatController extends Controller
{

    public function anySuccess(Request $request) {
        info($request->all());

        return 'SUCCESS';
    }
}