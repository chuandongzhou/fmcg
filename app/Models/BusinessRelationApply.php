<?php

namespace App\Models;

class BusinessRelationApply extends Model
{
    protected $table = 'business_relation_apply';

    protected $fillable = [
        'maker_id',
        'supplier_id',
        'salesman_id',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * 关联厂商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function maker()
    {
        return $this->belongsTo(Shop::class, 'maker_id');
    }

    /**
     * 关联供应商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function supplier()
    {
        return $this->belongsTo(Shop::class, 'supplier_id');
    }

    /**
     * 关联业务员
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function salesman()
    {
        return $this->belongsTo(Salesman::class, 'salesman_id');
    }

    /**
     * 获取角色对象
     * @return string
     */
    public function getObject()
    {
        return check_role('maker') ? 'supplier' : 'maker';
    }

    /**
     *  名称
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->{$this->getObject()}->name ?? '';
    }

    /**
     * 账号
     *
     * @return string
     */
    public function getAccountAttribute()
    {
        return $this->{$this->getObject()}->user->user_name ?? '';
    }

    /**
     * 联系人
     *
     * @return string
     */
    public function getContactAttribute()
    {
        return $this->{$this->getObject()}->contact_person ?? '';
    }

    /**
     * 厂家联系人电话
     *
     * @return string
     */
    public function getContactMobileAttribute()
    {
        return $this->{$this->getObject()}->contact_person ?? '';
    }

    /**
     * 营业地址
     *
     * @return string
     */
    public function getAddressAttribute()
    {
        return $this->{$this->getObject()}->address ?? '';
    }

    /**
     * 收货地址
     *
     * @return string
     */
    public function getShippingAddressAttribute()
    {
        $shipping = $this->{$this->getObject()}->user->shippingAddress->where('is_default', 1)->first();
        return $shipping ? $shipping->address_name : $this->getAddressAttribute();
    }

    /**
     * 获取业务员名
     *
     * @return string
     */
    public function getSalesmanNameAttribute()
    {
        return $this->salesman->name ?? '';
    }

    /**
     * 获取状态名
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return $this->status ? '已通过' : '处理中';
    }

}
