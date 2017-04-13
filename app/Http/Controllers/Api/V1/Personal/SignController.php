<?php

namespace App\Http\Controllers\Api\V1\Personal;


use App\Http\Controllers\Api\V1\Controller;

class SignController extends Controller
{

    /**
     * 获取工人数
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getWorkerCount()
    {
        return $this->success(['workerCount' => app('sign')->workerCount()]);
    }

    /**
     * 保证金缴纳
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postDeposit()
    {
        $user = auth()->user();

        if ($user->deposit) {
            return $this->error('该用户已缴纳');
        }

        return $this->success();
    }
}
