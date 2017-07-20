<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\Salesman;
use App\Services\BusinessService;
use Carbon\Carbon;
use App\Http\Requests;
use App\Services\SalesmanTargetService;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use Gate;
use Hash;

class SalesmanController extends Controller
{

    /**
     * 添加业务员
     *
     * @param \App\Http\Requests\Api\v1\CreateSalesManRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Requests\Api\v1\CreateSalesManRequest $request)
    {
        $attributes = $request->except('status');
        $user = auth()->user();
        if ($user->type == cons('user.type.maker')) {
            $attributes['shop_id'] = $user->shop_id;
        }
        if ($user->shop->salesmen()->create($attributes)->exists) {
            return $this->success('添加业务员成功');
        }
        return $this->error('添加业务员是出现错误');
    }

    /**
     * 业务员详情
     *
     * @param $salesman
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function show($salesman)
    {
        if (Gate::denies('validate-salesman', $salesman)) {
            return $this->error('业务员不存在');
        }
        return $this->success(['salesman' => $salesman]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\UpdateSalesmanRequest $request
     * @param $salesman
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(Requests\Api\v1\UpdateSalesmanRequest $request, Salesman $salesman)
    {
        if (Gate::denies('validate-salesman', $salesman)) {
            return $this->error('业务员不存在');
        }
        $attributes = $request->all();
        if ($salesman->fill(array_except($attributes, 'account'))->save()) {
            return $this->success('保存业务员成功');
        }
        return $this->error('保存业务员是出现错误');
    }

    /**
     * 修改密码
     *
     * @param \App\Http\Requests\Api\v1\UpdatePasswordRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function password(Requests\Api\v1\UpdatePasswordRequest $request)
    {
        $attributes = $request->all();

        $user = salesman_auth()->user();

        if (Hash::check($attributes['old_password'], $user->password)) {
            if ($user->fill(['password' => $attributes['password']])->save()) {
                return $this->success('修改密码成功');
            }
            return $this->error('修改密码时遇到错误');

        }
        return $this->error('原密码错误');
    }

    /**
     * Update the specified resource by App
     *
     * @param \App\Http\Requests\Api\v1\UpdateSalesmanRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function updateByApp(Requests\Api\v1\UpdateSalesmanRequest $request)
    {
        $salesman = salesman_auth()->user();
        $attributes = $request->all();
        if ($salesman->fill(array_except($attributes, 'account'))->save()) {
            return $this->success('保存业务员成功');
        }
        return $this->error('保存业务员是出现错误');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $salesman
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy($salesman)
    {
        if (Gate::denies('validate-salesman', $salesman)) {
            return $this->error('业务员不存在');
        }
        return $salesman->delete() ? $this->success('删除业务员成功') : $this->error('删除业务员失败');
    }

    /**
     * 批量删除业务员
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function batchDelete(Request $request)
    {
        $salesmanId = $request->input('salesman_id');
        if (empty($salesmanId)) {
            return $this->error('请选择要删除的业务员');
        }
        return auth()->user()->shop->salesmen()->whereIn('id',
            $salesmanId)->delete() ? $this->success('删除业务员成功') : $this->error('删除业务员时出现问题');
    }

    /**
     * 设置业务员目标
     *
     * @param \App\Http\Requests\Api\v1\UpdateSalesmanTargetRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function targetSet(Requests\Api\v1\UpdateSalesmanTargetRequest $request)
    {
        $data = $request->all();

        $salesman = auth()->user()->shop->salesmen()->find($data['salesman_id']);

        if (is_null($salesman)) {
            return $this->error('业务员不存在');
        }
        $result = (new SalesmanTargetService())->setTarget($data['salesman_id'], $data['date'], $data['target']);

        return $result ? $this->success('目标设置成功') : $this->success('更新目标成功');
    }

    /**
     * app首页数据
     *
     * @return array
     */
    public function homeData()
    {
        $salesman = salesman_auth()->user();
        $thisDate = (new Carbon())->format('Y-m');

        $target = (new SalesmanTargetService)->getTarget($salesman->id, $thisDate);

        //本月已完成订单金额
        $thisMonthCompleted = $salesman->orderForms()->whereBetween('created_at',
                [(new Carbon($thisDate))->startOfMonth(), (new Carbon($thisDate))->endOfMonth()])->sum('amount') ?? 0;

        //未处理订货单数
        $untreatedOrderForms = $salesman->orderForms()->OfUntreated()->count() ?? 0;
        //未处理退货单数
        $untreatedReturnOrders = $salesman->returnOrders()->OfUntreated()->count() ?? 0;

        // 今日拜访数
        $todayVisitCount = $salesman->visits()->whereBetween('created_at',
                [
                    Carbon::today(),
                    (new Carbon())->endOfDay()
                ])->select('salesman_customer_id')->groupBy('salesman_customer_id')->get()->count() ?? 0;
        return compact('target', 'thisMonthCompleted', 'untreatedOrderForms', 'untreatedReturnOrders',
            'todayVisitCount');
    }

    /**
     * 解冻/冻结
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postLock(Request $request)
    {
        $salesman = Salesman::find($request->input('id'));
        $status = $request->input('status') == cons('status.off') ? 1 : 0;
        $salesman->status = $status;
        if ($salesman->save()) {
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function exportTarget(Request $request)
    {
        $date = $request->input('date', (new Carbon())->format('Y-m'));

        $salesmenOrderData = (new BusinessService())->getSalesmanOrders(auth()->user()->shop,
            (new Carbon($date))->startOfMonth(), (new Carbon($date))->endOfMonth());
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');
        $cellAlignCenter = array('align' => 'center');


        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        $targetService = new SalesmanTargetService();

        $section = $phpWord->addSection();
        $table = $section->addTable('table');

        //第一行
        $table->addRow(16);
        $table->addCell(2000)->addText('业务员', null, $cellAlignCenter);
        $table->addCell(1500)->addText('月份目标', null, $cellAlignCenter);
        $table->addCell(1500)->addText('订货总金额', null, $cellAlignCenter);
        $table->addCell(1500)->addText('完成率', null, $cellAlignCenter);
        $table->addCell(1500)->addText('退货总金额', null, $cellAlignCenter);

        foreach ($salesmenOrderData as $salesman) {
            $table->addRow(16);
            $table->addCell(2000)->addText($salesman->name, null, $cellAlignCenter);
            $table->addCell(1500)->addText($targetService->getTarget($salesman->id, $date), null, $cellAlignCenter);
            $table->addCell(1500)->addText($salesman->orderFormSumAmount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($targetService->getTarget($salesman->id,
                $date) ? percentage($salesman->orderFormSumAmount,
                $targetService->getTarget($salesman->id, $date)) : '100%', null, $cellAlignCenter);
            $table->addCell(1500)->addText($salesman->returnOrderSumAmount, null, $cellAlignCenter);
        }

        $name = auth()->user()->shop->name . $date . '业务员目标' . '.docx';
        $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);
    }
}
