@extends('index.menu-master')
@include('includes.salesman-order-change')
@include('includes.shipping-address-map')
@section('subtitle')
    业务管理-退货单
@stop

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> >
    <a href="{{ url('business/order/return-orders') }}">退货单</a> >
    <span class="second-level">退货单详情</span>
@stop

@section('right')

    <div class="row order-detail business-detail">
        <div class="col-sm-12 go-history">

                <a class="btn go-back btn-border-blue" href="javascript:history.go(-1)">返回</a>
            @if($order->can_pass)
                <button
                        data-url="{{ url('api/v1/business/order/' . $order->id) }}"
                        data-method="put" data-data='{"status" : "1"}' data-done-url="{{ url('business/order/return-orders') }}"
                        class="btn btn-blue-lighter ajax">通过
                </button>

            @endif
        </div>
        <div class="col-sm-12">
            <div class="row order-receipt">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">退货信息</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center public-table">
                                <thead>
                                <tr>
                                    <td>退货单号</td>
                                    <td>退货时间</td>
                                    <td>业务员</td>
                                    <td>客户名称</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at->toDateString() }}</td>
                                    <td>{{ $order->salesman_name }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">客户信息</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center table-th-color">
                                <thead>
                                <tr>
                                    <th>联系人</th>
                                    <th>联系电话</th>
                                    <th>客户地址</th>
                                </tr>
                                </thead>
                                <tr>
                                    <td>{{ $order->customer_contact }}</td>
                                    <td>{{ $order->salesmanCustomer->contact_information }}</td>
                                    <td>
                                        <p>{{ $order->shipping_address }}</p>
                                        <p class="prop-item">
                                            <a href="javascript:" class="check-address"
                                               data-target="#shippingAddressMapModal"
                                               data-toggle="modal"
                                               data-x-lng="{{ isset($order->shippingAddress)? $order->salesmanCustomer->shipping_address_lng : 0 }}"
                                               data-y-lat="{{ isset($order->shippingAddress)? $order->salesmanCustomer->shipping_address_lat : 0 }}"
                                               data-address="{{ isset($order->shipping_address) ? $order->shipping_address : '' }}"
                                               data-consigner="{{  isset($order->shippingAddress) ? $order->customer_contact : '' }}"
                                               data-phone= {{ isset($order->shippingAddress) ? $order->salesmanCustomer->contact_information : '' }}><i
                                                        class="iconfont icon-chakanditu"></i> 查看地图</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                退货商品
                            </h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center table-th-color">
                                <thead>
                                <th>商品编号</th>
                                <th>商品图片</th>
                                <th>商品名称</th>
                                <th>退货数量</th>
                                <th>单位</th>
                                <th>金额</th>
                                @if($order->can_pass)
                                    <th>操作</th>
                                @endif
                                </thead>
                                @foreach($orderGoods as $goods)
                                    <tr>
                                        <td>{{ $goods->goods_id }}</td>
                                        <td><img class="store-img"
                                                 src="{{ $goods->goods?$goods->goods->image_url:'' }}">
                                        </td>
                                        <td width="30%">
                                            <div class="product-panel">
                                                <a class="product-name"
                                                   href="{{ url('goods/' . $goods->goods_id) }}">{{ $goods->goods_name }}</a>
                                                @if(!empty($goods->promotion_info))
                                                    <p class="promotions">(<span
                                                                class="ellipsis">{{ $goods->promotion_info }}</span>)
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $goods->num }}</td>
                                        <td>{{ cons()->valueLang('goods.pieces', $goods->pieces) }}</td>
                                        <td>{{ $goods->amount }}</td>
                                        @if($order->can_pass)
                                            <td>
                                                <a class="edit" data-toggle="modal" data-target="#salesmanOrder"
                                                   data-id="{{ $goods->id }}"
                                                   data-goods-id="{{ $goods->goods_id }}"
                                                   data-price="{{ $goods->price }}"
                                                   data-num="{{  $goods->num }}"
                                                   data-pieces="{{ $goods->pieces }}"
                                                   data-type="{{ $goods->type }}"
                                                   data-amount="{{ $goods->amount }}">
                                                    <i class="iconfont icon-xiugai"></i>编辑</a>
                                                <a class="red delete-no-form" data-method="delete"
                                                   data-url="{{ url('api/v1/business/order/goods-delete/' . $goods->id) }}">
                                                    <i class="iconfont icon-shanchu"></i>删除
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
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
        $(function () {

            deleteNoForm();
        })


    </script>
@stop
