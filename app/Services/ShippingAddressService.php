<?php

namespace App\Services;

use App\Models\SalesmanCustomer;
use App\Models\ShippingAddress;
use App\Models\ShippingAddressSnapshot;


/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class ShippingAddressService extends BaseService
{
    /**
     * 复制收货地址至快照
     *
     * @param $shippingAddressId
     * @return bool|mixed
     * @throws \Exception
     */
    public function copyToSnapshot($shippingAddressId)
    {
        if (!$shippingAddressId) {
            $this->setError('收货地址为空');
            return false;
        }
        $shippingAddress = ShippingAddress::where('id', $shippingAddressId)->first([
            'id',
            'consigner',
            'phone',
            'user_id',
            'user_id',
            'x_lng',
            'y_lat'
        ]);

        if (!$shippingAddress) {
            $this->setError('收货地址为空');
            return false;
        }
        $addressData = $shippingAddress->toArray();
        unset($addressData['id']);
        $shippingAddressSnapshot = ShippingAddressSnapshot::create($addressData);
        if ($shippingAddressSnapshot->exists) {
            $addressDetail = $shippingAddress->address;

            if (!$addressDetail) {
                $shippingAddressSnapshot->delete();
                $this->setError('收货地址为空');
                return false;
            }
            $shippingAddressSnapshot->address()->create([
                'province_id' => $addressDetail->province_id,
                'city_id' => $addressDetail->city_id,
                'district_id' => $addressDetail->district_id,
                'street_id' => $addressDetail->street_id,
                'area_name' => $addressDetail->area_name,
                'address' => $addressDetail->address,
            ]);
            return $shippingAddressSnapshot->id;
        }
        $this->setError('创建收货地址错误');
        return false;

    }

    /**
     * 复制用户收货地址至快照
     *
     * @param \App\Models\SalesmanCustomer $salesmanCustomer
     * @return bool|mixed
     */
    public function copySalesmanCustomerShippingAddressToSnapshot(SalesmanCustomer $salesmanCustomer)
    {
        $addressData = [
            'consigner' => $salesmanCustomer->contact,
            'phone' =>$salesmanCustomer->contact_information,
            'user_id' => 0,
            'x_lng' =>$salesmanCustomer->shipping_address_lng,
            'y_lat' => $salesmanCustomer->shipping_address_lat
        ];

        $shippingAddressSnapshot = ShippingAddressSnapshot::create($addressData);
        if ($shippingAddressSnapshot->exists) {
            $addressDetail = $salesmanCustomer->shippingAddress;

            if (!$addressDetail) {
                $shippingAddressSnapshot->delete();
                $this->setError('收货地址为空');
                return false;
            }
            $shippingAddressSnapshot->address()->create([
                'province_id' => $addressDetail->province_id,
                'city_id' => $addressDetail->city_id,
                'district_id' => $addressDetail->district_id,
                'street_id' => $addressDetail->street_id,
                'area_name' => $addressDetail->area_name,
                'address' => $addressDetail->address,
            ]);
            return $shippingAddressSnapshot->id;
        }
        $this->setError('创建收货地址错误');
        return false;
    }

    /**
     * 验证收货地址
     *
     * @param $shippingAddressId
     * @param null $userId
     * @param null $shops
     * @return mixed
     */
    public function validate($shippingAddressId, $userId = null, $shops = null)
    {
        $userId = is_null($userId) ? auth()->id() : $userId;

        $shippingAddress = ShippingAddress::where('user_id', $userId)->find($shippingAddressId);

        if (is_null($shippingAddress)) {
            $this->setError('收货地址不存在');
            return false;
        }
        if (!is_null($shops)) {
            return (new ShopService())->getShopMinMoneyByShippingAddress($shops, $shippingAddress, true);
            /*$address = $shippingAddress->address;
            $where = [
                'province_id' => $address->province_id,
                'city_id' => $address->city_id,
                'district_id' => $address->district_id,
            ];

            foreach ($shops as $shop) {
                $delivery = $shop->deliveryArea()->where(array_filter($where))->first();

                $minMoney = ($delivery && $delivery->min_money) ? $delivery->min_money : $shop->min_money;

                if ($shop->sum_price < $minMoney) {
                    return false;
                }
            }*/

        }

        return true;
    }

}