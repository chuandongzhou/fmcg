<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\V1\Controller;
use App\Services\ChatService;
use Gate;
use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Category;
class ShopController extends Controller
{
    /**
     * 保存店铺
     *
     * @param \App\Http\Requests\Api\v1\UpdateShopRequest $request
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shop(Requests\Api\v1\UpdateShopRequest $request, $shop)
    {
        $attributes = $request->all();
        if (Gate::denies('validate-shop', $shop)) {
            return $this->error('保存失败');
        }
        if ($shop->fill(array_except($attributes, 'id'))->save()) {

            //更新聊天远程
            $shop->load('user');
            (new ChatService())->usersHandle($shop, true);
            return $this->success('保存店铺成功');
        }
        return $this->error('保存店铺时出现错误');
    }

    /**
     * 首页店铺数据
     *
     */
    public function orderData(){
        $month_start = Carbon::now()->startOfMonth();
        $month_end = Carbon::now()->endOfMonth();
        $month = Carbon::now()->formatLocalized('%Y-%m');

        //本月已完成订单
        $finishedOrders = Order::select(DB::raw("count(1) as count,DATE_FORMAT(finished_at,'%Y-%m-%d') as day,status,pay_status,pay_type,is_cancel"))
            ->bySellerId(auth()->id())
            ->whereBetween('finished_at',[$month_start,$month_end])
            ->where('status', cons('order.status.finished'))->nonCancel()->groupBy('day')->get()->each(function($order) {
                $order->setAppends([]);
            });

        //本月付款订单
        $receivedOrders = Order::select(DB::raw("count(1) as count,DATE_FORMAT(paid_at,'%Y-%m-%d') as receivedday,status,pay_status,pay_type,is_cancel"))
            ->bySellerId(auth()->id())
            ->whereBetween('paid_at',[$month_start,$month_end])
            ->where('pay_status',cons('order.pay_status.payment_success'))->nonCancel()->groupBy('receivedday')->get();
        //本月销售图表信息

       // $sellOrdersInfo = Order::bySellerId(auth()->id())->where('status',cons('order.status.finished')) ->whereBetween('finished_at',[$month_start,$month_end])->with('orderGoods.goods')->get();
        $orderGoodsInfo = DB::select('select d.name,c.cate_level_2,sum(b.num) as sum from fmcg_order a join fmcg_order_goods b on a.id = b.order_id join fmcg_goods c on c.id = b.goods_id join fmcg_category d on
 d.id=c.cate_level_2 join fmcg_shop as e on e.id = a.shop_id join fmcg_user f on f.id = e.user_id where f.id = :id and a.`status`= :status and is_cancel = :is_cancel and a.finished_at like :finish_at GROUP BY c.cate_level_2', ['id' =>auth()->id(),'status' => cons('order.status.finished'),'is_cancel' => cons('order.is_cancel.off'),'finish_at'=>'%'.$month.'%']);
 //      dd($orderGoodsInfo);
//        $orderGoodsInfo = Array();

//       foreach($sellOrdersInfo as $sellOrderInfo){
//            foreach($sellOrderInfo->orderGoods as $order_goods){
//
//                $category = Category::where('id',$order_goods->goods->cate_level_2)->select('name')->get()->toArray();
//
//                if( empty($orderGoodsInfo[$order_goods->goods->cate_level_2]['num'])){
//                    $orderGoodsInfo[$order_goods->goods->cate_level_2]['name'] = $category[0]['name'];
//                    $orderGoodsInfo[$order_goods->goods->cate_level_2]['num'] =  $order_goods['num'];
//                }else{
//                    $orderGoodsInfo[$order_goods->goods->cate_level_2]['num'] += $order_goods['num'];
//                }
//
//            }
//       }
        return $this->success(['finishedOrders' =>$finishedOrders,'receivedOrders'=>$receivedOrders,'orderGoodsInfo'=>$orderGoodsInfo]);
    }

}
