<?php

namespace App\Http\Controllers\Admin;

use App\Models\DataStatistics;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Services\DataStatisticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Display a listing of the resource
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $startTime = new Carbon($request->input('start_time'));
        $endTime = new Carbon($request->input('end_time'));

        $dayEnd = $endTime->copy()->endOfDay();
        $dayStart = $startTime->copy()->startOfDay();
        $nowTime = Carbon::now();

        /**
         * 总用户数
         *
         * $totalUser = User::select(DB::raw('count(*) as num , type'))->whereBetween('created_at',
         * [$dayStart, $dayEnd])->groupBy('type')->lists('num', 'type');
         */
        if ($dayStart == $nowTime->startOfDay()) {
            $statistics = DataStatisticsService::getTodayDataStatistics($nowTime->copy()->addDay(), false);
        } else {
            //$statistics = DataStatistics::whereBetween('created_at', [$dayStart->toDateString(), $dayEnd->toDateString()])->first();

            // $monthAgo = $carbon->copy()->subDays(30);

            // 活跃用户数
            $statistics = DataStatistics::whereBetween('created_at', [
                $dayStart,
                $dayEnd
            ])->select(DB::raw(
                'sum(`retailer_login_num`) as retailer_login_num
                ,sum(`wholesaler_login_num`) as wholesaler_login_num
                ,sum(`supplier_login_num`) as supplier_login_num
                ,sum(`retailer_reg_num`) as retailer_reg_num
                ,sum(`wholesaler_reg_num`) as wholesaler_reg_num
                ,sum(`supplier_reg_num`) as supplier_reg_num
                '))->first();

        }

        //历史最高注册数和登录数

        /**
         * 'wholesaler' => 1,       //批发商
         * 'retailer' => 2,       //零售商
         * 'supplier' => 3,
         */

        $dataStatistics = DataStatistics::whereBetween('created_at', [$dayStart, $dayEnd])->get();

        $maxArray = [
            'max_wholesaler_login_num' => $dataStatistics->sortByDesc('wholesaler_login_num')->first(),
            'max_retailer_login_num' => $dataStatistics->sortByDesc('retailer_login_num')->first(),
            'max_supplier_login_num' => $dataStatistics->sortByDesc('supplier_login_num')->first(),
            'max_wholesaler_reg_num' => $dataStatistics->sortByDesc('wholesaler_reg_num')->first(),
            'max_retailer_reg_num' => $dataStatistics->sortByDesc('retailer_reg_num')->first(),
            'max_supplier_reg_num' => $dataStatistics->sortByDesc('supplier_reg_num')->first(),
        ];
        /*
         * 每日订单统计
         */
        $orders = Order::whereBetween('created_at', [$dayStart, $dayEnd])->where('is_cancel',
            cons('order.is_cancel.off'))->get();

        $orderEveryday = []; //当日订单统计

        if (!$orders->isEmpty()) {

            $userIds = $orders->pluck('user_id')->all();

            $users = User::whereIn('id', array_unique($userIds))->lists('type', 'id');  // 买家类型

            $userType = cons('user.type');
            $payType = cons('pay_type');

            foreach ($orders as $order) {
                //买家
                if ($users[$order->user_id] == $userType['retailer']) {
                    //终端
                    $orderEveryday['retailer']['count'] = isset($orderEveryday['retailer']['count']) ? ++$orderEveryday['retailer']['count'] : 1;
                    $orderEveryday['retailer']['amount'] = isset($orderEveryday['retailer']['amount']) ? $orderEveryday['retailer']['amount'] + $order->price : $order->price;
                    if ($order->pay_type == $payType['online']) {
                        //线上总金额
                        $orderEveryday['retailer']['onlineAmount'] = isset($orderEveryday['retailer']['onlineAmount']) ? $orderEveryday['retailer']['onlineAmount'] + $order->price : $order->price;
                    } else {
                        //线下总金额
                        $orderEveryday['retailer']['codAmount'] = isset($orderEveryday['retailer']['codAmount']) ? $orderEveryday['retailer']['codAmount'] + $order->price : $order->price;
                    }

                } elseif ($users[$order->user_id] == $userType['wholesaler']) {
                    //批发
                    $orderEveryday['wholesaler']['count'] = isset($orderEveryday['wholesaler']['count']) ? ++$orderEveryday['wholesaler']['count'] : 1;
                    $orderEveryday['wholesaler']['amount'] = isset($orderEveryday['wholesaler']['amount']) ? $orderEveryday['wholesaler']['amount'] + $order->price : $order->price;
                    if ($order->pay_type == $payType['online']) {
                        //线上总金额
                        $orderEveryday['wholesaler']['onlineAmount'] = isset($orderEveryday['wholesaler']['onlineAmount']) ? $orderEveryday['wholesaler']['onlineAmount'] + $order->price : $order->price;

                    } else {
                        //线下总金额
                        $orderEveryday['wholesaler']['codAmount'] = isset($orderEveryday['wholesaler']['codAmount']) ? $orderEveryday['wholesaler']['codAmount'] + $order->price : $order->price;
                    }
                }
            }
        }


        /*
        * 每日成单统计
        */
        $completeOrders = Order::whereBetween('paid_at', [$dayStart, $dayEnd])->with('systemTradeInfo')->where('status',
            cons('order.pay_status.payment_success'))->where('is_cancel',
            cons('order.is_cancel.off'))->get();

        $orderSellerEveryday = [];  //对于卖家每日统计

        if (!$completeOrders->isEmpty()) {
            $userIds = $orders->pluck('user_id')->all();
            $users = User::whereIn('id', array_unique($userIds))->lists('type', 'id');  // 买家类型

            $shopIds = $orders->pluck('shop_id')->all();
            $shops = Shop::whereIn('id', array_unique($shopIds))->with('user')->get(['id', 'user_id']);
            $sellerTypes = [];                                                          // 卖家类型
            foreach ($shops as $shop) {
                $sellerTypes[$shop->id] = $shop->user->type;
            }

            $userType = cons('user.type');
            $payType = cons('pay_type');

            foreach ($completeOrders as $order) {
                if ($sellerTypes[$order->shop_id] == $userType['wholesaler']) {
                    //批发商
                    //成单数
                    $orderSellerEveryday['wholesaler']['count'] = isset($orderSellerEveryday['wholesaler']['count']) ? ++$orderSellerEveryday['wholesaler']['count'] : 1;
                    //成单总金额
                    $orderSellerEveryday['wholesaler']['amount'] = isset($orderSellerEveryday['wholesaler']['amount']) ? $orderSellerEveryday['wholesaler']['amount'] + $order->price : $order->price;
                    if ($order->pay_type == $payType['online']) {
                        // 线上完成总金额
                        $orderSellerEveryday['wholesaler']['onlineSuccessAmount'] = isset($orderSellerEveryday['wholesaler']['onlineSuccessAmount']) ? $orderSellerEveryday['wholesaler']['onlineSuccessAmount'] + $order->price : $order->price;
                    } else {
                        //线下完成总金额
                        $orderSellerEveryday['wholesaler']['codSuccessAmount'] = isset($orderSellerEveryday['wholesaler']['codSuccessAmount']) ? $orderSellerEveryday['wholesaler']['codSuccessAmount'] + $order->price : $order->price;
                        //线下pos机完成总金额
                        if ($order->systemTradeInfo && $order->systemTradeInfo->pay_type == cons('trade.pay_type.pos')) {
                            $orderSellerEveryday['wholesaler']['posSuccessAmount'] = isset($orderEveryday['wholesaler']['posSuccessAmount']) ? $orderEveryday['wholesaler']['posSuccessAmount'] + $order->price : $order->price;
                        }
                    }
                } elseif ($sellerTypes[$order->shop_id] == $userType['supplier']) {
                    //供应商
                    if ($users[$order->user_id] == $userType['wholesaler']) {
                        //对于批发商
                        //成单数
                        $orderSellerEveryday['supplier']['wholesaler']['count'] = isset($orderSellerEveryday['supplier']['wholesaler']['count']) ? ++$orderSellerEveryday['supplier']['wholesaler']['count'] : 1;
                        //成单总金额
                        $orderSellerEveryday['supplier']['wholesaler']['amount'] = isset($orderSellerEveryday['supplier']['wholesaler']['amount']) ? $orderSellerEveryday['supplier']['wholesaler']['amount'] + $order->price : $order->price;
                        if ($order->pay_type == $payType['online']) {
                            //线上成单总金额
                            $orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount'] = isset($orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount']) ? $orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount'] + $order->price : $order->price;
                        } else {
                            //线下成单总金额
                            $orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount'] = isset($orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount']) ? $orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount'] + $order->price : $order->price;
                            //线下pos机成单总金额
                            if ($order->systemTradeInfo && $order->systemTradeInfo->pay_type == cons('trade.pay_type.pos')) {
                                $orderSellerEveryday['supplier']['wholesaler']['posSuccessAmount'] = isset($orderEveryday['supplier']['wholesaler']['posSuccessAmount']) ? $orderEveryday['supplier']['wholesaler']['posSuccessAmount'] + $order->price : $order->price;
                            }

                        }
                    } else {
                        //对于终端商
                        //成单数
                        $orderSellerEveryday['supplier']['retailer']['count'] = isset($orderSellerEveryday['supplier']['retailer']['count']) ? ++$orderSellerEveryday['supplier']['retailer']['count'] : 1;
                        //成单总金额
                        $orderSellerEveryday['supplier']['retailer']['amount'] = isset($orderSellerEveryday['supplier']['retailer']['amount']) ? $orderSellerEveryday['supplier']['retailer']['amount'] + $order->price : $order->price;
                        if ($order->pay_type == $payType['online']) {
                            //线上完成总金额
                            $orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount'] = isset($orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount']) ? $orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount'] + $order->price : $order->price;

                        } else {
                            //线下完成总金额
                            $orderSellerEveryday['supplier']['retailer']['codSuccessAmount'] = isset($orderSellerEveryday['supplier']['retailer']['codSuccessAmount']) ? $orderSellerEveryday['supplier']['retailer']['codSuccessAmount'] + $order->price : $order->price;
                            //线下pos机完成总金额
                            if ($order->systemTradeInfo && $order->systemTradeInfo->pay_type == cons('trade.pay_type.pos')) {
                                $orderSellerEveryday['supplier']['retailer']['posSuccessAmount'] = isset($orderEveryday['supplier']['retailer']['posSuccessAmount']) ? $orderEveryday['supplier']['retailer']['posSuccessAmount'] + $order->price : $order->price;
                            }
                        }
                    }
                }
            }
        }

        return view('admin.statistics.statistics',
            [
                'statistics' => $statistics,
                'maxArray' => $maxArray,
                'startTime' => $dayStart,
                'endTime' => $dayEnd,
                'orderEveryday' => $orderEveryday,
                'orderSellerEveryday' => $orderSellerEveryday
            ]);
    }
}
