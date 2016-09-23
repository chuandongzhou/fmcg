<?php

namespace App\Services;

use App\Models\Advert;
use App\Models\Shop;
use App\Models\ShopColumn;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use QrCode;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class ShopService extends RedisService
{

    protected $subName = 'shop-user';

    public function __construct($connectionRedis = false)
    {
        $connectionRedis && parent::__construct();
    }

    public function getShopColumn()
    {
        $type = auth()->user()->type;

        $homeColumnShopConf = cons('home_column.shop');


        $shopColumns = [];

        $addressData = (new AddressService)->getAddressData();
        $data = array_except($addressData, 'address_name');

        $cacheKey = $homeColumnShopConf['cache']['pre_name'] . $type . ':' . $data['province_id'] . ':' . $data['city_id'];

        if (Cache::has($cacheKey)) {
            $shopColumns = Cache::get($cacheKey);
        } else {
            //商家
            $shopColumns = ShopColumn::get();

            foreach ($shopColumns as $shopColumn) {
                $shops = Shop::whereIn('id', $shopColumn->id_list)
                    ->OfUser($type)
                    ->OfDeliveryArea(array_filter($data))
                    ->with('images', 'logo', 'user')
                    ->get()
                    ->each(function ($shop) {
                        $shop->setAppends(['image_url', 'logo']);
                    });
                $columnShopsCount = $shops->count();
                if ($columnShopsCount < 10) {
                    $columnShopsIds = $shops->pluck('id')->toArray();
                    $ShopsBySort = Shop::whereNotIn('id', $columnShopsIds)
                        ->OfUser($type)
                        ->with('images', 'logo', 'user')
                        ->{'Of' . ucfirst(camel_case($shopColumn->sort))}()
                        ->OfDeliveryArea(array_filter($data))
                        ->take(10 - $columnShopsCount)
                        ->get()->each(function ($shop) {
                            $shop->setAppends(['image_url', 'logo']);
                        });
                    $shops = $shops->merge($ShopsBySort);
                }
                $shopColumn->shops = $shops;
            }
            Cache::put($cacheKey, $shopColumns, $homeColumnShopConf['cache']['expire']);
        }
        return $shopColumns;
    }


    /**
     * 获取店铺二维码头像
     *
     * @param int $uid
     * @param $size
     * @return string
     */
    public function qrcode($uid = 0, $size = null)
    {
        $qrcodePath = config('path.upload_qrcode');
        $relatePath = str_replace(public_path(), '', $qrcodePath);
        $qrcodeSize = is_null($size) ? cons('shop.qrcode_size') : $size;
        // 处理分割后的ID
        $path = implode('/', divide_uid($uid, "/{$qrcodeSize}.png"));

        if (!is_file($qrcodePath . $path)) {
            @mkdir(dirname($qrcodePath . $path), 0777, true);
            QrCode::format('png')->size($qrcodeSize)->margin(0)->generate(url('shop/' . $uid), $qrcodePath . $path);
        }
        // 处理缓存
        $mtime = @filemtime($qrcodePath . $path);
        if (false !== $mtime) {
            return asset($relatePath . $path) . '?' . $mtime;
        }
        return asset($relatePath . $path);
    }

    /**
     * 商店图片为空时获取首页广告的第一张图
     *
     * @return mixed
     */
    public function getAdvertFirstImage()
    {
        $advert = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime()->first();
        return $advert->image ? $advert : new Advert;
    }

    /**
     * 获取店铺默认收货地址
     *
     * @param $shop
     * @return mixed
     */
    public function getDefaultShippingAddress($shop)
    {
        if ($shop instanceof Shop) {
            return $shop->user->shippingAddress->first();
        } else if (is_int($shop)) {
            $shop = Shop::find($shop);
            return self::getDefaultShippingAddress($shop);
        }
    }

    /**
     * 根据收货地址找出店铺最低配送额
     *
     * @param $shops
     * @param $shippingAddress
     * @param bool $validate
     * @return array|bool
     */
    public function getShopMinMoneyByShippingAddress($shops, $shippingAddress, $validate = false)
    {
        $address = $shippingAddress->address;
        $where = [
            'province_id' => $address->province_id,
            'city_id' => $address->city_id,
            'district_id' => $address->district_id,
        ];

        $shopMinMoneys = [];
        foreach ($shops as $shop) {
            $delivery = $shop->deliveryArea()->where(array_filter($where))->first();

            $minMoney = ($delivery && $delivery->min_money) ? $delivery->min_money : $shop->min_money;

            if ($validate) {
                if ($shop->sum_price < $minMoney) {
                    return false;
                }
            }
            $shopMinMoneys[] = [
                'shop_id' => $shop->id,
                'min_money' => $minMoney
            ];
        }
        return $validate ? true : $shopMinMoneys;
    }

    /**
     * 获取shopId
     *
     * @param $account
     * @return mixed
     */
    public function getShopIdByAccount($account)
    {
        if (!$account) {
            return null;
        }
        $user = User::where('user_name', $account)->first();
        return $user->shop_id;
    }


    /**
     *  获取店铺详情
     *
     * @param $shopId
     * @param $field
     * @return int|string
     */
    public function getUserDetail($shopId, $field)
    {
        $key = $this->getKey($this->subName . ':' . $shopId);

        if (!$this->redis->exists($key)) {
            return 0;
        }
        return $this->redis->hget($key, $field);
    }

    /**
     * 设置店铺详情
     *
     * @param $shop
     * @param string $returnField
     * @return mixed
     */
    public function setUserDetail($shop, $returnField = 'id')
    {
        $key = $this->getKey($this->subName . ':' . $shop->id);

        if ($this->redis->exists($key)) {
            return true;
        }
        $user = $shop->user;

        $value = [
            'id' => $user->id,
            'name' => $user->user_name,
            'type' => $user->type,
            'mobile' => $user->backup_mobile
        ];
        $this->redis->hmset($key, $value);
        return $value[$returnField];
    }

}