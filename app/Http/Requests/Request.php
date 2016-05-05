<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function authorize()
    {
        return auth()->check() || admin_auth()->check() || delivery_auth()->check();
    }
}
