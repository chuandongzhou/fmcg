<?php

namespace App\Services;

use App\Models\ShippingAddress;
use App\Models\ShippingAddressSnapshot;


/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class ShippingAddressService
{
    public function copyToSnapshot($shippingAddressId)
    {
        if (!$shippingAddressId) {
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
            return false;
        }
        $addressData = $shippingAddress->toArray();
        unset($addressData['id']);
        $shippingAddressSnapshot = ShippingAddressSnapshot::create($addressData);
        if ($shippingAddressSnapshot->exists) {
            $addressDetail = $shippingAddress->address;

            if (!$addressDetail) {
                $shippingAddressSnapshot->delete();
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
        return false;

    }

}