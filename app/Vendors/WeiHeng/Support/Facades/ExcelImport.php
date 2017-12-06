<?php

namespace WeiHeng\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WeiHeng\ExcelImport\ExcelImport
 */
class ExcelImport extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'excel.import';
    }

}