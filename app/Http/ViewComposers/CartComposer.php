<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/25
 * Time: 20:51
 */
namespace App\Http\ViewComposers;

use App\Services\CartService;
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
        $cartService = new CartService;
        $cartCount = 0;
        if ($user) {
            if ($cartService->has()) {
                $cartCount = $cartService->get();
            } else {
                $cartCount = $user->carts->count();
                $cartService->set($cartCount);
            }
        }
        $view->with('cartNum', $cartCount);
    }

}
