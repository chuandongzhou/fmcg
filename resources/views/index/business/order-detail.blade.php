@extends('index.menu-master')
@include('includes.salesman-order-change')
@section('subtitle')
    业务管理-{{ $order->type == cons('salesman.order.type.order') ? '订货单' : '退货单' }}
@stop

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;

    @if($order->type == cons('salesman.order.type.order'))
        <a href="{{ url('business/order/order-forms') }}">订货单</a> &rarr;
        订货单详情
    @else
        <a href="{{ url('business/order/return-orders') }}">退货单</a> &rarr;
        退货单详情
    @endif
@stop

@section('right')

    <div class="row">
        <div class="col-xs-10 ">
            <div class="business-order-wrap">
                <div class="item clearfix"><span class="prompt">单号 : {{ $order->id }}</span><span
                            class="pull-right">{{ $order->created_at->toDateString() }}</span></div>
                <div class="item clearfix">
                    <span class="prompt">客户名称 : {{ $order->customer_name }}</span>
                    <span class="pull-right">业务员 : {{ $order->salesman_name }}</span>
                </div>
                <div class="item"><span class="prompt">联系人 : {{ $order->customer_contact }}</span></div>
                <div class="item"><span class="prompt">收货地址 : {{ $order->shipping_address }}</span></div>
                <hr>
                <div>{{ $order->type == cons('salesman.order.type.order') ? '订货' : '退货' }}商品</div>
                <table class="table text-center business-table">
                    <tr>
                        <td>平台商品ID</td>
                        <td>商品名称</td>
                        @if($order->type != cons('salesman.order.type.return_order'))
                            <td>商品单价</td>
                            <td>订货数量</td>
                        @else
                            <td>退货数量</td>
                        @endif
                        <td>金额</td>
                        <td>操作</td>
                    </tr>
                    @foreach($orderGoods as $goods)
                        <tr>
                            <td>{{ $goods->goods_id }}</td>
                            <td>{{ $goods->goods_name }}</td>
                            @if($order->type != cons('salesman.order.type.return_order'))
                                <td>{{ $goods->price }}/{{ cons()->valueLang('goods.pieces', $goods->pieces) }}</td>
                            @endif
                            <td>{{ $goods->num }}</td>
                            <td>{{ $goods->amount }}</td>
                            <td>
                                <a href="javascript:" data-toggle="modal" data-target="#salesmanOrder"
                                   data-id="{{ $goods->id }}"
                                   data-price="{{ $goods->price }}"
                                   data-num="{{  $goods->num }}"
                                   data-pieces="{{ $goods->pieces }}"
                                   data-type="{{ $goods->type }}"
                                   data-amount="{{ $goods->amount }}"
                                >
                                    编辑
                                </a>
                            </td>
                        </tr>
                    @endforeach

                </table>
                <div class="item text-right"><span class="prompt">总计 : {{ $orderGoods->sum('amount') }}</span></div>

                @if($order->type == cons('salesman.order.type.order'))
                    <div class="item"><span class="prompt">陈列费 : </span></div>
                    <div class="item">
                        <span class="prompt">现金 : </span>
                        <b class="money display-fee">{{ $order->display_fee }}</b>
                        <input class="edit-money display-fee-edit" type="text" value="{{ $order->display_fee }}">
                        <button class="edit-cash btn btn-primary" type="button" data-type="edit">编辑</button>
                    </div>

                    @if(!$mortgageGoods->isEmpty())
                        <div class="item"><span class="prompt">抵费商品 : </span></div>
                        <table class="table text-center business-table">
                            <tr>
                                <td>商品名称</td>
                                <td>商品单位</td>
                                <td>数量</td>
                                <td>操作</td>
                            </tr>
                            @foreach($mortgageGoods as $goods)
                                <tr>
                                    <td>{{ $goods['name'] }}</td>
                                    <td>{{ cons()->valueLang('goods.pieces', $goods['pieces']) }}</td>
                                    <td>{{ $goods['num'] }}</td>
                                    <td>
                                        <a href="javascript:" data-toggle="modal" data-target="#salesmanOrder"
                                           data-id="{{ $goods['id'] }}"
                                           data-num="{{  $goods['num'] }}"
                                           data-order-id="{{ $order->id }}"
                                           data-type="-1">
                                            编辑
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                    <div class="text-right">
                        @if($order->status == cons('salesman.order.status.not_pass'))
                            <button
                                    data-url="{{ url('api/v1/business/order/' . $order->id) }}"
                                    data-method="put" data-data='{"status" : "1"}'
                                    class="btn btn-success ajax">通过
                            </button>
                        @else
                            <a class="btn btn-primary"
                               href="{{ url('business/order/export?order_id[]=' . $order->id) }}">导出</a>
                            @if($order->can_sync)
                                <button class="btn btn-warning ajax"
                                        data-url="{{ url('api/v1/business/order/' . $order->id . '/sync') }}"
                                        data-method="post">
                                    同步
                                </button>
                            @endif
                        @endif
                    </div>
                @else
                    @if($order->status == cons('salesman.order.status.not_pass'))
                        <div class="text-right">
                            <button
                                    data-url="{{ url('api/v1/business/order/' . $order->id) }}"
                                    data-method="put" data-data='{"status" : "1"}'
                                    class="btn btn-success ajax">通过
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('.parent', '.child');

            var editCash = $(".edit-cash");

            editCash.button({
                loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中',
                saveText: '保存',
                editText: '编辑'
            });
            editCash.click(function () {
                var self = $(this);
                if (self.data('type') == "edit") {
                    self.parents(".item").addClass("money-item");
                    self.button("save").data('type', 'save');
                } else {
                    var displayFeeControl = self.siblings('.display-fee'),
                            displayFee = parseFloat(displayFeeControl.html()),
                            displayFeeEditControl = self.siblings('.display-fee-edit'),
                            displayFeeEdit = parseFloat(displayFeeEditControl.val());
                    if (displayFee != displayFeeEdit) {
                        if (displayFeeEdit < 0) {
                            alert('请正确填写陈列费');
                            return false;
                        }

                        self.button('loading');
                        $.ajax({
                            url: site.api('business/order/{{ $order->id }}'),
                            method: 'put',
                            data: {display_fee: displayFeeEdit}
                        }).done(function (data, textStatus, jqXHR) {
                            displayFeeControl.html(displayFeeEdit);
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                            if (errorThrown == 'Unauthorized') {
                                site.redirect('auth/login');
                            } else {
                                tips(self, apiv1FirstError(jqXHR['responseJSON'], '操作失败'));
                            }
                        });

                    }

                    self.parents(".item").removeClass("money-item");
                    self.button('edit').data('type', 'edit');
                }
            })

        })
    </script>
@stop
