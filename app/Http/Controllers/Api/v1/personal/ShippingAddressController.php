<?php

namespace App\Http\Controllers\Api\v1\personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use App\Models\ShippingAddress;
use Gate;

class ShippingAddressController extends Controller
{

    /**
     * 设置默认
     *
     * @param $id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function addressDefault($id)
    {
        $addressInfo = ShippingAddress::find($id);
        if (Gate::denies('validate-user', $addressInfo)) {
            return $this->error('设置失败，请重试');
        }
        if ($addressInfo['is_default'] == 1) {
            return $this->success('设置成功');
        }

        auth()->user()->ShippingAddress()->where('is_default', 1)->update(['is_default' => 0]);

        if ($addressInfo->fill(['is_default' => 1])->save()) {
            return $this->success('设置成功');
        }
        return $this->error('设置失败，请重试');
    }

    /**
     * 添加收货地址
     *
     * @param \App\Http\Requests\Api\v1\CreateShippingAddressRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Requests\Api\v1\CreateShippingAddressRequest $request)
    {
        $attributes = $request->all();
        $shippingAddress = auth()->user()->shippingAddress()->create($attributes);
        if ($shippingAddress->exists) {
            if ($shippingAddress->address()->create($attributes)->exists) {
                return $this->success('添加成功');
            }
            $shippingAddress->delete();
            return $this->error('添加收货地址失败');
        }
        return $this->error('添加收货地址失败');
    }

    /**
     * 保存
     *
     * @param \App\Http\Requests\Api\v1\CreateShippingAddressRequest $request
     * @param $address
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(Requests\Api\v1\CreateShippingAddressRequest $request, $address)
    {
        $attributes = $request->all();
        if (Gate::denies('validate-user', $address)) {
            return $this->error('保存收货地址时出现问题');
        }

        if ($address->fill($request->all())->save()) {
            $address->address->fill(
                [
                    'province_id'=> $attributes['province_id'],
                    'city_id' => $attributes['city_id'],
                    'district_id' => $attributes['district_id'],
                    'street_id' => $attributes['street_id'],
                    'area_name' => $attributes['area_name'],
                    'address' =>  $attributes['address']
                ])->save();
            return $this->success('保存收货地址成功');
        }
        return $this->error('保存收货地址时出现问题');
    }

    /**
     * 删除
     *
     * @param $address
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy($address)
    {

        if (Gate::denies('validate-user', $address)) {
            return $this->error('删除收货地址时遇到问题');
        }
        if ($address->delete()) {
            return $this->success('删除收货地址成功');
        }
        return $this->error('删除收货地址时遇到问题');
    }
}
