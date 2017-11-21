@extends('index.manage-master')
@section('subtitle', '个人中心-发车详情')
@include('includes.delivery-truck')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--页面中间内容开始-->
            <div class="row">
                <div class="col-sm-12 path-title">配送管理 > <span class="second-level">发车详情</span></div>
            </div>
            <div class="row order-detail">
                <div class="col-sm-12 go-history">
                    <a class="go-back btn btn-border-blue" href="javascript:history.back()"><i
                                class="iconfont icon-fanhui"></i> 返回</a>
                </div>
                <div class="col-sm-12">
                    <div class="row order-receipt">
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">发车信息</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered  table-th-color table-center order-msg-table">
                                        <thead>
                                        <tr>
                                            <th>发车单号</th>
                                            <th>车辆名称</th>
                                            <th>车牌号</th>
                                            <th>配送人</th>
                                            <th>配送订单数</th>
                                            <th>已收金额</th>
                                            <th>未收金额</th>
                                            <th>状态</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{$dispatchTruck->id}}</td>
                                            <td>{{$dispatchTruck->truck->name}}</td>
                                            <td>{{$dispatchTruck->truck->license_plate}}</td>
                                            <td>{!! implode("|",array_column($dispatchTruck->deliveryMans->toArray(), 'name')) !!}</td>
                                            <td>{{$dispatchTruck->orders->count()}}</td>
                                            <td>{{$dispatchTruck->alreadyPaidAmount}}</td>
                                            <td>{{$dispatchTruck->unpaidAmount}}</td>
                                            <td>{{$dispatchTruck->status_name}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">配送订单</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-th-color table-center">
                                        <thead>
                                        <tr>
                                            <th>订单号</th>
                                            <th>订单金额</th>
                                            <th>支付方式</th>
                                            <th>商家名称</th>
                                            <th>联系人</th>
                                            <th>联系方式</th>
                                            <th>联系地址</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($dispatchTruck->orders as $order)
                                            <tr>
                                                <td>{{$order->id}}</td>
                                                <td>{{$order->price}}</td>
                                                <td>{{$order->pay_type_name}}</td>
                                                <td>{{$order->user_shop_name}}</td>
                                                <td>{{$order->user_contact}}</td>
                                                <td>{{$order->user_contact_info}}</td>
                                                <td>{{$order->user_shop_address->area_name}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">配送商品总计</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-th-color table-center">
                                        <thead>
                                        <tr>
                                            <th>商品编号</th>
                                            <th>商品图片</th>
                                            <th>商品名称</th>
                                            <th>商品数量</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($dispatchTruck->order_goods_statis as $goods_statis)
                                            <tr>
                                                <td>{{$goods_statis['goods_id']}}</td>
                                                <td><img class="store-img"
                                                         src="{{$goods_statis['img_url']}}">
                                                </td>
                                                <td width="50%">
                                                    <div class="product-panel">
                                                        <a class="product-name" href="">{{$goods_statis['name']}}</a>
                                                    </div>
                                                </td>
                                                <td>{{$goods_statis['quantity']}}</td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @if(count($dispatchTruck->return_order_goods_statis))
                            <div class="col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">退回商品总计</h3>
                                    </div>
                                    <div class="panel-container table-responsive">
                                        <table class="table table-bordered table-th-color table-center">
                                            <thead>
                                            <tr>
                                                <th>商品编号</th>
                                                <th>商品图片</th>
                                                <th>商品名称</th>
                                                <th>商品数量</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($dispatchTruck->return_order_goods_statis as $goods_statis)
                                                <tr>
                                                    <td>{{$goods_statis['goods_id']}}</td>
                                                    <td><img class="store-img"
                                                             src="{{$goods_statis['img_url']}}">
                                                    </td>
                                                    <td width="50%">
                                                        <div class="product-panel">
                                                            <a class="product-name"
                                                               href="">{{$goods_statis['name']}}</a>
                                                        </div>
                                                    </td>
                                                    <td>{{$goods_statis['quantity']}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">发车单记录</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center">
                                        <tr>
                                            <th>发车单操作</th>
                                            <th>操作时间</th>
                                            <th>操作人</th>
                                        </tr>
                                        @foreach($dispatchTruck->record as $record)
                                            <tr>
                                                <td>{{$record->name}}</td>
                                                <td>{{$record->time}}</td>
                                                <td>{{$record->operater}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        ajaxNoForm();
    </script>
@stop

