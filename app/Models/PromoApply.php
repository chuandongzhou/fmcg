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
     * 客户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Shop');
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

    public function scopeOfSalesman($query, $salesman)
    {
        return $query->whereHas('salesman', function ($query) use ($salesman) {
            $query->where('name', 'LIKE', '%' . $salesman . '%');
        });
    }
}
