<?php

namespace App\Models;

use App\Services\UserService;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class ChildUser extends Model implements AuthenticatableContract
{

    use SoftDeletes;
    use Authenticatable;

    protected $table = 'child_user';

    protected $fillable = ['account', 'password', 'name', 'phone', 'user_id', 'shop_id', 'last_login_at'];

    /**
     * 缓存数据的来源
     *
     * @return array
     */
    protected static function cacheSource()
    {
        return static::sort()->get()->toArray();
    }

    //关联用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //关联店铺
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * 关联节点
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function indexNodes()
    {
        return $this->belongsToMany(IndexNode::class, 'child_user_node');
    }

    /**
     * 排序
     *
     * @param $query
     * @return mixed
     */
    public function scopeSort($query)
    {
        return $query->orderBy('sort', 'ASC')->orderBy('id', 'ASC');
    }

    /**
     * 密码加密
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        if ($password) {
            $this->attributes['password'] = md5($password);
        }
    }

    /**
     * 获取用户类型
     *
     * @return mixed|\WeiHeng\Constant\Constant
     */
    public function getTypeAttribute()
    {
        return $this->user ? $this->user->type : cons('user.type.wholesaler');
    }

    /**
     * 获取店铺名
     *
     * @return int|mixed|string
     */
    public function getShopNameAttribute()
    {
        $userService = new UserService(true);

        if ($shopName = $userService->getShopDetail($this->user_id, 'name')) {
            return $shopName;
        }
        return $userService->setShopDetail($this, 'name');
    }

    /**
     * 是否过期
     *
     * @return mixed
     */
    public function getIsExpireAttribute()
    {
        return $this->user->is_expire;
    }

    /**
     * 获取第一个可访问节点
     *
     * @return mixed
     */
    public function getFirstNodeAttribute()
    {
        return $this->indexNodes->first(function ($key, $item) {
            return 'GET' == $item->method && $item->active == 1;
        });
    }

    /**
     * 获取用户节点
     *
     * @return mixed
     */
    public function getNodesAttribute()
    {
        return app('child.user')->cacheNodes($this);
    }

}
