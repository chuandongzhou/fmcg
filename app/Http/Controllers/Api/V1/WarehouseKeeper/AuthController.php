<?php

namespace App\Http\Controllers\Api\V1\WarehouseKeeper;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\WarehouseKeeper;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * 登录处理
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function login(Request $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');
        if (!$account || !$password) {
            return $this->invalidParam('password', '账号或密码不能为空');
        }
        $wk = WarehouseKeeper::where('account', $account)->first();
        if (!$wk || !Hash::check($password, $wk->password)) {
            return $this->invalidParam('password', '账号或密码错误');
        }
        wk_auth()->login($wk);
        return $this->success(['wk' => $wk]);
    }

    /**
     * 登出
     */
    public function logout()
    {
        return wk_auth()->logout();
    }
}
