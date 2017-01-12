<?php

namespace App\Listeners;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateAdminNotification
{

    /**
     * 监听的消息
     *
     * @var array
     */
    protected $events = [
        'eloquent.created' => [
            'App\Models\Role' => 'role-create',
            'App\Models\Admin' => 'admin-create',
            'App\Models\User' => 'user-create',
            'App\Models\Category' => 'category-create',
            'App\Models\Attr' => 'attr-create',
            'App\Models\Images' => 'images-create',
            'App\Models\Advert' => 'advert-create',
            'App\Models\Promoter' => 'promoter-create', //推广人员添加
            'App\Models\VersionRecord' => 'version-recode-create',
            'App\Models\ShopColumn' => 'shop-column-create',
            'App\Models\Notice' => 'notice-create'
        ],
        'eloquent.updated' => [
            'App\Models\Role' => 'role-update',
            'App\Models\Admin' => 'admin-update',
            'App\Models\Category' => 'category-update',
            'App\Models\Attr' => 'attr-update',
            'App\Models\Advert' => 'advert-update',
            'App\Models\Promoter' => 'promoter-update', //推广人员添加
            'App\Models\ShopColumn' => 'shop-column-update',
            'App\Models\Notice' => 'notice-update'
        ],
    ];

    protected $textTemplates = [
        'role-create' => '角色 [<a href="/admin/role/{$id}/edit">{$name}</a>] 添加成功',
        'admin-create' => '管理员账户 [<a href="/admin/admin/{$id}/edit">{$name}</a>] 添加成功，所属角色：{$role.name}',
        'user-create' => '用户 [<a href="/admin/shop/{$shop_id}/edit">{$name}</a>] 注册成功',
        'category-create' => '商品分类 [<a href="/admin/category/{$id}/edit">{$name}</a>] 添加成功',
        'attr-create' => '商品标签 [<a href="/admin/attr/{$id}/edit">{$name}</a>] 添加成功',
        'images-create' => '商品图片条形码 [<a href="/admin/images">{$bar_code}</a>] 添加成功',
        'advert-create' => '广告 [{$name}] 添加成功',
        'promoter-create' => '推广人员 [<a href="/admin/promoter">{$name}</a>] 添加成功，联系方式：{$contact}',
        'version-recode-create' => '版本有新的更新，版本名：{$version_name},版本号：{$version_no} [<a href="/admin/operation">点击</a>] 查看',
        //'goods-column-create' => '版本有新的更新，版本名：{$version_name},版本号：{$version_no} [<a href="/admin/version-record">点击</a>] 查看',
        'shop-column-create' => '店铺栏目 [<a href="/admin/shop-column/{$id}/edit">{$name}</a>] 添加成功',
        'notice-create' => '公告 [<a href="/admin/notice/{$id}/edit">{$name}</a>] 添加成功',

        'role-update' => '角色 [<a href="/admin/role/{$id}/edit">{$name}</a>] 有新的更新',
        'admin-update' => '管理员账户 [<a href="/admin/admin/{$id}/edit">{$name}</a>] 有新的更新',
        'category-update' => '商品分类 [<a href="/admin/category/{$id}/edit">{$name}</a>] 有新的更新',
        'attr-update' => '商品标签 [<a href="/admin/attr/{$id}/edit">{$name}</a>] 有新的更新',
        'advert-update' => '广告 [{$name}] 添加成功',
        'promoter-update' => '推广人员 [<a href="/admin/promoter">{$name}</a>] 有新的更新',
        'shop-column-update' => '店铺栏目 [<a href="/admin/shop-column/{$id}/edit">{$name}</a>] 有新的更新',
        'notice-update' => '公告 [<a href="/admin/notice/{$id}/edit">{$name}</a>] 有新的更新',
    ];

    /**
     * Create the event handler.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function handle(Model $model)
    {
        list($event, $name) = explode(': ', \Event::firing());

        $names = array_get($this->events, $event);
        if (empty($names)) {
            return;
        }

        $type = array_get($names, $name);
        if (empty($type)) {
            return;
        }

        $request = app('request');

        if (!str_contains($request->path(), 'admin')) {
            return;
        }

        $textTemplate = array_get($this->textTemplates, $type);
        $text = $this->formatText($model, $textTemplate);
        if (empty($text)) {
            return;
        }

        Notification::notifyUser(cons('admin.notification.', $type), cons()->lang('admin.notification')[$type], $text);
    }

    /**
     * 格式化字符串
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $template
     * @return string
     */
    protected function formatText(Model $model, $template)
    {
        // 替换变量
        return preg_replace_callback('/{\$([^\${}]*)}/', function ($match) use ($model) {
            return object_get_ex($model, $match[1]);
        }, $template);
    }

}
