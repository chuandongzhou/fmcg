<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use Hash;

class PasswordController extends Controller
{
    protected $userId = 1;


    /**
     * @param \App\Http\Requests\Api\v1\UpdatePasswordRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function password(Requests\Api\v1\UpdatePasswordRequest $request)
    {
        $attributes = $request->all();

        $user = auth()->user();

        if (Hash::check($attributes['old_password'], $user->password))
        {
           if ($user->fill(['password' => $attributes['password']])->save()){
                return $this->success('修改密码成功');
           }
            return $this->error('修改密码时遇到错误');

        }
        return $this->error('原密码错误');
    }

}
