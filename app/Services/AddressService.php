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

    public function __construct($array = [])
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
        if (!is_array($array) || empty($array['id'])) {
            return [];
        }
        $addressArr = [];
        foreach ($array['id'] as $key => $addressId) {
            if ($array['province_id'][$key]) {
                $addressArr[] = [
                    'id' => $addressId,
                    'province_id' => isset($array['province_id'][$key]) ? $array['province_id'][$key] : 0,
                    'city_id' => isset($array['city_id'][$key]) ? $array['city_id'][$key] : 0,
                    'district_id' => isset($array['district_id'][$key]) ? $array['district_id'][$key] : 0,
                    'street_id' => isset($array['street_id'][$key]) ? $array['street_id'][$key] : 0,
                    'area_name' => isset($array['area_name'][$key]) ? $array['area_name'][$key] : 0,
                    'address' => isset($array['address'][$key]) ? $array['address'][$key] : 0,
                    'extra_common_param' => isset($array['min_money'][$key]) ? $array['min_money'][$key] : '',
                    'coordinate' => [
                        'bl_lng' => isset($array['blx'][$key]) ? $array['blx'][$key] : 0,
                        'bl_lat' => isset($array['bly'][$key]) ? $array['bly'][$key] : 0,
                        'sl_lng' => isset($array['slx'][$key]) ? $array['slx'][$key] : 0,
                        'sl_lat' => isset($array['sly'][$key]) ? $array['sly'][$key] : 0,
                    ]
                ];
            }
        }

        return $addressArr;
    }

    /**
     * 获取地址数据
     *
     * @return array
     */
    public function getAddressData()
    {
        $address = config('address.address');
        $provinceCookie = request()->cookie('province_id');

        if ($provinceCookie && array_get($address, $provinceCookie)) {
            $provinceId = $provinceCookie;
        } else {
            $provinceId = auth()->check() && auth()->user()->shop->shopAddress ? auth()->user()->shop->shopAddress->province_id : cons('address.default_province');
        }
        $provinceName = array_get($address, $provinceId . '.name');
        $cities = array_get($address, $provinceId . '.child');
        $cityCookie = request()->cookie('city_id');
        $cityId = $cityCookie && array_get($cities, $cityCookie) ? $cityCookie : key($cities);
        $cityName = array_get($cities, $cityId . '.name', '');;
        return [
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'address_name' => $provinceName . $cityName
        ];
    }
}