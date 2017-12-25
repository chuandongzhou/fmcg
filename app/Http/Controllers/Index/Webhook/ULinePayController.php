<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/11/6
 * Time: 14:56
 */

namespace App\Http\Controllers\Index\Webhook;

use App\Http\Controllers\Index\Controller;
use App\Models\Order;
use App\Services\PayService;
use Illuminate\Http\Request;
use Gate;

class ULinePayController extends Controller
{

    public function anySuccess(Request $request)
    {
        $data = $request->all();

        info($data);

        return array_to_xml(['return_code' => 'SUCCESS'],
            new \SimpleXMLElement('<?xml version=\'1.0\' encoding=\'utf-8\'?><xml />'))->asXML();
    }


}