<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Models\Goods;
use App\Models\Salesman;
use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;
use App\Models\SalesmanCustomer;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;

class SalesmanCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        $customers = salesman_auth()->user()->customers()->with('businessAddress', 'shippingAddress',
            'shop.user')->get()->each(function ($customer) {
            $customer->shop && $customer->shop->setAppends([]);
            $customer->setAppends(['account']);
            $customer->business_district_id = $customer->businessAddress->district_id;
            $customer->business_street_id = $customer->businessAddress->street_id;
            $customer->business_address_address = $customer->businessAddress->address;
        });
        $customers = $customers->sortBy('business_address_address')->sortBy('business_district_id')->sortBy('business_street_id');
        return $this->success(['customers' => $customers->values()]);
    }

    /**
     * @param \App\Http\Requests\Api\v1\CreateSalesmanCustomerRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Api\v1\CreateSalesmanCustomerRequest $request)
    {
        $attributes = $request->all();
        $salesman = isset($attributes['salesman_id']) ? auth()->user()->shop->salesmen()->active()->find($attributes['salesman_id']) : salesman_auth()->user();
        if (is_null($salesman)) {
            return $this->error('添加客户失败，请重试');
        }

        $attributes['number'] = $this->_getCustomerNumber($salesman);
        $attributes['letter'] = $this->_getLetter($attributes['name']);

        if ($attributes['account']) {
            $shop = $salesman->shop;

            if (!$attributes['shop_id'] = $this->_validateAccount($shop, $attributes['account'])) {
                return $this->invalidParam('account', '账号不存在或已被关联');
            }
        } else {
            $attributes['shop_id'] = null;
        }


        if ($salesman->customers()->create(array_except($attributes, 'account'))->exists) {
            return $this->success('添加客户成功');
        }
        return $this->error('添加客户时出现问题');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateSalesmanCustomerRequest $request
     * @param $customer
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Api\v1\CreateSalesmanCustomerRequest $request, SalesmanCustomer $customer)
    {
        if ($customer && Gate::denies('validate-customer', $customer)) {
            return $this->error('客户不存在');
        }
        $attributes = $request->all();
        $attributes['letter'] = $this->_getLetter($attributes['name']);
        if ($attributes['account']) {
            $shop = auth()->user()->shop;

            if (!$attributes['shop_id'] = $this->_validateAccount($shop, $attributes['account'], $customer)) {
                return $this->invalidParam('account', '账号不存在或已被关联');
            }
        } else {
            $attributes['shop_id'] = null;
        }

        if ($customer->fill(array_except($attributes, 'account'))->save()) {
            return $this->success('修改客户成功');
        }
        return $this->error('修改客户时出现问题');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateSalesmanCustomerRequest $request
     * @param $customer
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateByApp(Requests\Api\v1\CreateSalesmanCustomerRequest $request, SalesmanCustomer $customer)
    {
        if ($customer && $customer->salesman_id != salesman_auth()->id()) {
            return $this->error('客户不存在');
        }
        $attributes = $request->except(['number']);
        $attributes['letter'] = $this->_getLetter($attributes['name']);


        if ($attributes['account']) {
            $shop = salesman_auth()->user()->shop;

            if (!$attributes['shop_id'] = $this->_validateAccount($shop, $attributes['account'], $customer)) {
                return $this->invalidParam('account', '账号不存在或已被关联');
            }
        } else {
            $attributes['shop_id'] = null;
        }

        if ($customer->fill(array_except($attributes, 'account'))->save()) {
            return $this->success('修改客户成功');
        }
        return $this->error('修改客户时出现问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $customer
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($customer)
    {
        if ($customer && Gate::denies('validate-customer', $customer)) {
            return $this->error('客户不存在');
        }

        return $customer->delete() ? $this->success('删除客户成功') : $this->error('删除客户时出现问题');

    }

    /**
     * 销售商品列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function saleGoods(Request $request)
    {
        $salesman = salesman_auth()->user();
        $customerId = $request->input('salesman_customer_id');
        $customer = $salesman->customers()->find($customerId);
        if (is_null($customer)) {
            return $this->error('客户不存在');
        }

        $goods = $customer->goods()->select([
            'id',
            'name',
            'bar_code',
            'price_retailer',
            'pieces_retailer',
            'price_wholesaler',
            'pieces_wholesaler',
        ])->get()->each(function ($item) {
            $item->setAppends(['image_url']);
        });
        return $this->success(['goods' => $goods]);
    }

    /**
     * 添加销售商品
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function addSaleGoods(Request $request)
    {
        $salesman = salesman_auth()->user();
        $customerId = $request->input('salesman_customer_id');
        $goodsId = $request->input('goods_id');

        $customer = $salesman->customers()->find($customerId);
        if (is_null($customer)) {
            return $this->error('客户不存在');
        }
        if ($customer->goods()->where(['goods_id' => $goodsId])->pluck('goods_id')) {
            return $this->success('添加商品成功');
        }
        $goods = Goods::active()->find($goodsId);


        if (is_null($goods) || $goods->shop_id != $salesman->shop_id) {
            return $this->error('商品不存在');
        }

        $customer->goods()->attach($goodsId);

        return $this->success('添加商品成功');

    }

    /**
     * 删除销售商品
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function deleteSaleGoods(Request $request)
    {
        $salesman = salesman_auth()->user();
        $customerId = $request->input('salesman_customer_id');
        $customer = $salesman->customers()->find($customerId);
        if (is_null($customer)) {
            return $this->error('客户不存在');
        }
        $goodsId = $request->input('goods_id');
        $customer->goods()->detach($goodsId);
        return $this->success('移除商品成功');
    }

    /**
     * 获取客户号
     *
     * @param \App\Models\Salesman $salesman
     * @return mixed
     */
    private function _getCustomerNumber(Salesman $salesman)
    {
        return $salesman->customers()->max('number') + 1;
    }

    /**
     * 获取昵称首字字母
     *
     * @param $name
     * @return string
     */
    private function _getLetter($name)
    {
        return strtoupper(pinyin_abbr($name)[0]);
    }

    /**
     * 验证用户名
     *
     * @param $shop
     * @param $account
     * @param $customer
     * @return bool
     */
    private function _validateAccount($shop, $account, $customer = null)
    {
        $user = User::where('user_name', $account)->with('shop')->first();
        if (is_null($user)) {
            return false;
        }
        $shopId = $user->shop->id;

        $existsCustomer = $shop->salesmenCustomer()->where('salesman_customer.shop_id', $shopId)->first();

        if ($existsCustomer) {
            if ($customer && $customer->id == $existsCustomer->id) {
                return $shopId;
            }
            return false;
        }
        return $shopId;

    }

}
