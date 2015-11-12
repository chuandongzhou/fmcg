<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/25
 * Time: 20:51
 */
namespace App\Http\ViewComposers;

use App\Models\Category;
use App\Services\CategoryService;
use Carbon\Carbon;
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
        $categories = [];
        if (Cache::has('categories')) {
            $categories = Cache::get('categories');
        } else {
            $categories = CategoryService::unlimitForLayer(Category::get()->each(function ($category) {
                $category->setAppends([]);
            })->toArray());
            $expiresAt = Carbon::now()->addDays(1);
            Cache::put('categories', $categories, $expiresAt);
        }

        $view->with('categories', $categories);
    }

}
