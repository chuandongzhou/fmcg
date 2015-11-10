<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    protected $modelBindings = [
        'App\Models\User' => 'user',
        'App\Models\Category' => 'category',
        'App\Models\Shop' => 'shop',
        'App\Models\Promoter' => 'promoter',
        'App\Models\Advert' => ['advert-index', 'advert-user', 'advert-app'],
        'App\Models\Admin' => 'admin',
        'App\Models\OperationRecord' => 'operation-record',
        'App\Models\Goods' => ['my-goods', 'goods'],
        'App\Models\UserBank' => 'bank',
        'App\Models\DeliveryMan' => 'delivery-man',
        'App\Models\ShippingAddress' => 'shipping-address',
        'App\Models\HomeColumn' => 'column',
        'App\Models\VersionRecord' => 'version-record',

    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        //

        parent::boot($router);
        foreach ($this->modelBindings as $class => $keys) {
            foreach ((array)$keys as $key) {
                $router->model($key, $class);
            }
        }
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
            require app_path('Http/routes.php');
        });
    }
}
