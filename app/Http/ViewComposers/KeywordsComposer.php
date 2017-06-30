<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/25
 * Time: 20:51
 */
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class KeywordsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $cacheConf = cons('goods.cache');

        $cacheKey = $cacheConf['keywords_pre'] . 'sort';
        $keywords = Cache::get($cacheKey);
        if (!is_null($keywords)) {
            arsort($keywords);
            $keywords = array_slice($keywords, 0, $cacheConf['num'], true);
        }
        $view->with('keywords', $keywords);
    }

}
