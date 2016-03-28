<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Advert;
use Carbon\Carbon;
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
        $nowTime = Carbon::now();
        //广告
        $indexAdvertConf = cons('advert.cache.index');
        $adverts = [];
        if (Cache::has($indexAdvertConf['name'])) {
            $adverts = Cache::get($indexAdvertConf['name']);
        } else {
            $adverts = Advert::with('image')->where('type',
                cons('advert.type.index'))->OfTime($nowTime)->get()->each(function ($advert) {
                $advert->setAppends(['image_url'])->addHidden(['image', 'type', 'start_at', 'end_at']);
            });
            Cache::put($indexAdvertConf['name'], $adverts, $indexAdvertConf['expire']);
        }
        return $this->success(['advert' => $adverts]);
    }
}
