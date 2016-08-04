<?php

namespace App\Http\Requests\Api\v1;


abstract class UserRequest extends Request
{

    public function authorize()
    {
        return auth()->check() || admin_auth()->check();
    }

}