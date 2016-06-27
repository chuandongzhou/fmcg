<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Models\Salesman;
use App\Services\BusinessService;
use App\Services\SalesmanTargetService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;
use Hash;

class AuthController extends Controller
{
    /**
     * 登录
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

        $salesman = Salesman::where('account', $account)->first();

        if (!$salesman || !Hash::check($password, $salesman->password)) {
            return $this->invalidParam('password', '账号或密码错误');
        }

        if ($salesman->status != cons('status.on')) {
            return $this->invalidParam('password', '账号已锁定');
        }


        $nowTime = Carbon::now();

        if ($salesman->fill(['last_login_at' => $nowTime])->save()) {
            salesman_auth()->login($salesman, true);
            $salesmanData = $this->_getSalesmanData($salesman);
            return $this->success(['salesman' => $salesmanData]);
        }
        return $this->invalidParam('password', '登录失败，请重试');

    }

    /**
     * app首页数据
     *
     * @param \App\Models\Salesman $salesman
     * @return array
     */
    private function _getSalesmanData(Salesman $salesman)
    {
        $thisDate = (new Carbon())->format('Y-m');

        $target = (new SalesmanTargetService)->getTarget($salesman->id, $thisDate);

        //本月已完成订单金额
        $thisMonthCompleted = $salesman->orderForms()->whereBetween('created_at',
            [(new Carbon($thisDate))->startOfMonth(), (new Carbon($thisDate))->endOfMonth()])->sum('amount');

        //未处理订货单数
        $untreatedOrderForms = $salesman->orderForms()->OfUntreated()->count();
        //未处理退货单数
        $untreatedReturnOrders = $salesman->returnOrders()->OfUntreated()->count();

        // 今日拜访数
        $todayVisitCount = $salesman->visits()->whereBetween('created_at',
            [Carbon::today(), (new Carbon())->endOfDay()])->count();

        return compact('target', 'thisMonthCompleted', 'untreatedOrderForms', 'untreatedReturnOrders',
            'todayVisitCount');
    }
}
