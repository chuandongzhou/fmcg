<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Models\SalesmanVisitOrder;
use App\Models\Salesman;
use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;
use App\Models\SalesmanCustomer;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use App\Http\Requests\Api\v1\CustomerDisplayFeeRequest;
use Carbon\carbon;

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
            'shop.user', 'mortgageGoods')->get()->each(function ($customer) {
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

        if (array_get($attributes,
                'display_type') == cons('salesman.customer.display_type.mortgage') && !isset($attributes['mortgage_goods'])
        ) {
            return $this->error('抵费商品不能为空');
        }


        $attributes['number'] = $this->_getCustomerNumber($salesman);
        $attributes['letter'] = $this->_getLetter($attributes['name']);

        if ($attributes['account']) {
            $shop = $salesman->shop;

            if (!$attributes['shop_id'] = $this->_validateAccount($shop, $attributes)) {
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

        if (isset($attributes['display_start_month']) && $attributes['display_start_month'] > $attributes['display_end_month']) {
            return $this->invalidParam('display_end_month', '开始月份不能大于结束月份');
        }

        if ($attributes['account']) {
            $shop = auth()->user()->shop;

            if (!$attributes['shop_id'] = $this->_validateAccount($shop, $attributes, $customer)) {
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

        if (isset($attributes['display_start_month']) && $attributes['display_start_month'] > $attributes['display_end_month']) {
            return $this->invalidParam('display_end_month', '开始月份不能大于结束月份');
        }

        if ($attributes['account']) {
            $shop = salesman_auth()->user()->shop;

            if (!$attributes['shop_id'] = $this->_validateAccount($shop, $attributes, $customer)) {
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
        $goodsIds = $request->input('goods_id');

        $customer = $salesman->customers()->find($customerId);
        if (is_null($customer)) {
            return $this->error('客户不存在');
        }
        /*
                $addGoodsIds = [];

                foreach ($goodsIds as $goodsId) {
                    if ($customer->goods()->where(['goods_id' => $goodsId])->pluck('goods_id')) {
                        continue;
                    }
                    $addGoodsIds[] = $goodsId;
                }

                if (empty($addGoodsIds)) {
                    return $this->error('添加商品成功');
                }

                $goods = Goods::active()->whereIn('id', $goodsIds)->where('shop_id', $salesman->shop_id)->get([
                    'id',
                    'shop_id'
                ]);

                if ($goods->isEmpty()) {
                    return $this->error('商品不存在');
                }*/

        $customer->goods()->sync((array)$goodsIds);

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
     * 客户某月陈列费发放情况
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function customerDisplayFee(Request $request)
    {
        $salesman = salesman_auth()->user();
        $keyword = $request->input('keyword');
        $month = $request->input('month');

        $displayTypes = cons('salesman.customer.display_type');

        $like = '%' . $keyword . '%';

        $customers = $salesman->customers()->where('name', 'LIKE', $like)->where('display_type', '>',
            $displayTypes['no'])->where(function ($q) use ($month) {
            $q->where('display_start_month', '<=', $month)->where('display_end_month', '>=', $month);
        })->with([
            'displayList' => function ($query) use ($month) {
                $query->where(['month' => $month])->select([
                    'id',
                    'salesman_customer_id',
                    'month',
                    'used',
                    'salesman_visit_order_id',
                    'mortgage_goods_id'
                ]);
            },
            'displayList.order.order'
        ])->get(['id', 'name', 'display_type', 'salesman_id', 'display_fee']);

        foreach ($customers as $customer) {
            $isMortgage = $customer->display_type == $displayTypes['mortgage'];
            if ($isMortgage) {
                $customer->load('mortgageGoods');
                $customer->displayList->load('mortgageGoods');
            }
            $customer->setAppends(['business_address_name']);
            $customer->addHidden(['business_address']);
            if ($customer->displayList) {
                $orders = [];
                foreach ($customer->displayList as $item) {
                    $order = $item->order;
                    $order->setAppends(['order_status_name']);
                    $order->addHidden(['order']);
                    if (isset($orders[$order->id]) && $item->mortgageGoods) {
                        //商品
                        $orders[$order->id]['mortgages'][] = [
                            'id' => $item->mortgageGoods->id,
                            'name' => $item->mortgageGoods->goods_name,
                            'used' => $item->used,
                            'pieces' => $item->mortgageGoods->pieces
                        ];
                    } else {
                        if ($isMortgage && $item->mortgageGoods) {
                            $orders[$order->id] = [
                                'id' => $order->id,
                                'order_status_name' => $order->order_status_name,
                                'created_at' => (string)$order->created_at
                            ];
                            $orders[$order->id]['mortgages'][] = [
                                'id' => $item->mortgageGoods->id,
                                'name' => $item->mortgageGoods->goods_name,
                                'used' => (int)$item->used,
                                'pieces' => $item->mortgageGoods->pieces
                            ];
                        } elseif (!$item->mortgage_goods_id) {
                            //陈列费现金
                            $orders[$order->id] = [
                                'id' => $order->id,
                                'order_status_name' => $order->order_status_name,
                                'used' => $item->used,
                                'created_at' => (string)$order->created_at
                            ];
                        }
                    }
                }
                $customer->orders = array_values($orders);
                $customer->addHidden(['displayList']);
            }

        }

        return $this->success(compact('customers'));
    }

    /**
     * 指定时间段内陈列费发放情况
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function displayFee(Request $request)
    {
        if (empty($request->input('start_at')) || empty($request->input('end_at'))) {
            return $this->error('开始时间和结束时间不能为空');
        }

        return $this->success(['orders' => $this->_queryDispalyFee($request)]);
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
        return $name ? strtoupper(pinyin_abbr($name)[0]) : '';
    }

    /**
     * 验证用户名
     *
     * @param $shop
     * @param $attributes
     * @param $customer
     * @return bool
     */
    private function _validateAccount($shop, $attributes, $customer = null)
    {
        $user = User::where('user_name', $attributes['account'])->first();
        if (is_null($user) || array_get($attributes, 'type', cons('user.type.retailer')) != $user->type) {
            return false;
        }
        $shopId = $user->shop_id;

        $existsCustomer = $shop->salesmenCustomer()->where('salesman_customer.shop_id', $shopId)->first();

        if ($existsCustomer) {
            if ($customer && $customer->id == $existsCustomer->id) {
                return $shopId;
            }
            return false;
        }
        return $shopId;

    }


    /**
     * 陈列费订单查询
     *
     * @param $request
     * @return mixed
     */
    private function _queryDispalyFee($request)
    {
        $start_at = (new Carbon($request->input('start_at')))->startOfDay();
        $end_at = (new Carbon($request->input('end_at')))->endOfDay();
        $where = ['salesman_id' => salesman_auth()->id()];
        if ($customer_id = $request->input('salesman_customer_id')) {
            $where['salesman_customer_id'] = $customer_id;
        }
        $orders = SalesmanVisitOrder::where($where)
            ->whereBetween('created_at', [$start_at, $end_at])->where(function ($query) {
                $query->where('display_fee', '>', 0)->orWhere(function ($query) {
                    $query->has('mortgageGoods');
                });
            })
            ->with('mortgageGoods')
            ->select([
                'id',
                'status',
                'created_at',
                'display_fee',
                'order_remark',
                'display_remark',
                'salesman_customer_id'
            ])->get();
        return $orders;
    }

}
