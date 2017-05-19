<?php

namespace App\Http\Controllers\Api\V1\ChildUser;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests\Api\v1\UpdatePasswordRequest;

use Hash;

class SecurityController extends Controller
{

    public function update(UpdatePasswordRequest $request)
    {
        $password = $request->input('password');

        $oldPassword = $request->input('old_password');
        $user = child_auth()->user();

        if (!Hash::check($oldPassword, $user->password)) {
            return $this->error('原密码不正确');
        }

        if ($user->fill(['password' => $password])->save()) {
            return $this->success('修改密码成功');
        }
        return $this->error('修改失败');
    }


}
