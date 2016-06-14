<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/25
 * Time: 20:51
 */
namespace App\Http\ViewComposers;

use App\Services\CategoryService;
use Illuminate\Contracts\View\View;
use Cache;

class CategoryComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $categories = CategoryService::getCategories();

        $view->with('categories', CategoryService::unlimitForLayer($categories, 0, 'child', false));
    }

}
