<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use App\Models\ShippingAddress;
use Gate;

class ShippingAddressController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * 收货地址首页
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $shippingAddress = $this->user->shippingAddress()->with('address')->orderBy('is_default', 'desc')->get(); //商店详情

        return $this->success(['shippingAddress' => $shippingAddress]);
    }

    public function show($address)
    {
        $address->load('address');
        return $this->success(['shippingAddress' => $address]);
    }

    /**
     * 设置默认
     *
     * @param $shippingAddress
     * @param $force
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function addressDefault($shippingAddress, $force = false)
    {
        $addressInfo = $shippingAddress instanceof ShippingAddress ? $shippingAddress : ShippingAddress::find($shippingAddress);
        if (Gate::denies('validate-user', $addressInfo)) {
            return $this->error('设置失败，请重试');
        }

        auth()->user()->shippingAddress()->where('id', '<>', $addressInfo->id)->update(['is_default' => 0]);

        if ($force || $addressInfo->fill(['is_default' => 1])->save()) {
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
        $addressAttr = ['province_id', 'city_id', 'district_id', 'street_id', 'area_name', 'address'];
        $address = $request->except($addressAttr);

        $shippingAddress = auth()->user()->shippingAddress()->create($address);
        if ($shippingAddress->exists) {
            $shippingAddress->is_default == 1 && $this->addressDefault($shippingAddress, true);
            $shipping = $request->only($addressAttr);
            $addressModel = $shippingAddress->address()->create($shipping);
            if ($addressModel->exists) {
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
        $addressAttr = ['province_id', 'city_id', 'district_id', 'street_id', 'area_name', 'address'];
        $shippingAddress = $request->except($addressAttr);
        if (Gate::denies('validate-user', $address)) {
            return $this->error('保存收货地址时出现问题');
        }

        if ($address->fill($shippingAddress)->save()) {
            $address->is_default == 1 && $this->addressDefault($address, true);
            $shipping = $request->only($addressAttr);
            $address->address->fill($shipping)->save();
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
