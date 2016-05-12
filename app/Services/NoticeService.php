<?php

namespace App\Services;

use App\Models\Notice;
use Cache;
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class NoticeService
{

    public function getNotice()
    {
        $indexNoticeConf = cons('notice.index.cache');
        $notices = [];
        if (Cache::has($indexNoticeConf['name'])) {
            $notices = Cache::get($indexNoticeConf['name']);
        } else {
            $notices = Notice::orderBy('id', 'desc')->take(cons('notice.index.count'))->get();
            Cache::put($indexNoticeConf['name'], $notices, $indexNoticeConf['expire']);
        }
        return $notices;
    }
}