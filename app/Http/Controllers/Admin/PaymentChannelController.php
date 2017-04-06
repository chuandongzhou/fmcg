<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreatePaymentChannelRequest;
use App\Http\Requests\Admin\UpdatePaymentChannelRequest;
use App\Models\PaymentChannel;
use Illuminate\Http\Request;

class PaymentChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paymentChannels = PaymentChannel::paginate();
        return view('admin.payment-channel.index', compact('paymentChannels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payment-channel.payment-channel', ['paymentChannel' => new PaymentChannel]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreatePaymentChannelRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreatePaymentChannelRequest $request)
    {
        $attributes = $request->all();
        return PaymentChannel::create($attributes)->exists ? $this->success('添加渠道成功') : $this->error('添加渠道时出现问题');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentChannel $paymentChannel
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(PaymentChannel $paymentChannel)
    {
        return view('admin.payment-channel.payment-channel', ['paymentChannel' => $paymentChannel]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\UpdatePaymentChannelRequest $request
     * @param \App\Models\PaymentChannel $paymentChannel
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(UpdatePaymentChannelRequest $request, PaymentChannel $paymentChannel)
    {
        $attributes = $request->all();
        return $paymentChannel->update($attributes) ? $this->success('修改渠道成功') : $this->error('修改渠道时出现问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\PaymentChannel $paymentChannel
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(PaymentChannel $paymentChannel)
    {
        return $paymentChannel->delete() ? $this->success('删除渠道成功') : $this->error('删除渠道时出现问题');
    }
}
