<?php

namespace App\Http\Controllers\Api\v1\Business;

use App\Services\PromoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Api\V1\Controller;

class PromoController extends Controller
{
    protected $user;

    //构造方法验证身份
    public function __construct()
    {
        $this->middleware('maker_salesman');
        $this->user = salesman_auth()->user();
    }

    //业务员获取厂家活动列表
    public function index()
    {
        //获取启用且有效的活动(活动日期内)
        $promos = $this->user->maker->promo()->with([
            'condition.goods',
            'rebate.goods',
        ])->where('status', '1')
            ->active()
            ->get()
            ->each(function ($item) {
                $item->addHidden(['shop_id', 'updated_at', 'status', 'created_at', 'deleted_at']);
            });
        return $this->success(['promos' => $promos->toArray()]);
    }

    /**
     * 业务员获取促销活动申请记录列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyList(Request $request)
    {
        $data = $request->all();
        $applyPromoModel = $this->user->applyPromo()->with([
            'client',
            'client.businessAddress',
            'promo',
            'promo.condition.goods',
            'promo.rebate.goods'
        ]);
        $applyLists = (new PromoService())->applyLogSearch($applyPromoModel, $data)->orderBy('created_at',
            'DESC')->get()->each(function ($item) {
            $item->setHidden(['promo_id', 'client_id', 'deleted_at', 'updated_at', 'salesman_id']);
        });
        return $this->success(compact('applyLists'));
    }

    /**
     * 业务员获取审核通过的申请
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyPass(Request $request)
    {
        $promos = collect();
        $applyLists = $this->user->applyPromo()->with([
            'promo' => function ($promo) {
                $promo->active();
            },
            'promo.condition.goods',
            'promo.rebate.goods'
        ])->pass()->client($request->input('client_id'))->get();
        $applyLists->each(function ($item) use (
            $promos
        ) {
            if ($item->promo) {
                $item->promo->addHidden(['created_at', 'updated_at', 'status', 'deleted_at']);
                $item->promo->apply_id = $item->id;
                $item->promo->is_use = is_null($item->order) ? 0 : 1;
                $promos->push($item->promo->toArray());
            }
        });
        $promos->toArray();
        return $this->success(compact('promos'));
    }

    /**
     * 促销活动申请
     *
     * @param \App\Http\Requests\Api\v1\PromoApplyRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyCreate(Requests\Api\v1\PromoApplyRequest $request)
    {
        $data = $request->all();
        $user = $this->user;
        //验证客户
        $customer = $user->customers->pluck('id')->toArray();
        if (!in_array($data['client_id'], $customer)) {
            return $this->error('未知的客户!');
        }
        //验证促销活动是否为可申请状态

        $promo = $user->maker->promo()->find($data['promo_id']);
        $now = Carbon::now();
        if (!($promo && $promo->status && $promo->start_at <= $now && $promo->end_at >= $now)) {
            return $this->error('不可申请!');
        }
        if ($user->applyPromo()->create($data)) {
            return $this->success('操作成功,请等待审核!');
        }
        return $this->error('操作失败');
    }

    /**
     * 删除
     *
     * @param $apply
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyDelete($apply)
    {
        if (Gate::forUser(salesman_auth()->user())->denies('validate-salesman-promoApply', $apply)) {
            return $this->error('没有权限!');
        }
        return $apply->delete() ? $this->success('删除成功') : $this->error('删除失败');
    }
}
