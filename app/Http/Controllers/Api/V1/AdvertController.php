<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Advert;
use Illuminate\Support\Facades\Cache;

class AdvertController extends Controller
{

    /**
     * 获取移动端广告
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        //广告
        $indexAdvertConf = cons('advert.cache.index');
        $adverts = [];
        if (Cache::has($indexAdvertConf['name'])) {
            $adverts = Cache::get($indexAdvertConf['name']);
        } else {
            $adverts = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime()->get();
            Cache::put($indexAdvertConf['name'], $adverts, $indexAdvertConf['expire']);
        }
        return $this->success(['advert' => $adverts]);
    }
}
