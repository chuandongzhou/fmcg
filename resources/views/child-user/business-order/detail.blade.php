@extends('child-user.manage-master')
@include('includes.salesman-order-change', ['giftUrl' => url('V1'), 'url' => url('V1')])
@include('includes.shipping-address-map')
@section('subtitle')
    业务管理-{{ $order->type == cons('salesman.order.type.order') ? '订货单' : '退货单' }}
@stop

@section('container')
    @include('includes.child-menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('child-user/salesman') }}">业务管理</a> >
                    <a href="{{ url('child-user/business-order/order-forms') }}">订货单</a> >
                    <span class="second-level">订货单详情</span>
                </div>
            </div>

            <div class="row order-detail business-detail">
                <div class="col-sm-12 go-history">
                    <a class="btn go-back btn-border-blue" href="javascript:history.go(-1)">返回</a>
                    @if($order->status == cons('salesman.order.status.not_pass'))
                        <button
                                data-url="{{ url('api/v1/child-user/business-order/' . $order->id) }}"
                                data-method="put" data-data='{"status" : "1"}'
                                data-done-url="{{ $order->type == cons('salesman.order.type.order')?url('child-user/business-order/order-forms'):url('child-user/business-order/return-orders') }}"
                                class="btn btn-blue-lighter ajax">通过
                        </button>
                    @else
                        <a class="btn btn-blue-lighter"
                           href="{{ url('child-user/business-order/export?order_id[]=' . $order->id) }}">导出</a>
                        @if($order->can_sync)
                            <button class="btn btn-blue-lighter ajax"
                                    data-url="{{ url('api/v1/child-user/business-order/' . $order->id . '/sync') }}"
                                    data-method="post">
                                同步
                            </button>
                        @endif
                    @endif
                </div>
                <div class="col-sm-12">
                    <div class="row order-receipt">
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">订货信息</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center public-table">
                                        <thead>
                                        <tr>

                                            <td>订货单号</td>
                                            <td>订货时间</td>
                                            <td>业务员</td>
                                            <td>客户名称</td>
                                            <td>
                                                订货单备注
                                                <a class="edit order-note" onclick="editText('order-note')"
                                                   data-type="edit"
                                                   data-url="child-user/business-order/{{ $order->id }}"><i
                                                            class="iconfont icon-xiugai"></i> 编辑</a>
                                            </td>
                                            @if(!$mortgageGoods->isEmpty() || !$displayFee->isEmpty())
                                                <td>
                                                    陈列费备注
                                                    <a class="edit display-fee-notes"
                                                       onclick="editText('display-fee-notes')"
                                                       data-type="edit"
                                                       data-url="child-user/business-order/{{ $order->id }}"><i
                                                                class="iconfont icon-xiugai"></i> 编辑</a>
                                                </td>
                                            @endif

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->created_at }}</td>
                                            <td>{{ $order->salesman_name }}</td>
                                            <td>{{ $order->customer_name }}</td>
                                            <td width="20%">
                                                <div id="order-note">{{ $order->order_remark }}</div>
                                                <div class="enter-num-panel ">
                                        <textarea class="edit-text" autofocus
                                                  maxlength="50"
                                                  data-name="order_remark">{{ $order->order_remark }}</textarea>
                                                </div>
                                            </td>
                                            @if(!$mortgageGoods->isEmpty() || !$displayFee->isEmpty())
                                                <td width="20%">
                                                    <div id="display-fee-notes">{{ $order->display_remark }}</div>
                                                    <div class="enter-num-panel ">
                                                <textarea class="edit-text" autofocus
                                                          maxlength="50"
                                                          data-name="display_remark">{{ $order->display_remark }}</textarea>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">收货人信息</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center table-th-color">
                                        <thead>
                                        <tr>
                                            <th>联系人</th>
                                            <th>联系电话</th>
                                            <th>收货地址</th>
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
                                        {{ $order->type == cons('salesman.order.type.order') ? '订货商品' : '退货商品' }}
                                    </h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center table-th-color">

                                        <thead>
                                        <th>商品编号</th>
                                        <th>商品图片</th>
                                        <th>商品名称</th>
                                        <td>商品单价</td>
                                        <td>订货数量</td>
                                        <th>金额</th>
                                        @if($order->can_pass)
                                            <th>操作</th>
                                        @endif
                                        </thead>
                                        @foreach($orderGoods as $goods)
                                            <tr>
                                                <td>{{ $goods->goods_id }}</td>
                                                <td><img class="store-img"
                                                         src="{{ $goods->goods->image_url }}">
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

                                                <td>{{ $goods->price }}
                                                    /{{ cons()->valueLang('goods.pieces', $goods->pieces) }}</td>
                                                <td>{{ $goods->num }}</td>
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
                                                            <i class="iconfont icon-xiugai"></i>编辑
                                                        </a>
                                                        <a class="red delete-no-form" data-method="delete"
                                                           data-url="{{ url('api/v1/child-user/business-order/goods-delete/' . $goods->id) }}">
                                                            <i class="iconfont icon-shanchu"></i>删除
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        <tr>

                                            <td colspan="7">
                                                <div class="text-right">
                                                    订货总数 : {{ $goods_total_num }}

                                                    总金额 : {{ number_format($goods_total_amount, 2, '.', '')}}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @if(!$mortgageGoods->isEmpty())
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">抵费商品</h3>
                                    </div>
                                    <div class="panel-container table-responsive">
                                        <table class="table table-bordered table-center table-th-color">
                                            <thead>
                                            <th>商品编号</th>
                                            <th>陈列费（月份）</th>
                                            <th>商品图片</th>
                                            <th>商品名称</th>
                                            <th>商品数量</th>
                                            <th>操作</th>
                                            </thead>
                                            @foreach($mortgageGoods as $key => $goods)
                                                <tr>
                                                    <td>{{ $goods['id'] }}</td>
                                                    <td>{{ $goods['month'] }}</td>
                                                    <td><img class="store-img"
                                                             src="{{ $goods['image_url'] }}">
                                                    </td>
                                                    <td width="30%">
                                                        <div class="product-panel">
                                                            <a class="product-name" href="">{{ $goods['name'] }}</a>
                                                            @if(!empty($goods['promotion_info']))
                                                                <p class="promotions">(<span
                                                                            class="ellipsis"> {{ $goods['promotion_info'] }}</span>)
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td width="20%">
                                                        <div class="commodity-num"
                                                             id="commodity-num{{ $key.$goods['id'] }}">{{  $goods['num'] }}</div>

                                                        <div class="enter-num-panel pull-left">
                                                            <input data-id="{{ $goods['id'] }}" data-name="num"
                                                                   data-month="{{ $goods['month'] }}"
                                                                   class="edit-text" autofocus
                                                                   value="{{  $goods['num'] }}"/>
                                                            {{--<div class="prompt">--}}
                                                            {{--剩余可设置陈列数量:{{ !is_null($goods['surplus']) ? (int)$goods['surplus']['surplus'] : $goods['total'] }}--}}
                                                            {{--{{ cons()->valueLang('goods.pieces', $goods['pieces']) }}--}}
                                                            {{--</div>--}}
                                                        </div>
                                                        <span class="pull-right goods-pieces">{{ cons()->valueLang('goods.pieces', $goods['pieces']) }}</span>
                                                    </td>
                                                    <td>
                                                        <a class="edit commodity-num commodity-num{{ $key.$goods['id'] }}"
                                                           data-url="child-user/business-order/change"
                                                           onclick="editText('commodity-num{{ $key.$goods['id'] }}')">
                                                            <i class="iconfont icon-xiugai "></i>编辑</a>
                                                        <a class="red delete-no-form" data-method="delete"
                                                           data-url="{{ url('api/v1/child-user/business-order/mortgage-goods-delete') }}"
                                                           data-data='{"order_id":{{ $order->id }}, "mortgage_goods_id" : {{ $goods['id'] }}}'><i
                                                                    class="iconfont icon-shanchu"></i>删除</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if(!$displayFee->isEmpty())
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">陈列费</h3>
                                    </div>
                                    <div class="panel-container table-responsive">
                                        <table class="table table-bordered table-center table-th-color">
                                            <thead>
                                            <th>陈列费（月份）</th>
                                            <th>现金</th>
                                            <th>操作</th>
                                            </thead>
                                            @foreach($displayFee as $key => $item)
                                                <tr>
                                                    <td>{{ $item->month }}</td>
                                                    <td width="20%">
                                                        <b class="red old-value"
                                                           id="money{{ $key.$item->id }}">{{ $item->used }}</b>

                                                        <div class="enter-num-panel ">
                                                            <input data-name="display_fee" data-id="{{ $item->id }}"
                                                                   class="edit-text" autofocus
                                                                   value="{{ $item->used }}"/>
                                                            {{--<div class="prompt">剩余可设置陈列数量:{{ $item->surplus }}</div>--}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a class="edit money money{{ $key.$item->id }}"
                                                           data-url="child-user/business-order/update-order-display-fee"
                                                           data-id="{{ $item->id }}" data-parse="true" data-type="edit"
                                                           onclick="editText('money{{ $key.$item->id }}')"><i
                                                                    class="iconfont icon-xiugai "></i>编辑</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if (!$order->gifts->isEmpty())
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">赠品</h3>
                                    </div>
                                    <div class="panel-container table-responsive">
                                        <table class="table table-bordered table-center table-th-color">
                                            <thead>
                                            <th width="10%">商品编号</th>
                                            <th>商品名</th>
                                            <th>数量</th>
                                            <th>编辑</th>
                                            </thead>
                                            @foreach($order->gifts as $gift)
                                                <tr>
                                                    <td>{{ $gift->id }}</td>
                                                    <td>{{ $gift->name }}</td>
                                                    <td>
                                                        {{ $gift->pivot->num . cons()->valueLang('goods.pieces', $gift->pivot->pieces) }}
                                                    </td>
                                                    <td>
                                                        <a href="javascript:" class="btn" data-id="{{ $gift->id }}"
                                                           data-order-id="{{ $order->id }}"
                                                           data-num="{{ $gift->pivot->num }}"
                                                           data-pieces="{{ $gift->pivot->pieces }}"
                                                           data-toggle="modal"
                                                           data-target="#giftModal">
                                                            <i class="iconfont icon-xiugai"></i>编辑
                                                        </a>
                                                        <a class="red delete-no-form" data-method="delete"
                                                           data-url="{{ url('api/v1/child-user/business-order/gift/' . $gift->id) }}"
                                                           data-data='{"order_id":{{ $order->id }}}'><i
                                                                    class="iconfont icon-shanchu"></i>删除</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">

        function editText(id) {
            var content = $("#" + id), self = $("." + id);
            if (content.is(":visible")) {
                $("." + id).html("<i class='iconfont icon-baocun'></i> 保存");
                content.hide().siblings(".enter-num-panel").show();

                if (self.hasClass('money') || self.hasClass('commodity-num')) {
                    content.next('.enter-num-panel').append('<div class="prompt"><div>剩余陈列:<span class="surplus"><i class="fa fa-spinner fa-pulse"></i></span></div><div>未审核陈列:<span class="non-confirm"><i class="fa fa-spinner fa-pulse"></i></span></div></div>');
                    var url = self.hasClass('commodity-num') ? site.api('child-user/business-order/mortgage-goods-surplus') : site.api('child-user/business-order/display-fee-surplus'),
                        data = self.hasClass('commodity-num') ? {
                            id: content.siblings('.enter-num-panel').children('.edit-text').data('id'),
                            month: content.next('.enter-num-panel').find('input').data('month'),
                            order_id: '{{ $order->id }}'
                        } : {
                            customer_id: '{{ $order->salesmanCustomer->id }}',
                            month: self.parent().prev().prev().html(),
                            order_id: '{{ $order->id }}'
                        };

                    $.ajax({
                        url: url,
                        method: 'get',
                        data: data
                    }).done(function (data) {
                        var surplus = self.hasClass('commodity-num') ? parseInt(data.surplus) + content.next().next('.goods-pieces').html() : '￥' + data.surplus,
                            nonConfirm = self.hasClass('commodity-num') ? parseInt(data.nonConfirm) + content.next().next('.goods-pieces').html() : '￥' + data.nonConfirm;
                        var messageContainer = content.next('.enter-num-panel').find('.prompt');
                        messageContainer.find('.surplus').html(surplus);
                        messageContainer.find('.non-confirm').html(nonConfirm);
                    }).fail(function () {
                        content.next('.enter-num-panel').find('.prompt').html('查询失败');

                    });
                }

            } else {

                var url = self.data('url');
                var oldValueControl = content,
                    isParse = self.data('parse'),
                    oldValue = isParse ? parseFloat(oldValueControl.html()) : oldValueControl.html(),
                    newValueControl = content.siblings('.enter-num-panel').children('.edit-text'),
                    newValue = isParse ? parseFloat(newValueControl.val()) : newValueControl.val(),
                    name = newValueControl.data('name'),
                    id = newValueControl.data('id'),
                    data = {};
                if (oldValue != newValue) {
                    if (isParse && newValue < 0) {
                        alert('请正确填写陈列费');
                        return false;
                    }
                    var load = '<div class="loading"> <img src="' + site.url("images/new-loading.gif") + '" /> </div>';
                    $('body').append(load);
                    data[name] = newValue;
                    if (id > 0) {
                        data['id'] = id;
                        data['order_id'] = '{{  $order->id }}';

                    }
                    if (self.hasClass('commodity-num')) {
                        data['month'] = newValueControl.data('month');
                        data['customer_id'] = '{{ $order->salesmanCustomer->id }}';
                    }
                    self.html('<i class="fa fa-spinner fa-pulse"></i> 操作中');
                    $.ajax({
                        url: site.api(url),
                        method: 'put',
                        data: data
                    }).done(function (data, textStatus, jqXHR) {

                        self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                        content.html(newValue);
                        content.next('.enter-num-panel').find('input').val(newValue);
                        content.show().siblings(".enter-num-panel").hide();
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        if (errorThrown == 'Unauthorized') {
                            site.redirect('auth/login');
                        } else {
                            tips(self, apiv1FirstError(jqXHR['responseJSON'], '操作失败'));
                            content.html(oldValue);
                            content.next('.enter-num-panel').find('input').val(oldValue);
                            content.show().siblings(".enter-num-panel").hide();
                            self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                        }
                    }).always(function () {
                        self.parent().children('.prompt').remove();
                        $('body').find('.loading').remove();
                    });

                } else {
                    self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                    content.show().siblings(".enter-num-panel").hide();
                    self.parent().children('.prompt').remove();
                    $('body').find('.loading').remove();
                }


            }
        }
        $(function () {

            deleteNoForm();
        })


    </script>
@stop
