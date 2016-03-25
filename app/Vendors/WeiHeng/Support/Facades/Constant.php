<?php

namespace WeiHeng\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WeiHeng\Constant\Constant
 */
class Constant extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'constant';
    }

}