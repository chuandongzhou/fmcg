@extends('index.menu-master')
@section('right')
    <div class="row order-detail">
        <div class="col-sm-12 go-history">
            <a class="go-back" href="{{ url('order-sell') }}"><i class="fa fa-reply"></i> 返回</a>
        </div>
        <div class="col-sm-12">
            <div class="row order-tracking">

                <div class="col-sm-12">
                    <p><label>订单跟踪 :</label></p>

                    <div id="stepBar" class="ui-stepBar-wrap">
                        <div class="ui-stepBar">
                            <div class="ui-stepProcess"></div>
                        </div>
                        <div class="ui-stepInfo-wrap">
                            <div class="ui-stepLayout" border="0" cellpadding="0" cellspacing="0">
                                <ul>
                                    <li class="ui-stepInfo">
                                        <a class="ui-stepSequence"></a>

                                        <p class="ui-stepName">未发货</p>
                                    </li>
                                    <li class="ui-stepInfo">
                                        <a class="ui-stepSequence"></a>

                                        <p class="ui-stepName">已发货</p>
                                    </li>
                                    <li class="ui-stepInfo">
                                        <a class="ui-stepSequence"></a>

                                        <p class="ui-stepName">已付款</p>
                                    </li>
                                    <li class="ui-stepInfo">
                                        <a class="ui-stepSequence"></a>

                                        <p class="ui-stepName">已完成</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 orders-submit-detail">
                    <ul class="submit-detail-item">
                        <li>订单操作</li>
                        <li>操作时间</li>
                        <li>操作人</li>
                    </ul>
                    <ul class="submit-detail-item">
                        <li>提交订单</li>
                        <li class="time">
                            <span class="date">{{ $order['created_at'] }}</span>
                        </li>
                        <li>{{ $order['user']['user_name'] }}</li>
                    </ul>
                    @if((int)$order['send_at'])
                        <ul class="submit-detail-item">
                            <li>发货</li>
                            <li class="time">
                                <span class="date">{{ $order['send_at'] }}</span>
                            </li>
                            <li>{{ $order['shop']['contact_person'] }}</li>
                        </ul>
                    @endif
                    @if((int)$order['paid_at'])
                        <ul class="submit-detail-item">
                            <li>付款</li>
                            <li class="time">
                                <span class="date">{{ $order['paid_at'] }}</span>
                            </li>
                            <li>{{ $order['user']['user_name'] }}</li>
                        </ul>
                    @endif
                    @if((int)$order['finished_at'])
                        <ul class="submit-detail-item">
                            <li>完成</li>
                            <li class="time">
                                <span class="date">{{ $order['finished_at'] }}</span>
                            </li>
                            <li>{{ $order['user']['user_name'] }}</li>
                        </ul>
                    @endif
                    @if($order['is_cancel'])
                        <ul class="submit-detail-item">
                            <li>取消订单</li>
                            <li class="time">
                                <span class="date">{{ $order['cancel_at'] }}</span>
                            </li>
                            <li>{{ $order['cancel_by'] == $order['user']['user_name'] ? $order['user']['user_name'] : $order['shop']['contact_person'] }}</li>
                        </ul>
                    @endif
                </div>
            </div>
            <div class="row order-receipt">
                <div class="col-sm-12">
                    <ul class="pull-left order-information">
                        <li class="title">订单信息</li>
                        <li><span class="title-info-name">订单号 : </span>{{ $order['id'] }}</li>
                        <li><span class="title-info-name">订单金额 : </span><span class="red">￥{{ $order['price'] }}</span>
                        </li>
                        <li><span class="title-info-name">支付方式 : </span>{{ $order['payment_type'] }}</li>
                        <li><span class="title-info-name">订单状态 : </span><span
                                    class="red">{{ $order['status_name'] }}</span></li>
                        <li><span class="title-info-name">订单备注 :</span>

                            <p class="remarks-content">{{ $order['remark'] }}</p>
                        </li>
                    </ul>
                    <div class="pull-right">
                        {{--卖家显示按钮--}}
                        <div class="pull-right">
                            @if(!$order['is_cancel'])
                                @if($order['can_cancel'])
                                    <button class="btn btn-cancel ajax" data-url="{{ url('order-sell/cancel-sure') }}"
                                            data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>取消
                                    </button>
                                @endif
                                @if($order['can_send'])
                                    <a class="btn btn-warning send-goods" data-target="#sendModal"
                                       data-toggle="modal" data-data="{{ $order['id'] }}">发货</a>
                                @elseif($order['can_confirm_collections'])
                                    <button class="btn btn-primary ajax" data-method='put'
                                            data-url="{{ url('api/v1/order/batch-finish-of-sell') }}"
                                            data-data='{"order_id":{{ $order['id'] }}}'>确认收款
                                    </button>
                                @endif
                                @if($order['can_export'])
                                    <a target="_blank" class="btn btn-success"
                                       href="{{ url('order-sell/export?order_id='.$order['id']) }}">导出</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 receiving-information">
                    <ul>
                        <li class="title">收货人信息</li>
                        <li><span class="title-info-name">终端商名称 : </span>{{ $order['user']['user_name'] }} </li>
                        <li><span class="title-info-name">联系人 : </span>{{ $order['shipping_address']['consigner'] }}
                        </li>
                        <li><span class="title-info-name">联系电话 : </span>{{ $order['shipping_address']['phone'] }}</li>
                        <li>
                            <span class="title-info-name">联系地址: </span>
                            {{ $order['shipping_address']['address']['area_name'] . $order['shipping_address']['address']['address'] }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row table-row">
                <div class="col-sm-12 table-responsive table-col">
                    <table class="table table-bordered table-center">
                        <thead>
                        <tr>
                            <th>商品编号</th>
                            <th>商品图片</th>
                            <th>商品名称</th>
                            <th>商品价格</th>
                            <th>商品数量</th>
                            <th>金额</th>
                            @if($order['status']<cons('order.status.send') && $order['is_cancel'] == cons('order.is_cancel.off'))
                                <th>操作</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order['goods'] as $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td><img class="store-img" src={{ $item['image_url'] }} /></td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['pivot']['price'] }}</td>
                                <td>{{ $item['pivot']['num'] }}</td>
                                <td>{{ $item['pivot']['total_price'] }}</td>
                                @if($order['status']<cons('order.status.send') && $order['is_cancel'] == cons('order.is_cancel.off'))
                                    <td><a class="change-price" data-target="#changePrice"
                                           data-toggle="modal" data-data="{{ $order['id'] }}"
                                           data-pivot="{{  $item['pivot']['id'] }}">修改</a></td>
                                @endif

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    总额 : <b class="red">￥{{ $order['price'] }}</b>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="sendModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    @if($delivery_man->count())
                        <p class="modal-title" id="cropperModalLabel">选择配送人员:
                            <span class="extra-text">
                                  <select name="delivery_man_id">
                                      @foreach($delivery_man as  $item)
                                          <option value="{{ $item->id }}">{{ $item->name }}</option>
                                      @endforeach
                                  </select>


                            </span>
                        </p>
                    @else
                        没有配送人员信息,请设置。<a href="{{ url('personal/delivery-man') }}">去设置</a>
                    @endif
                </div>
                <div class="modal-body">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        @if($delivery_man->count())
                            <button type="button" class="btn btn-primary btn-sm btn-add ajax" data-text="确定"
                                    data-url="{{ url('api/v1/order/batch-send') }}" data-method="put">确定
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="changePrice" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <p class="modal-title" id="cropperModalLabel">修改单价:
                            <span class="extra-text">
                                  <input type="text" name="price"/>
                                <span class="tip" style="display: none;color:red;">请输入数字</span>
                            </span>
                    </p>
                </div>
                <div class="modal-body">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary btn-sm btn-add ajax" data-text="确定"
                                data-url="{{ url('api/v1/order/change-price') }}" data-method="put">确定
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@include('includes.stepBar')
@section('js')
    @parent
    <script>
        $(function () {
            sendGoodsByDetailPage();
            changePriceByDetailPage();
        })
    </script>
@stop