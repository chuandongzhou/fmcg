<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * ���ﳵ
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany('app/Cart');
    }

    /**
     * �ղ�
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany('app/Like');
    }

    /**
     * ����
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shops()
    {
        return $this->hasMany('app/Shop');
    }

    /**
     * �󶨵�����
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userBanks()
    {
        return $this->hasMany('app/UserBank');
    }

    /**
     * �ջ���ַ
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivingAddresss(){
        return $this->hasMany('app/ReceivingAddress');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
}
