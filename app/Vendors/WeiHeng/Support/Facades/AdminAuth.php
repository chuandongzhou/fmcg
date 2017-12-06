<?php

namespace WeiHeng\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WeiHeng\Admin\Guard
 */
class AdminAuth extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'admin.auth';
    }

}