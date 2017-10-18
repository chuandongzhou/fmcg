<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Models\BusinessRelationApply;
use App\Models\Salesman;
use App\Models\Shop;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Support\Facades\DB;

class TradeRequestController extends Controller
{
    public function pass(Request $request)
    {
        $salesman = Salesman::find($request->input('salesman_id'));
        $supplier = Shop::find($request->input('supplier_id'));
        if ($salesman->shop_id != $salesman->maker_id) {
            return $this->error('已被指派');
        }
        if ($supplier->user_type != cons('user.type.supplier')) {
            return $this->error('客户类型错误');
        }
        $user = auth()->user();
        $shippingAddress = $supplier->user->shippingAddress->first();
        $businessAddress = $supplier->shopAddress;
        $attributes = [
            'number' => $salesman->customers()->max('number') + 1,
            'name' => $supplier->name,
            'belong_shop' => $user->shop_id,
            'letter' => strtoupper(pinyin_abbr($supplier->name)[0]),
            'type' => $supplier->user_type,
            'shop_id' => $supplier->id,
            'contact' => $supplier->contact_person,
            'contact_information' => $supplier->contact_info,
            'business_address_lng' => $supplier->x_lng,
            'business_address_lat' => $supplier->y_lat,
            'shipping_address_lng' => $shippingAddress->x_lng ?? $supplier->x_lng,
            'shipping_address_lat' => $shippingAddress->y_lat ?? $supplier->y_lat,
            'salesman_id' => $salesman->id,
            "business_address" => [
                "province_id" => $businessAddress->province_id,
                "city_id" => $businessAddress->city_id,
                "district_id" => $businessAddress->district_id,
                "street_id" => $businessAddress->street_id,
                "area_name" => $businessAddress->area_name,
                "address" => $businessAddress->address,
            ],
            "shipping_address" => [
                "province_id" => $shippingAddress->address->province_id ?? $businessAddress->province_id,
                "city_id" => $shippingAddress->address->city_id ?? $businessAddress->city_id,
                "district_id" => $shippingAddress->address->district_id ??  $businessAddress->district_id,
                "street_id" => $shippingAddress->address->street_id ?? $businessAddress->street_id,
                "area_name" => $shippingAddress->address->area_name ?? $businessAddress->area_name,
                "address" => $shippingAddress->address->address ?? $businessAddress->address,
            ]
        ];
        try {
            DB::beginTransaction();
            $salesman->customers()->create($attributes);
            $salesman->fill(['shop_id' => $supplier->id])->save();
            $businessRelation = $user->shop->businessRelation->where('supplier_id', $supplier->id)->first();
            $businessRelation->fill(['status' => 1, 'salesman_id' => $salesman->id])->save();
            DB::commit();
            return $this->success('添加客户成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error('添加客户时出现问题');
        }
    }
}
