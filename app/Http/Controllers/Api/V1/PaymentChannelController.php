<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PaymentChannel;

class PaymentChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        $paymentChannels = PaymentChannel::app()->get();

        return $this->success(compact('paymentChannels'));
    }


}
