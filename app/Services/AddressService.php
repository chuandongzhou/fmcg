<?php

namespace App\Services;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class AddressService
{

    protected $array = [];

    public function __construct($array)
    {
        $this->array = $array;
        return $this;
    }

    /**
     * 格式化标签
     *
     * @return array
     */
    public function formatAddressPost()
    {
        $array = $this->array;
        if (!is_array($array)) {
            return [];
        }
        $addressArr = [];
        foreach ($array['id'] as $key => $addressId) {
            if ($array['province_id'][$key]) {
                $addressArr[] = [
                    'id' => $addressId,
                    'province_id' => $array['province_id'][$key],
                    'city_id' => $array['city_id'][$key],
                    'district_id' => $array['district_id'][$key],
                    'street_id' => $array['street_id'][$key],
                    'detail_address' => $array['detail_address'][$key]
                ];
            }
        }
        return $addressArr;
    }
}