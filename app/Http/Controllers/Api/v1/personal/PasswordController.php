<?php

namespace App\Http\Controllers\Api\v1\personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    protected $userId = 1;


    /**
     * 修改密码
     *
     * @param \App\Http\Requests\Index\UpdatePasswordRequest $request
     */
    public function password(Requests\Index\UpdatePasswordRequest $request)
    {
        $attributes = $request->all();
        if (Auth::user()) {

        }
    }

}
