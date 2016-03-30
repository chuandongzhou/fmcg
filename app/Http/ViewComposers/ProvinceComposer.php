<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/25
 * Time: 20:51
 */
namespace App\Http\ViewComposers;

use DB;
use Cache;
use Illuminate\Contracts\View\View;

class ProvinceComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $provinces = [];
        $cacheConf = cons('address.provinces.cache');
        $cacheKey = $cacheConf['name'];
        if (Cache::has($cacheKey)) {
            $provinces = Cache::get($cacheKey);
        }else {
            $provinces = DB::table('address')->where('pid', 1)->lists('name', 'id');
            Cache::forever($cacheKey, $provinces);
        }

        $view->with('provinces', $provinces);
    }

}
