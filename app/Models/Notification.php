<?php

namespace App\Models;


use Carbon\Carbon;

class Notification extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notification';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'title', 'content', 'user_id', 'is_admin'];


    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Admin', 'user_id');
    }

    /**
     * 筛选类型
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int|null $type
     * @return mixed
     */
    public function scopeOfType($query, $type = null)
    {
        if (!(is_null($type) || $type == 'all')) {
            $query->where('type', $type);
        }

        return $query;
    }

    /**
     * 筛选用户
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param null|int $userId
     * @param bool $isAdmin
     * @return mixed
     */
    public function scopeOfUser($query, $userId = null, $isAdmin = false)
    {
        if (is_null($userId)) {
            if ($userId = admin_auth()->id()) {
                $isAdmin = true;
            } else {
                $userId = auth()->id();
                $isAdmin = false;
            }
        }

        return $query->where(function ($query) use ($userId, $isAdmin) {
            $query->where('user_id', $userId)->where('is_admin', intval($isAdmin));
        });
    }


    /**
     * 储存一条用户通知
     *
     * @param int $type 通知类型
     * @param string $title 通知标题
     * @param string $content 通知内容
     * @param int|null $userId 用户ID
     * @param bool $isAdmin 是否管理员
     * @return bool
     */
    public static function notifyUser($type, $title, $content, $userId = null, $isAdmin = false)
    {
        if (is_null($userId)) {
            if ($userId = admin_auth()->id()) {
                $isAdmin = true;
            } else {
                $userId = auth()->id();
                $isAdmin = false;
            }
        }

        if (empty($userId)) {
            return false;
        }

        $notification = static::create([
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'user_id' => $userId,
            'is_admin' => $isAdmin ? 1 : 0,
        ]);

        return $notification->exists;
    }

    /**
     * 按时间搜索
     *
     * @param $query
     * @param $dates
     * @return mixed
     */
    public function scopeOfDates($query, $dates)
    {
        return $query->where(function ($query) use ($dates) {
            if ($beginDay = array_get($dates, 'begin_day')) {
                $query->where('created_at', '>', $beginDay);
            }
            if ($endDay = array_get($dates, 'end_day')) {
                $endDay = (new Carbon($endDay))->endOfDay();
                $query->where('created_at', '<', $endDay);
            }
        });
    }
}
