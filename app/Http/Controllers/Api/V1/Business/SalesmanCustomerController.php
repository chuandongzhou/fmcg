<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Models\AddressData;
use App\Models\PromoApply;
use App\Models\SalesmanVisitOrder;
use App\Models\Salesman;
use App\Models\ConfirmOrderDetail;
use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;
use App\Models\SalesmanCustomer;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use DB;
use Illuminate\Http\Request;
use ExcelImport;
use Mockery\Exception;

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
            $customer->setAppends(['account', 'store_type_name']);
            $customer->business_district_id = $customer->businessAddress->district_id;
            $customer->business_street_id = $customer->businessAddress->street_id;
            $customer->business_address_address = $customer->businessAddress->address;
        });
        $customers = $customers->sortBy('business_address_address')->sortBy('business_district_id')->sortBy('business_street_id');
        return $this->success(['customers' => $customers->values()]);
    }

    /**
     * 添加客户(业务员添加/店家添加)
     *
     * @param \App\Http\Requests\Api\v1\CreateSalesmanCustomerRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Api\v1\CreateSalesmanCustomerRequest $request)
    {
        //客户数据
        $attributes = $request->all();
        //用户身份
        $userType = auth()->user()->type ?? salesman_auth()->user()->shop->user_type;
        //业务员身份
        $salesman = isset($attributes['salesman_id']) ? auth()->user()->shop->salesmen()->active()->find($attributes['salesman_id']) : salesman_auth()->user();
        //验证业务员身份
        if (is_null($salesman)) {
            return $this->error('业务员身份错误，请重试!');
        }

        //陈列费类型为商品时验证陈列费商品
        if (array_get($attributes,
                'display_type') == cons('salesman.customer.display_type.mortgage') && !isset($attributes['mortgage_goods'])
        ) {
            return $this->error('抵费商品不能为空!');
        }

        //客户编号
        $attributes['number'] = $this->_getCustomerNumber($salesman);
        //客户所属
        $attributes['belong_shop'] = $salesman->shop_id;
        //客户名首字母
        $attributes['letter'] = $this->_getLetter($attributes['name']);
        //是否有线上账号
        if (array_get($attributes, 'account')) {
            $validatedResult = $this->_validateAccount($attributes, $salesman->shop, $salesman);
            if (is_string($validatedResult)) {
                return $this->invalidParam('account', $validatedResult);
            }
            $attributes['shop_id'] = $validatedResult->shop_id;
            if (array_get($attributes, 'type') != $validatedResult->type) {
                return $this->invalidParam('type', '客户账号与客户类型不匹配');
            }
        }
        $shop_id = array_get($attributes, 'shop_id', null);

        //厂家添加供应商客户处理
        if ($userType == cons('user.type.maker') && $attributes['type'] == cons('user.type.supplier')) {
            if (!$shop_id) {
                return $this->invalidParam('salesman_id', '未绑定账号客户无法指派业务员');
            }
            if ($salesman->shop_id != $salesman->maker_id && $salesman->shop_id != $validatedResult->shop_id) {
                return $this->invalidParam('salesman_id', '该业务员已指派');
            }
            $salesman->shop_id = $shop_id;
            $salesman->save();
        }
        $salesmanCustomer = $salesman->customers()->create(array_except($attributes, 'account'));
        return $salesmanCustomer->exists ? $this->success('添加客户成功') : $this->error('添加客户时出现问题');
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
        $user = auth()->user();
        //客户数据
        $attributes = $request->all();
        //业务员信息
        $salesman = isset($attributes['salesman_id']) ? $user->shop->salesmen()->active()->find($attributes['salesman_id']) : salesman_auth()->user();
        //首字母
        $attributes['letter'] = $this->_getLetter($attributes['name']); //首字母大写

        if (isset($attributes['display_start_month']) && $attributes['display_start_month'] > $attributes['display_end_month']) {
            return $this->invalidParam('display_end_month', '开始月份不能大于结束月份');
        }
        //陈列费类型为商品时验证陈列费商品
        if (array_get($attributes,
                'display_type') == cons('salesman.customer.display_type.mortgage') && !isset($attributes['mortgage_goods'])
        ) {
            return $this->error('抵费商品不能为空!');
        }
        $account = array_get($attributes, 'account');
        if ($account && $account != $customer->account) {
            $shop = $salesman->shop;
            $validatedResult = $this->_validateAccount($attributes, $shop, $salesman);
            if (is_string($validatedResult)) {
                return $this->invalidParam('account', $validatedResult);
            }
        }
        $customerShopId = $attributes['shop_id'] = isset($validatedResult) ? $validatedResult->shop_id : $customer->shop_id;
        if (($type = array_get($attributes, 'type')) != $customer->type) {
            $customerType = isset($validatedResult) ? $validatedResult->type : $type;
            if ($customerType != $type) {
                return $this->invalidParam('type', '客户账号与客户类型不匹配');
            }
        }
        if ($user->type == cons('user.type.maker') && $attributes['type'] == cons('user.type.supplier')) {
            if ($customer->salesman_id != $salesman->id) {
                if (!$customerShopId) {
                    return $this->invalidParam('salesman_id', '未绑定账号客户无法指派业务员');
                }
                if ($customerShopId != $salesman->shop_id && $salesman->shop_id != $salesman->maker_id) {
                    return $this->invalidParam('salesman_id', '该业务员已指派');
                }
                $salesman->shop_id = $customerShopId;
                $salesman->save();
                $customer->salesman->shop_id = $user->shop_id;
                $customer->salesman->save();
            }
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
        $salesman = salesman_auth()->user();
        if ($salesman->maker_id && $customer->type > cons('user.type.wholesaler')) {
            return $this->invalidParam('account', '权限不足!');
        }
        $attributes = $request->except(['number']);
        $attributes['letter'] = $this->_getLetter($attributes['name']);

        if (isset($attributes['display_start_month']) && $attributes['display_start_month'] > $attributes['display_end_month']) {
            return $this->invalidParam('display_end_month', '开始月份不能大于结束月份');
        }
        //陈列费类型为商品时验证陈列费商品
        if (array_get($attributes,
                'display_type') == cons('salesman.customer.display_type.mortgage') && !isset($attributes['mortgage_goods'])
        ) {
            return $this->error('抵费商品不能为空!');
        }
        $account = array_get($attributes, 'account', null);
        $validatedResult = '';
        if (!is_null($account) && $account != $customer->account) {
            $validatedResult = $this->_validateAccount($attributes, $salesman->shop, $salesman);
            if (is_string($validatedResult)) {
                return $this->invalidParam('account', $validatedResult);
            }
        }
        $attributes['shop_id'] = ($validatedResult instanceof User) ? $validatedResult->shop_id : $customer->shop_id;
        if (($type = array_get($attributes, 'type')) != $customer->type) {
            $customerType = ($validatedResult instanceof User) ? $validatedResult->type : $type;
            if ($customerType != $type) {
                return $this->invalidParam('type', '客户账号与客户类型不匹配');
            }
        }
        //厂家业务员未分配是不能登录的,所以暂时不做处理
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
        ])->whereNull('deleted_at')->where('status', cons('status.on'))->withGoodsPieces()->get()->each(function ($item
        ) {
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
            'displayList.order.order',
            'businessAddress'
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
     * 客户导入
     *
     * @param \App\Http\Requests\Api\v1\SalesmanCustomerImportRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function import(Requests\Api\v1\SalesmanCustomerImportRequest $request)
    {
        //导入客户
        $data = $request->all();
        $salesmanId = array_get($data, 'salesman_id');
        $user = auth()->user();
        $salesman = $user->shop->salesmen()->active()->find($salesmanId);
        if (is_null($salesman)) {
            return $this->error('业务员不存在!');
        }

        $userType = $user->type;

        $file = $request->file('file');
        $result = ExcelImport::fetchContent($file);

        if (!$result) {
            return $this->error(ExcelImport::getErrorMsg());
        }

        $addressData = array_only($data, ['province_id', 'city_id', 'district_id', 'street_id', 'area_name']);
        $results = DB::transaction(function () use ($userType, $result, $salesman, $data, $addressData) {
            try {
                $customerAddresses = cons('salesman.customer.address_type');
                $addressesData = [];
                $nowTime = Carbon::now();
                foreach ($result as $item) {
                    if (($type = (int)$item[5]) >= $userType) {
                        continue;
                    }
                    $customer = $this->_createSalesmanCustomer($salesman, $item, $data, $type);
                    if ($customer->exists) {
                        $addressData['address'] = $item[4];
                        //创建收货地址
                        $addressesData[] = $this->_buildSalesmanCustomerAddress($customer, $addressData,
                            $customerAddresses['shipping'], $nowTime);
                        //创建营业地址
                        $addressesData[] = $this->_buildSalesmanCustomerAddress($customer, $addressData,
                            $customerAddresses['business'], $nowTime);
                    }
                }
                DB::table('address_data')->insert($addressesData);
                return true;
            } catch (Exception $e) {
                return $e->getMessage();
            }

        });

        return $results === true ? $this->success('导入客户成功') : $this->error($result);
    }

    /**
     * 创建客户
     *
     * @param $salesman
     * @param $item
     * @param $data
     * @param $type
     * @return mixed
     */
    private function _createSalesmanCustomer($salesman, $item, $data, $type)
    {
        $customerData = [
            'number' => $this->_getCustomerNumber($salesman),
            'belongs_shop' => $salesman->shop_id,
            'name' => $item[0],
            'letter' => $this->_getLetter($item[0]),
            'contact' => $item[1],
            'contact_information' => $item[2],
            'business_area' => $item[3],
            'business_address_lng' => $data['x_lng'],
            'business_address_lat' => $data['y_lat'],
            'shipping_address_lng' => $data['x_lng'],
            'shipping_address_lat' => $data['y_lat'],
            'type' => $type
        ];
        if ($type == cons('user.type.retailer')) {
            $customerData['store_type'] = (int)$item[6];
        }
        $customer = $salesman->customers()->create($customerData);
        return $customer;
    }

    /**
     *
     * 创建客户地址
     *
     * @param $customer
     * @param $addressData
     * @param $type
     * @return mixed
     */
    private function _buildSalesmanCustomerAddress($customer, $addressData, $type, $nowTime)
    {
        $addressData['type'] = $type;
        $addressData['addressable_type'] = 'App\Models\SalesmanCustomer';
        $addressData['addressable_id'] = $customer->id;
        $addressData['created_at'] = $nowTime;
        $addressData['updated_at'] = $nowTime;
        return $addressData;
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
    private function _validateAccount($attributes, $shop, $salesman)
    {
        //得到客户信息
        $userInfo = User::where('user_name', $attributes['account'])->first();
        if (is_null($userInfo)) {
            return '账号不存在!';
        }
        //判断用户角色类型
        if (array_get($attributes, 'type', cons('user.type.retailer')) != $userInfo->type) {
            return '账号角色类型不匹配';
        }
        if (!$salesman->maker_id) {
            //查找是否已存在此客户
            $existsCustomer = SalesmanCustomer::where('shop_id', $userInfo->shop_id)->where(function ($query) use ($shop
            ) {
                $query->whereIn('salesman_id', $shop->salesmen->pluck('id'))
                    ->orWhere('belong_shop', $shop->id);
            })->first();
            if ($existsCustomer) {
                return '已关联此用户!';
            }
        }
        return $userInfo;
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

    /**
     * 客户曾购买商品查询
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function purchasedGoods(Request $request)
    {
        $customerId = $request->input('customer_id');
        if (empty($customerId) || empty(SalesmanCustomer::where('salesman_id',
                salesman_auth()->id())->find($customerId))
        ) {
            return $this->error('客户信息错误');
        }
        return $this->success(['data' => ConfirmOrderDetail::where('customer_id', $customerId)->get()]);

    }

    /**
     * 获取客户商店全部类型
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getStoreType()
    {
        $types = cons()->valueLang('salesman.customer.store_type');
        array_walk($types, function ($value, $key) use (&$new) {
            $new[] = [
                'key' => $key,
                'value' => $value
            ];
        });
        return $this->success($new);
    }

    /**
     * 更新客户店铺类型
     *
     * @param \Illuminate\Http\Request $request
     */
    public function updateStoreType(Request $request, $customerId)
    {
        try {
            $customer = SalesmanCustomer::find($customerId);
            if (!$customer instanceof SalesmanCustomer) {
                throw new \Exception('参数错误');
            }
            $customer->fill($request->only('store_type'))->save();
            return $this->success('修改成功');
        } catch (\Exception $e) {
            return $this->error('修改失败');
        }
    }

    /**
     * 绑定客户关系
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function bindRelation(Request $request)
    {
        $shopIds = $request->input('shopIds', null);
        if (is_null($shopIds)) {
            return $this->error('参数错误!');
        }
        $user = auth()->user();
        $data = [];
        $now = Carbon::now();
        foreach (explode(',', $shopIds) as $shopId) {
            $data[] = [
                'maker_id' => $shopId,
                'supplier_id' => $user->shop_id,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        if (DB::table('business_relation_apply')->insert($data)) {
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 获取客户申请通过的且未被使用的促销活动
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getPassedPromos(Request $request)
    {
        $shop = auth()->user() ? auth()->user()->shop : salesman_auth()->user()->shop;
        if (!count($shop->salesmen)) {
            return $this->error('店铺业务员不见了');
        }
        $applyLists = PromoApply::with([
            'promo' => function ($promo) {
                $promo->active();
            },
            'promo.condition.goods',
            'promo.rebate.goods'
        ])->whereIn('salesman_id',
            $shop->salesmen->pluck('id'))->client($request->input('client_id'))->pass()->get();

        $promos = collect();
        $applyLists->each(function ($item) use (
            $promos
        ) {
            if ($item->promo) {
                $item->promo->addHidden(['created_at', 'updated_at', 'status', 'deleted_at']);
                $item->promo->apply_id = $item->id;
                $item->promo->is_use = is_null($item->order) ? 0 : 1;
                $promos->push($item->promo->toArray());
            }
        });
        $promos->toArray();
        return $this->success(compact('promos'));
    }
}
