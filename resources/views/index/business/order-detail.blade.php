@extends('index.menu-master')
@include('includes.salesman-order-change')
@include('includes.shipping-address-map')
@section('subtitle')
    业务管理-{{ $order->type == cons('salesman.order.type.order') ? '订货单' : '退货单' }}
@stop

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> >

    @if($order->type == cons('salesman.order.type.order'))
        <a href="{{ url('business/order/order-forms') }}">订货单</a> >
        <span class="second-level">订货单详情</span>
    @else
        <a href="{{ url('business/order/return-orders') }}">退货单</a> >
        <span class="second-level">退货单详情</span>
    @endif
@stop

@section('right')

    <div class="row order-detail business-detail">
        <div class="col-sm-12 go-history">
            @if($order->type == cons('salesman.order.type.order'))
                <a class="btn go-back btn-border-blue" href="javascript:history.go(-1)">返回</a>
                @if($order->status == cons('salesman.order.status.not_pass'))
                    <button
                            data-url="{{ url('api/v1/business/order/' . $order->id) }}"
                            data-method="put" data-data='{"status" : "1"}'
                            class="btn btn-blue-lighter ajax">通过
                    </button>
                @else
                    <a class="btn btn-blue-lighter"
                       href="{{ url('business/order/export?order_id[]=' . $order->id) }}">导出</a>
                    @if($order->can_sync)
                        <button class="btn btn-blue-lighter ajax"
                                data-url="{{ url('api/v1/business/order/' . $order->id . '/sync') }}"
                                data-method="post">
                            同步
                        </button>
                    @endif
                @endif
            @else
                @if($order->status == cons('salesman.order.status.not_pass'))

                    <a class="btn go-back btn-border-blue" href="javascript:history.go(-1)">返回</a>
                    <button
                            data-url="{{ url('api/v1/business/order/' . $order->id) }}"
                            data-method="put" data-data='{"status" : "1"}'
                            class="btn btn-blue-lighter ajax">通过
                    </button>

                @endif
            @endif
        </div>
        <div class="col-sm-12">
            <div class="row order-receipt">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{ $order->type == cons('salesman.order.type.order') ? '订货信息' : '退货信息' }}</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center public-table">
                                <thead>
                                <tr>
                                    <td>订货单号</td>
                                    <td>订货时间</td>
                                    <td>业务员</td>
                                    <td>客户名称</td>
                                    @if($order->type == cons('salesman.order.type.order'))
                                        <td>
                                            订货单备注
                                            <a class="edit order-note" onclick="editText('order-note')" data-type="edit"
                                               data-url="business/order/{{ $order->id }}"><i
                                                        class="iconfont icon-xiugai"></i>编辑</a>
                                        </td>
                                        <td>
                                            陈列费备注
                                            <a class="edit display-fee-notes" onclick="editText('display-fee-notes')"
                                               data-type="edit"
                                               data-url="business/order/{{ $order->id }}"><i
                                                        class="iconfont icon-xiugai"></i>编辑</a>
                                        </td>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at->toDateString() }}</td>
                                    <td>{{ $order->salesman_name }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    @if($order->type == cons('salesman.order.type.order'))
                                        <td width="20%">
                                            <div id="order-note">{{ $order->order_remark }}</div>
                                            <div class="enter-num-panel ">
                                        <textarea class="edit-text" autofocus
                                                  maxlength="50"
                                                  data-name="order_remark">{{ $order->order_remark }}</textarea>
                                            </div>
                                        </td>
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
                            <table class="table table-bordered table-center public-table">
                                <thead>
                                    <th>联系人</th>
                                    <th>联系电话</th>
                                    <th>收货地址</th>
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
                            <h3 class="panel-title">{{ $order->type == cons('salesman.order.type.order') ? '订货' : '退货' }}
                                商品</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center public-table">

                                <thead>
                                    <th>商品编号</th>
                                    <th>商品图片</th>
                                    <th>商品名称</th>
                                    @if($order->type != cons('salesman.order.type.return_order'))
                                        <td>商品单价</td>
                                        <td>订货数量</td>
                                    @else
                                        <td>退货数量</td>
                                    @endif
                                    <th>金额</th>
                                    <th>操作</th>
                                </thead>
                                @foreach($orderGoods as $goods)
                                    <tr>
                                        <td>{{ $goods->goods_id }}</td>
                                        <td><img class="store-img"
                                                 src="{{ $goods->goods->image_url }}">
                                        </td>
                                        <td width="30%">
                                            <div class="product-panel">
                                                <a class="product-name" href="">{{ $goods->goods_name }}</a>
                                                @if(!empty($goods->promotion_info))
                                                    <p class="promotions">(<span
                                                                class="ellipsis">{{ $goods->promotion_info }}</span>)
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                        @if($order->type != cons('salesman.order.type.return_order'))
                                            <td>{{ $goods->price }}
                                                /{{ cons()->valueLang('goods.pieces', $goods->pieces) }}</td>
                                        @endif
                                        <td>{{ $goods->num }}</td>
                                        <td>{{ $goods->amount }}</td>
                                        <td>
                                            <a class="edit" data-toggle="modal" data-target="#salesmanOrder"
                                               data-id="{{ $goods->id }}"
                                               data-price="{{ $goods->price }}"
                                               data-num="{{  $goods->num }}"
                                               data-pieces="{{ $goods->pieces }}"
                                               data-type="{{ $goods->type }}"
                                               data-amount="{{ $goods->amount }}"><i
                                                        class="iconfont icon-xiugai"></i>编辑</a>
                                            <a class="red delete-no-form" data-method="delete"
                                               data-url="{{ url('api/v1/business/order/goods-delete/' . $goods->id) }}"><i
                                                        class="iconfont icon-shanchu"></i>删除</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    @if($order->type == cons('salesman.order.type.order'))

                        @if(!$mortgageGoods->isEmpty())
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">抵费商品</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center public-table">
                                        <thead>
                                            <th>商品编号</th>
                                            <th>商品图片</th>
                                            <th>商品名称</th>
                                            <th>商品数量</th>
                                            <th>操作</th>
                                        </thead>
                                        @foreach($mortgageGoods as $goods)
                                            <tr>
                                                <td>{{ $goods['id'] }}</td>
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
                                                         id="commodity-num{{ $goods['id'] }}">{{  $goods['num'] }}</div>

                                                    <div class="enter-num-panel pull-left">
                                                        <input data-id="{{ $goods['id'] }}" data-name="num"
                                                               class="edit-text" autofocus
                                                               value="{{  $goods['num'] }}"/>
                                                        <div class="prompt">
                                                            剩余可设置陈列数量:{{ !is_null($goods['surplus'])?(int)$goods['surplus']['surplus']:$goods['total'] }}
                                                            {{ cons()->valueLang('goods.pieces', $goods['pieces']) }}
                                                        </div>
                                                    </div>
                                                    <span class="pull-right">{{ cons()->valueLang('goods.pieces', $goods['pieces']) }}</span>


                                                </td>
                                                <td>
                                                    <a class="edit commodity-num{{ $goods['id'] }}"
                                                       data-url="business/order/change"
                                                       onclick="editText('commodity-num{{ $goods['id'] }}')"><i
                                                                class="iconfont icon-xiugai "></i>编辑</a>
                                                    <a class="red delete-no-form" data-method="delete"
                                                       data-url="{{ url('api/v1/business/order/mortgage-goods-delete') }}"
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
                                    <table class="table table-bordered table-center public-table">
                                        <thead>
                                            <th>月份</th>
                                            <th>现金</th>
                                            <th>操作</th>
                                        </thead>
                                        @foreach($displayFee as $item)
                                            <tr>
                                                <td>{{ $item->month }}</td>
                                                <td width="20%">
                                                    <b class="red old-value"
                                                       id="money{{ $item->id }}">{{ $item->used }}</b>

                                                    <div class="enter-num-panel ">
                                                        <input data-name="display_fee" data-id="{{ $item->id }}"
                                                               class="edit-text" autofocus
                                                               value="{{ $item->used }}"/>
                                                        <div class="prompt">剩余可设置陈列数量:{{ $item->surplus }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a class="edit money{{ $item->id }}"
                                                       data-url="business/order/update-order-display-fee"
                                                       data-id="{{ $item->id }}" data-parse="true" data-type="edit"
                                                       onclick="editText('money{{ $item->id }}')"><i
                                                                class="iconfont icon-xiugai "></i>编辑</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script type="text/javascript">

        function editText(id) {
            var content = $("#" + id);
            if (content.is(":visible")) {
                $("." + id).html("<i class='iconfont icon-baocun'></i> 保存");
                content.hide().siblings(".enter-num-panel").show();

            } else {
                var self = $("." + id), url = self.data('url');
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

                    data[name] = newValue;
                    if (id > 0) {
                        data['id'] = id;
                        data['order_id'] = '{{ $order->id }}';
                    }
                    self.html('<i class="fa fa-spinner fa-pulse"></i> 操作中');
                    $.ajax({
                        url: site.api(url),
                        method: 'put',
                        data: data
                    }).done(function (data, textStatus, jqXHR) {


                        self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                        content.html(newValue);
                        content.show().siblings(".enter-num-panel").hide();

                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        if (errorThrown == 'Unauthorized') {
                            site.redirect('auth/login');
                        } else {
                            tips(self, apiv1FirstError(jqXHR['responseJSON'], '操作失败'));
                            content.html(oldValue);
                            content.show().siblings(".enter-num-panel").hide();
                            self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                        }
                    });

                } else {
                    self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                    content.html(newValue);
                    content.show().siblings(".enter-num-panel").hide();
                }


            }
        }
        $(function () {

            deleteNoForm();
        })


    </script>
@stop
