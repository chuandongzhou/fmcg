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

class ChildNodeComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $childUser = child_auth()->user();

        $nodes = $childUser->indexNodes()->active()->where('active', 1)->get();

        $view->with('nodes', CategoryService::unlimitForLayer($nodes));
    }

}
