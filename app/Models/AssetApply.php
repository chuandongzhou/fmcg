<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 资产申请使用与审核
 * Class AssetReview
 *
 * @package App\Models
 */
class AssetApply extends Model
{
    use SoftDeletes;
    protected $table = 'asset_apply';
    protected $fillable = [
        'asset_id',
        'client_id',
        'quantity',
        'use_date',
        'apply_remark',
        'status',
    ];

    protected $appends = [];

    protected $hidden = [];

    /**
     * 所属资产
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset()
    {
        return $this->belongsTo('App\Models\Asset');
    }

    /**
     * 客户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Models\SalesmanCustomer', 'client_id');
    }

    /**
     * 操作日志
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function log()
    {
        return $this->hasMany('App\Models\AssetApplyLog');
    }

    /**
     * 业务员
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesman()
    {
        return $this->belongsTo('App\Models\Salesman', 'salesman_id');
    }

    /**
     * 获取通过时间
     *
     * @return mixed
     */
    public function getPassDateAttribute()
    {
        $log = $this->log->filter(function ($log) {
            return $log->action == cons('asset_apply_log.action.review');
        })->first();
        return (string) ($log->created_at ?? '');
    }

}
