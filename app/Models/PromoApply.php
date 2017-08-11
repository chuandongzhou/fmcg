<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class PromoApply extends Model
{
    use SoftDeletes;
    protected $table = 'promo_apply';
    protected $fillable = [
        'promo_id',
        'client_id',
        'status',
        'salesman_id',
        'use_date',
        'pass_date',
        'apply_remark',
        'deleted_at',
    ];

    /**
     * 所属促销
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function promo()
    {
        return $this->belongsTo('App\Models\Promo');
    }

    /**
     *关联订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne(SalesmanVisitOrder::class, 'apply_promo_id');
    }

    /**
     * 客户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Models\SalesmanCustomer', 'client_id');
    }

    /**
     * 业务员信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesman()
    {
        return $this->belongsTo('App\Models\Salesman');
    }


    /**
     * 以编号或名称检索
     *
     * @param $query
     * @param $numberName
     * @return mixed
     */
    public function scopeOfNumberName($query, $numberName)
    {
        if (is_numeric(trim($numberName))) {
            return $query->where('promo_apply.id', $numberName);
        } else {
            return $query->whereHas('promo', function ($query) use ($numberName) {
                $query->where('promo.name', 'LIKE', '%' . $numberName . '%');
            });
        }


    }

    //通过且没被使用条件
    public function scopePass($query)
    {
        $query->where('status', cons('promo.review_status.pass'))->whereNull('use_date');
    }

    //指定客户
    public function scopeClient($query, $client_id)
    {
        $query->where('client_id', $client_id);
    }

    /**
     * 以销售人员名称搜索
     *
     * @param $query
     * @param $salesman
     * @return mixed
     */
    public function scopeOfSalesman($query, $salesman)
    {
        return $query->whereHas('salesman', function ($query) use ($salesman) {
            $query->where('name', 'LIKE', '%' . $salesman . '%');
        });
    }

    /**
     * 获取客户名
     *
     * @return mixed
     */
    public function getClientNameAttribute()
    {
        return $this->client->name;
    }

    /**
     * 获取业务员名
     *
     * @return mixed
     */
    public function getSalesmanNameAttribute()
    {
        return $this->salesman->name;
    }
}
