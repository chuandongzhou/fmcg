<?php

namespace WeiHeng\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HengCaoTang\Pushbox\Adapter\Ums
 */
class Sms extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pushbox.sms';
    }

}
