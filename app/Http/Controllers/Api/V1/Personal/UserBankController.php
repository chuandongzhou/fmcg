<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests;
use App\Models\UserBank;

class UserBankController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth()->user();

    }

    /**
     * 获取银行卡
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        //获取该角色名下的所有银行账号
        return $this->success(['user_bank_cards' => UserBank::where('user_id', $this->user->id)->get()]);
    }

    /**
     * 获取银行信息
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function banks()
    {
        return $this->success(['banks' => cons()->valueLang('bank.type')]);
    }

    /**
     * 设置默认银行
     *
     * @param $bankInfo
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function bankDefault($bankInfo)
    {
        $userId = $bankInfo['user_id'];
        if ($userId != $this->user->id) {
            return $this->error('要修改的账号不存在');
        }
        if ($bankInfo['is_default'] == 1) {
            return $this->success('设置成功');
        }
        // 设置些用户其它银行账号默认

        $this->user->userBanks()->where('is_default', 1)->update(['is_default' => 0]);

        if ($bankInfo->fill(['is_default' => 1])->save()) {
            return $this->success('设置成功');
        }

        return $this->error('设置失败，请重试');
    }

    /**
     * 添加
     *
     * @param \App\Http\Requests\Api\v1\CreateUserBankRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Requests\Api\v1\CreateUserBankRequest $request)
    {
        if ($this->user->userBanks()->create($request->all())->exists) {
            return $this->success('添加账号成功');
        }

        return $this->success('添加账号时出现问题');
    }

    /**
     * 修改
     *
     * @param \App\Http\Requests\Api\v1\UpdateUserBankRequest $request
     * @param $userBank
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(Requests\Api\v1\UpdateUserBankRequest $request, $userBank)
    {
        if ($userBank->fill($request->all())->save()) {
            return $this->success('保存成功');
        }

        return $this->success('保存账号时出现问题');
    }

    /**
     * 删除
     *
     * @param \App\Models\UserBank $bank
     * @return \WeiHeng\Responses\Apiv1Response
     * @throws \Exception
     */
    public function destroy(UserBank $bank)
    {
        //检查提现订单中是否有已通过但是还未打款的
        if ($bank->withdraws()->whereIn('status', [cons('withdraw.review'), cons('withdraw.pass')])->get()->count()) {
            return $this->error('该账号下有提现订单未打款,暂不能删除');
        }
        if ($bank->delete()) {
            return $this->success('删除银行账号成功');
        }

        return $this->error('删除时遇到问题');
    }
}
