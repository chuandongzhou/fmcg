<?php

namespace WeiHeng\SignManage;


use App\Models\Model;
use App\Models\User;
use Carbon\Carbon;

class Sign
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 获取工人数
     *
     * @return string
     */
    public function workerCount()
    {
        $shop = auth()->user()->shop;

        $salesmanCount = $shop->salesmen->count();
        $deliveryManCount = $shop->deliveryMans->count();

        return bcadd($salesmanCount, $deliveryManCount);
    }

    /**
     * 续费
     *
     * @param \App\Models\Model $model
     * @param int|string $cost
     * @param User $user
     * @return bool
     */
    public function renew(Model $model, $cost, User $user = null)
    {
        //添加续费记录

        $costConf = $model instanceof User ? cons()->valueLang('sign.expire_amount') : cons()->valueLang('sign.worker_expire_amount');

        $renewTypeName = array_get($costConf, $cost);

        $user = $user ?: auth()->user();

        $startTime = is_null($model->expire_at) || $model->expire_at->isPast() ? Carbon::now() : $model->expire_at;

        if (preg_match('/^\d+$/', $renewTypeName)) {
            $month = $renewTypeName;
            $renewTypeName = $renewTypeName . '个月';
        } else {
            $month = bcmul(intval($renewTypeName), 12);
        }

        $newTime = $startTime->addMonth($month)->endOfDay();

        $user->renews()->create([
                'renew_type' => $renewTypeName,
                'cost' => $cost,
                'detail' => $model->model_name . '缴费' . $renewTypeName . '成功',
                'old_expire_at' => $model->expire_at
            ]
        );

        return $model->fill(['expire_at' => $newTime])->save();
    }

}