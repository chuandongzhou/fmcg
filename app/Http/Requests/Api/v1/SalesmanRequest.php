<?php

namespace App\Http\Requests\Api\v1;


abstract class SalesmanRequest extends Request
{

    public function authorize()
    {
        return salesman_auth()->check() || auth()->check() || child_auth()->check();
    }

}