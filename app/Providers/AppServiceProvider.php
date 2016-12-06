<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh');
        // SQL查询日志记录
        if (config('app.debug')) {
            \DB::listen(function ($query, $bindings, $time, $name) {
                $path = app('request')->path();
                $data = compact('bindings', 'time', 'name', 'path');

                // Format binding data for sql insertion
                foreach ($bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else if (is_string($binding)) {
                        $bindings[$i] = "'$binding'";
                    }
                }

                // Insert bindings into query
                $query = str_replace(['%', '?'], ['%%', '%s'], $query);
                $finalQuery = vsprintf($query, $bindings);

                info($finalQuery, $data);
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerContainerAliases();
    }

    /**
     * 注册容器别名
     */
    protected function registerContainerAliases()
    {

        $aliases = [
            'admin.auth' => \WeiHeng\Admin\Guard::class,
            'delivery.auth' => \WeiHeng\Delivery\Guard::class,
            'salesman.auth' => \WeiHeng\Salesman\Guard::class,
        ];

        foreach ($aliases as $key => $value) {
            foreach ((array)$value as $alias) {
                $this->app->alias($key, $alias);
            }
        }
    }

}
