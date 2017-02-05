<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Promoter;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use PhpOffice\PhpWord\PhpWord;

class PromoterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        if ($name) {
            $promoters = Promoter::where('name', 'like', $name . '%')->paginate();
        } else {
            $promoters = Promoter::paginate();
        }
        return view('admin.promoter.index', ['promoters' => $promoters, 'name' => $name]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.promoter.promoter', ['promoter' => new Promoter]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreatePromoterRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Admin\CreatePromoterRequest $request)
    {
        $attributes = $request->all();
        $attributes['end_at'] = array_get($attributes, 'end_at') ?: null;
        if (Promoter::create($attributes)->exists) {
            return $this->success('添加推广人员成功');
        }
        return $this->error('添加推广人员时遇到错误');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $promoter
     * @return \Illuminate\View\View
     */
    public function edit($promoter)
    {
        return view('admin.promoter.promoter', ['promoter' => $promoter]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\UpdatePromoterRequest $request
     * @param Promoter $promoter
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Admin\UpdatePromoterRequest $request, $promoter)
    {
        $attributes = $request->all();
        $attributes['end_at'] = array_get($attributes, 'end_at') ?: null;
        if ($promoter->fill($attributes)->save()) {
            return $this->success('修改推广人员成功');
        }
        return $this->error('修改推广人员时遇到错误');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $promoter
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($promoter)
    {
        return $promoter->delete() ? $this->success('删除推广人员成功') : $this->error('删除推广人员时遇到错误');
    }

    /**
     * 批量删除用户
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteBatch(Request $request)
    {
        $promoterIds = (array)$request->input('ids');
        if (empty($promoterIds)) {
            return $this->error('推广人员未选择');
        }
        return Promoter::destroy($promoterIds) ? $this->success('删除推广人员成功') : $this->error('推广人员删除时遇到错误');
    }

    /**
     * 统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statistics(Request $request)
    {
        $dates = $request->all();

        $startDay = array_get($dates, 'start_day', (new Carbon())->startOfMonth()->format('Y-m-d'));
        $endDay = array_get($dates, 'end_day', Carbon::now()->format('Y-m-d'));

        $promoterId = $request->input('id');

        if ($promoterId) {
            $promoters = Promoter::where('id', $promoterId)->with('shops.user')->get();
        } else {
            $promoters = Promoter::with('shops.user')->get();
        }

        $promoters = $this->_formatPromoters($promoters, $startDay, $endDay);

        return view('admin.promoter.statistics',
            [
                'promoters' => $promoters,
                'startDay' => $startDay,
                'endDay' => $endDay,
                'promoterId' => $promoterId,
                'allPromoters' => Promoter::lists('name', 'id')
            ]);

    }

    /**
     * 导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function export(Request $request)
    {
        $dates = $request->all();

        $startDay = array_get($dates, 'start_day', (new Carbon())->startOfMonth()->format('Y-m-d'));
        $endDay = array_get($dates, 'end_day', Carbon::now()->format('Y-m-d'));

        $promoterId = $request->input('id');

        if ($promoterId) {
            $promoters = Promoter::where('id', $promoterId)->with('shops')->get();
        } else {
            $promoters = Promoter::with('shops')->get();
        }

        $promoters = $this->_formatPromoters($promoters, $startDay, $endDay);

        // Creating the new document...
        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');
        $cellAlignCenter = array('align' => 'center');
        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', $styleTable);

        $style = [
            'marginTop' => 500,
            'marginRight' => 0,
            'marginLeft' => 500,
            'marginBottom' => 0,
            'orientation' => 'landscape'
        ];

        $section = $phpWord->addSection($style);
        $table = $section->addTable('table');

        $titles = [
            '推广员' => 1200,
            '推广码' => 1000,
            '注册终端数' => 1500,
            '注册批发商数' => 1800,
            '总注册数量' => 1500,
            '下单客户数' => 1500,
            '成交客户数' => 1500,
            '订单数' => 1000,
            '成交订单数' => 1500,
            '下单金额' => 2000,
            '成交金额' => 1800,
        ];

        //第一行
        $table->addRow(100);
        foreach ($titles as $name => $width) {
            $table->addCell($width, ['fill' => '#f2f2f2'])->addText($name, ['bold' => true], $cellAlignCenter);
        }

        foreach ($promoters as $promoter) {
            $table->addRow(100);
            $table->addCell()->addText($promoter->name, null, $cellAlignCenter);
            $table->addCell()->addText($promoter->spreading_code, null, $cellAlignCenter);
            $table->addCell()->addText($promoter->retailerShopRegisterCount, null, $cellAlignCenter);
            $table->addCell()->addText($promoter->wholesalerShopRegisterCount, null, $cellAlignCenter);
            $table->addCell()->addText($promoter->retailerShopRegisterCount + $promoter->wholesalerShopRegisterCount,
                null, $cellAlignCenter);
            $table->addCell()->addText($promoter->submitOrdersUsersCount, null, $cellAlignCenter);
            $table->addCell()->addText($promoter->finishedOrdersUserCount, null, $cellAlignCenter);
            $table->addCell()->addText($promoter->submitOrdersCount, null, $cellAlignCenter);
            $table->addCell()->addText($promoter->finishedOrdersCount, null, $cellAlignCenter);
            $table->addCell()->addText(number_format($promoter->submitOrdersAmount, 2), null, $cellAlignCenter);
            $table->addCell()->addText(number_format($promoter->finishedOrdersAmount, 2), null, $cellAlignCenter);
        }


        $name = $startDay . '至' . $endDay . '推广统计.docx';
        $phpWord->save($name, 'Word2007', true);


    }

    /**
     * 格式化推广数据
     *
     * @param $promoters
     * @param $startDay
     * @param $endDay
     * @return mixed
     */
    private function _formatPromoters($promoters, $startDay, $endDay)
    {
        $endDay =(new Carbon($endDay))->endOfDay();
        $userType = cons('user.type');
        $promoters->each(function ($promoter) use ($startDay, $endDay, $userType) {
            $supplierShopRegisterCount = 0;
            $wholesalerShopRegisterCount = 0;
            $retailerShopRegisterCount = 0;

            $supplierUserActiveCount = 0;
            $wholesalerUserActiveCount = 0;
            $retailerUserActiveCount = 0;

            foreach ($promoter->shops as $shop) {
                if ($shop->created_at >= $startDay && $shop->created_at <= $endDay) {
                    if ($shop->user_type == $userType['retailer']) {
                        $retailerShopRegisterCount++;
                    } elseif ($shop->user_type == $userType['wholesaler']) {
                        $wholesalerShopRegisterCount++;
                    }else {
                        $supplierShopRegisterCount++;
                    }
                }
                $user = $shop->user;
                if ($user->last_login_at >= $startDay && $user->last_login_at <= $endDay) {
                    if ($user->type == $userType['retailer']) {
                        $retailerUserActiveCount++;
                    } elseif ($user->type == $userType['wholesaler']) {
                        $wholesalerUserActiveCount++;
                    }else {
                        $supplierUserActiveCount++;
                    }
                }
            }
            $promoter->supplierShopRegisterCount = $supplierShopRegisterCount;
            $promoter->wholesalerShopRegisterCount = $wholesalerShopRegisterCount;
            $promoter->retailerShopRegisterCount = $retailerShopRegisterCount;
            $promoter->supplierUserActiveCount = $supplierUserActiveCount;
            $promoter->wholesalerUserActiveCount = $wholesalerUserActiveCount;
            $promoter->retailerUserActiveCount = $retailerUserActiveCount;

            //总用户数  直接 $promoter->shops->count();

            $userIds = $promoter->shops->pluck('user_id');

            //下单
            $submitOrders = Order::NonCancel()->whereIn('user_id', $userIds)->whereBetween('created_at',
                [$startDay, $endDay])->get();

            //下单用户数
            $promoter->submitOrdersUsersCount = $submitOrders->pluck('user_id')->toBase()->unique()->count();
            //下单数
            $promoter->submitOrdersCount = $submitOrders->count();
            //下单总金额
            $promoter->submitOrdersAmount = $submitOrders->sum('price');

            //完成的订单
            $finishedOrders = Order::whereIn('user_id',
                $userIds)->with('user.shop.shopAddress')->whereBetween('finished_at', [$startDay, $endDay])->get();

            //成单客户
            $address = config('address.address');


            $shops = [];
            $users = [];

            foreach ($finishedOrders as $order) {
                $userId = $order->user_id;
                if (!in_array($userId, $users)) {
                    $provinceId = $order->user_shop_address->province_id;
                    $shops[$provinceId] = [
                        'name' => parse_province($address[$provinceId]['name']),
                        'value' => isset($shops[$provinceId]) ? ++$shops[$provinceId]['value'] : 1
                    ];
                    array_push($users, $userId);
                }
            }

            $promoter->shops = json_encode(array_values($shops));

            //成单数
            $promoter->finishedOrdersCount = $finishedOrders->count();

            //当前月份提交并完成订单数
            $promoter->currentMonthFinishedOrdersCount = $finishedOrders->count();

            //成单用户数
            $promoter->finishedOrdersUserCount = $finishedOrders->pluck('user_id')->toBase()->unique()->count();

            //成单总金额
            $promoter->finishedOrdersAmount = $finishedOrders->sum('price');

        });

        return $promoters;
    }
}
