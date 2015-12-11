<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/25
 * Time: 20:51
 */
namespace App\Http\ViewComposers;

use App\Models\Category;
use App\Models\Node;
use App\Services\CategoryService;
use Illuminate\Contracts\View\View;

class NodeComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $admin = admin_auth()->user();
        $nodes = $admin->name == cons('admin.super_admin_name') ? Node::all() : $admin->role->nodes;
        $view->with('nodes', CategoryService::unlimitForLayer($nodes));
    }

}
