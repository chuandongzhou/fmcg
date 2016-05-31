<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Promoter;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

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
     * @param $promoter
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Admin\UpdatePromoterRequest $request, $promoter)
    {
        if ($promoter->fill($request->all())->save()) {
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
        $month = $request->input('month', (new Carbon())->format('Y-m'));
        $carbon = new Carbon($month);
        $startOfMonth = clone $carbon->startOfMonth();
        $endOfMonth =clone $carbon->endOfMonth();


        $promoterId = $request->input('id');

        if ($promoterId) {
            $promoters = Promoter::where('id', $promoterId)->with('shops')->get();
        } else {
            $promoters = Promoter::with('shops')->get();
        }
        $promoters->each(function ($promoter) use ($startOfMonth, $endOfMonth) {
            //新注册数
            $promoter->shopRegisterCount = $promoter->shops->filter(function ($item) use ($startOfMonth, $endOfMonth) {
                return $item->created_at >= $startOfMonth && $item->created_at <= $endOfMonth;
            })->count();
            //总用户数  直接 $promoter->shops->count();

            $userIds = $promoter->shops->pluck('user_id');


            //下单
            $submitOrders = Order::NonCancel()->whereIn('user_id', $userIds)->whereBetween('created_at',
                [$startOfMonth, $endOfMonth])->get();
            
            //下单用户数
            $promoter->submitOrdersUsersCount = $submitOrders->pluck('user_id')->toBase()->unique()->count();
            //下单数
            $promoter->submitOrdersCount = $submitOrders->count();
            //下单总金额
            $promoter->submitOrdersAmount = $submitOrders->sum('price');

            //完成的订单
            $finishedOrders = Order::whereIn('user_id', $userIds)->whereBetween('finished_at',
                [$startOfMonth, $endOfMonth])->get();

            //当前月份提交并完成订单
            $currentMonthSubmitOrders = $finishedOrders->filter(function ($item) use ($startOfMonth, $endOfMonth) {
                return $item->created_at >= $startOfMonth && $item->created_at <= $endOfMonth;
            });

            //成单数
            $promoter->finishedOrdersCount = $finishedOrders->count();

            //当前月份提交并完成订单数
            $promoter->currentMonthFinishedOrdersCount = $currentMonthSubmitOrders->count();

            //成单总金额
            $promoter->finishedOrdersAmount = $finishedOrders->sum('price');

            //当前月份提交并完成订单金额
            $promoter->currentMonthFinishedOrdersAmount = $currentMonthSubmitOrders->sum('price');
        });

        return view('admin.promoter.statistics',
            [
                'promoters' => $promoters,
                'month' => $month,
                'promoterId' => $promoterId,
                'allPromoters' => Promoter::lists('name', 'id')
            ]);

    }
}
