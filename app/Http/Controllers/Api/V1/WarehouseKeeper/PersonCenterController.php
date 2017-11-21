<?php

namespace App\Http\Controllers\Api\V1\WarehouseKeeper;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Support\Facades\Hash;

class PersonCenterController extends Controller
{
    /**
     * 修改密码
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function modifyPassword(Request $request)
    {
        $user = wk_auth()->user();
        $old = $request->input('old_password');
        if (!Hash::check($old, $user->password)) {
            return $this->error('原密码错误!');
        }
        return $user->update(['password' => $request->input('new_password')]) ? $this->success('修改成功') : $this->error('修改失败');
    }
}
