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
    public function getDataByCondition($model, $data, $type = '')
    {
        $prefix = ($type == 'apply') ? 'asset_apply.' : '';
        $model->orderBy('created_at', 'DESC');
        return $model->where(function ($query) use ($data, $prefix) {
            if ($start_at = array_get($data, 'start_at')) {
                $query->where($prefix . 'created_at', '>=', $start_at . ' 00:00:00');
            }
            if ($end_at = array_get($data, 'end_at')) {
                $query->where($prefix . 'created_at', '<=', $end_at . ' 23:59:59');
            }
            if ($use_start_at = array_get($data, 'use_start_at')) {
                $query->where('use_date', '>=', $use_start_at . ' 00:00:00');
            }
            if ($use_end_at = array_get($data, 'use_end_at')) {
                $query->where('use_date', '<=', $use_end_at . ' 23:59:59');
            }

            //资产编号或者名称
            if ($asset = array_get($data, 'asset')) {
                if (empty($prefix)) {
                    $query->where(function ($query) use ($asset) {
                        $is_id = is_numeric($asset);
                        if ($is_id) {
                            $query->where('id', $asset);
                        } else {
                            $query->where('name', 'like', '%' . $asset . '%');
                        }
                    });
                } else {
                    $query->whereHas('asset', function ($query) use ($asset) {
                        $is_id = is_numeric($asset);
                        if ($is_id) {
                            $query->where('id', $asset);
                        } else {
                            $query->where('name', 'like', '%' . $asset . '%');
                        }
                    });
                }

            }
            //业务员
            if (isset($data['salesmen'])) {
                $query->where($prefix . 'salesman_id', $data['salesmen']);
            }
            //审核状态
            if (isset($data['status'])) {
                $query->where($prefix . 'status', $data['status']);
            }
            if ($condition = array_get($data, 'condition')) {
                $query->where(function ($query) use ($condition) {
                    $query->whereHas('asset', function ($query) use ($condition) {
                        $query->where('asset.id', $condition);
                    })->orWhereHas('salesman', function ($query) use ($condition) {
                        $query->where('salesman.name', 'LIKE', '%' . $condition . '%');
                    })->orWhereHas('client', function ($query) use ($condition) {
                        $query->where('salesman_customer.name', 'LIKE', '%' . $condition . '%');
                    });
                });

            }
        });
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
            'opera_type' => auth()->user() ? Shop::class : Salesman::class,
            'operator' => auth()->user() ? auth()->user()->shop->id : salesman_auth()->user()->id
        ];
        $model->log()->create($log);

    }

}