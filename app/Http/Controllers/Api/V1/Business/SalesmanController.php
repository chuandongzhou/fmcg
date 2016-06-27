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

class SalesmanController extends Controller
{
    protected $shop;

    public function __construct()
    {
        $this->shop = auth()->user()->shop;
    }

    /**
     * 添加业务员
     *
     * @param \App\Http\Requests\Api\v1\CreateSalesManRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Requests\Api\v1\CreateSalesManRequest $request)
    {
        $attributes = $request->all();
        if ($this->shop->salesmen()->create($attributes)->exists) {
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
     * @param \App\Http\Requests\Api\V1\UpdateSalesmanRequest $request
     * @param $salesman
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(Requests\Api\V1\UpdateSalesmanRequest $request, $salesman)
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
     * Update the specified resource by App
     * @param \App\Http\Requests\Api\V1\UpdateSalesmanRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function updateByApp(Requests\Api\V1\UpdateSalesmanRequest $request)
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
     * @param  Salesman $salesman
     * @return \Illuminate\Http\Response
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
        return $this->shop->salesmen()->whereIn('id',
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

        $salesman = $this->shop->salesmen()->find($data['salesman_id']);

        if (is_null($salesman)) {
            return $this->error('业务员不存在');
        }
        $result = (new SalesmanTargetService())->setTarget($data['salesman_id'], $data['date'], $data['target']);

        return $result ? $this->success('目录设置成功') : $this->success('更新目标成功');
    }

    /**
     * 导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function exportTarget(Request $request)
    {
        $date = $request->input('date', (new Carbon())->format('Y-m'));

        $salesmenOrderData = (new BusinessService())->getSalesmanOrders($this->shop,
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

        $name = $this->shop->name . $date . '业务员目标' . '.docx';
        $phpWord->save($name, 'Word2007', true);
    }
}
