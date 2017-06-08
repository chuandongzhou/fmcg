<?php
/**
 * User: Dong
 * Date: 2017/5/8
 * Time: 11:00
 */
namespace App\Services;

use App\Models\AssetApply;
use App\Models\Salesman;
use App\Models\Shop;

class AssetService
{
    /**
     * 根据条件获取数据
     *
     * @param $model
     * @param $data
     * @return mixed
     */
    public function getDataByCondition($model, $data)
    {
        $prefix = ($model->first() instanceof AssetApply) ? 'asset_apply.' : '';
        return $model->where(function ($query) use ($data, $prefix) {
            if ($start = array_get($data, 'start_at')) {
                $query->where($prefix . 'created_at', '>=', $start);
            }
            if ($end_at = array_get($data, 'end_at')) {
                $query->where($prefix . 'created_at', '<=', $end_at);
            }

            if ($use_start_at = array_get($data, 'use_start_at')) {
                $query->where('use_date', '>=', $use_start_at);

            }
            if ($use_end_at = array_get($data, 'use_end_at')) {
                $query->where('use_date', '<=', $use_end_at);
            }
            if ($name = array_get($data, 'name')) {
                if (!empty($prefix)) {
                    $query->whereHas('asset', function ($query) use ($name) {
                        $query->where('name', 'LIKE', '%' . $name . '%');
                    });
                } else {
                    $query->where('name', 'LIKE', '%' . $name . '%');
                }

            }
            if ($asset = array_get($data, 'asset')) {
                $query->where(function ($query) use ($asset) {
                    $query->whereHas('asset', function ($query) use ($asset) {
                        $field = is_numeric($asset) ? 'asset.id' : 'asset.name';
                        $query->where($field, 'like', '%' . $asset . '%');
                    });
                });
            }
            if (isset($data['salesmen'])) {
                $query->where($prefix . 'salesman_id', $data['salesmen']);
            }
            if (isset($data['status'])) {
                $query->where($prefix . 'status', $data['status']);
            }
            if ($condition = array_get($data, 'condition')) {
                $query->where(function ($query) use ($condition) {
                    $query->whereHas('asset', function ($query) use ($condition) {
                        $query->where('asset.id', $condition);
                    });
                })->orWhere(function ($query) use ($condition) {
                    $query->whereHas('salesman', function ($query) use ($condition) {
                        $query->where('salesman.name', 'LIKE', '%' . $condition . '%');
                    });
                })->orWhere(function ($query) use ($condition) {
                    $query->whereHas('client', function ($query) use ($condition) {
                        $query->where('shop.name', 'LIKE', '%' . $condition . '%');
                    });
                });
            }
        });
    }

    /**
     * 获取店铺资产
     *
     * @return mixed
     */
    public function getShopAsset()
    {
        return auth()->user()->shop->asset()->get();
    }

    /**
     * 创建日志
     *
     * @return mixed
     */
    public function createLog($model, $action)
    {
        $log = [
            'action' => cons('asset_apply_log.action.' . $action),
            'opera_type' => auth()->user()->shop ? Shop::class : Salesman::class,
            'operator' => auth()->user()->shop ? auth()->user()->shop->id : auth()->user()->id
        ];
        $model->log()->create($log);

    }

}