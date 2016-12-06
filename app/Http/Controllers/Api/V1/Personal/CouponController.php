<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\CreateCouponRequest;
use App\Http\Requests\Api\v1\UpdateCouponRequest;
use App\Models\Coupon;
use Carbon\Carbon;
use Gate;


class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        $shop = auth()->user()->shop;

        $coupons = $shop->coupons;

        return $this->success(['coupon' => $coupons]);
    }

    public function store(CreateCouponRequest $request)
    {
        $attributes = $request->all();

        $attributes['start_at'] = (new Carbon())->toDateString();

        if ($attributes['full'] < $attributes['discount']) {
            return $this->error('优惠金额不能大于最低订单金额');
        }

        $shop = auth()->user()->shop;

        return $shop->coupons()->create($attributes)->exists ? $this->success('添加优惠券成功') : $this->error('添加优惠券时出现问题');

    }

    /**
     * Display the specified resource.o
     *
     * @param \App\Models\Coupon $coupon
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function show(Coupon $coupon)
    {
        return $this->success(['coupon' => $coupon]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\UpdateCouponRequest $request
     * @param \App\Models\Coupon $coupon
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        if (Gate::denies('validate-shop-coupon', $coupon)) {
            return $this->error('优惠券不存在');
        }
        $attributes = $request->only(['stock', 'start_at', 'end_at']);

        return $coupon->fill($attributes)->save() ? $this->success('修改优惠券成功') : $this->error('修改优惠券时出现问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Coupon $coupon
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy(Coupon $coupon)
    {
        if (Gate::denies('validate-shop-coupon', $coupon)) {
            return $this->error('优惠券不存在');
        }

        return $coupon->delete() ? $this->success('删除优惠券成功') : $this->error('删除优惠券时出现问题');
    }
}
