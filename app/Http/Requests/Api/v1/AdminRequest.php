<?php

namespace App\Http\Requests\Api\v1;

abstract class AdminRequest extends Request
{

    public function authorize()
    {
        return admin_auth();
    }

}