<?php

namespace WeiHeng\ExcelImport;

use Illuminate\Support\ServiceProvider;

class ExcelImportServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('excel.import', function ($app) {
            return new ExcelImport();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['excel.import'];
    }

}
