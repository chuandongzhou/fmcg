<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Api\v1\Request as BaseRequest;

abstract class Request extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}