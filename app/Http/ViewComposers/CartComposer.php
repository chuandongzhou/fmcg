<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/25
 * Time: 20:51
 */
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Cache;

class CartComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = auth()->user();
        $cartCount = 0;
        if ($user) {
            $cacheKey = 'cart:' . $user->id;
            if (Cache::has($cacheKey)) {
                $cartCount = Cache::get($cacheKey);
            } else {
                $cartCount = $user->carts->count();
                Cache::forever($cacheKey, $cartCount);
            }
        }
       /* $cartCount = auth()->user() ? auth()->user()->carts->count() : 0;*/
        $view->with('cartNum', $cartCount);
    }

}
