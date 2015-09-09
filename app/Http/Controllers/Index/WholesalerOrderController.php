<?php

namespace App\Http\Controllers\Index;


class WholesalerOrderController extends OrderController
{
    public $roleType = 1;//默认角色为批发商wholesalers


    /**
     * 处理搜索按钮发送过来的请求
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getSearch(Request $request)
    {
        //TODO:获取当前用户ID号和类型
        $userId = 2;
        $userType = 2;
        $search = $request->all();
        if ($search['search_role'] > $userType) {//查询供应商
            if($search['search_content']){//搜索框查询
                $orderId = trim($search['search_content']);
                if(is_numeric($orderId)){
                    $orders = Order::where('user_id', $userId)->find($orderId);
                }else{
                    $orders = Order::ofUserType($search)->first();
                }
            }else{//下拉列表查询
                $orders = Order::where('user', $userId)->where('payable_type',
                    $search['pay_type'])->where('status', $search['status'])->paginate();
            }

        }else{//查询终端商
            if($search['search_content']){//搜索框查询
                $orderId = trim($search['search_content']);
                if(is_numeric(trim($orderId))){
                    $orders = Order::where('shop_id', $userId)->find($orderId);
                }else {
                    $orders = Order::ofUserType($search)->paginate();
                }
            }else{//下拉列表查询
                $orders = Order::where('seller', $userId)->where('payable_type',
                    $search['pay_type'])->where('status', $search['status'])->paginate();
            }
        }


        dd($orders->toArray());
        return $orders->toArray();
    }

    public function getSelect(Request $request)
    {

        //获取当前用户的ID
        $userId = 2;
        $search = $request->all();
        if ($search['search_role']) {
            $orders = Order::where('shop_id', $sellerId)->where('payable_type', $search['pay_type'])->where('status',
                $search['status'])->paginate();
        }
    }
}
