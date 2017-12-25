@extends('index.manage-master')
@section('subtitle', '订单管理')
@include('includes.order-sell-replace-modal')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--页面中间内容开始-->
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('order-sell') }}">订单管理</a> >
                    <span class="second-level">代下单</span>
                </div>
            </div>
            <div class="row order-detail">
                <div class="col-sm-12 go-history">
                    <a class="btn  btn-blue-lighter" data-target="#customersModal" data-toggle="modal"> 选择客户</a>
                    <a class="btn  btn-blue-lighter" data-type="goods" data-target="#goodsModal" data-toggle="modal">
                        添加商品</a>
                    <a class="btn  btn-blue-lighter" data-type="display" data-target="#goodsModal" data-toggle="modal">
                        添加陈列</a>
                    <a class="btn  btn-blue-lighter" data-type="gifts" data-target="#goodsModal" data-toggle="modal">
                        添加赠品</a>
                </div>
                <form class="form-horizontal ajax-form" method="post"
                      action="{{ url('api/v1/order/replace'/*.$goods->id*/) }}"
                      data-done-url="{{ url('order-sell') }}"
                      data-help-class="col-sm-push-1 col-sm-10" data-done-then="referer"
                      autocomplete="off">
                    <div class="col-sm-12">
                        <div class="row instead-order">
                            <div class="col-sm-12">
                                <div class="customer panel hidden panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">收货人信息</h3>
                                    </div>
                                    <div class="panel-container table-responsive">
                                        <table class="table table-bordered table-th-color table-center">
                                            <thead>
                                            <tr>
                                                <th>客户名称</th>
                                                <th>联系人</th>
                                                <th>联系电话</th>
                                                <th>收货地址</th>
                                            </tr>
                                            </thead>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="client_id">
                                                    <input type="hidden" name="display_type">
                                                    <p id="customer_name"></p>
                                                </td>
                                                <td id="customer_contact"></td>
                                                <td id="customer_contact_info"></td>
                                                <td>
                                                    <p id="customer_shipping_address"></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 warehousing-table">
                                <div class="goods panel hidden panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">商品</h3>
                                    </div>
                                    <div class="panel-container table-responsive ">
                                        <table class="table goods-list-container table-bordered table-th-color table-border-none table-center">
                                            <thead>
                                            <tr>
                                                <th>商品编号</th>
                                                <th>商品</th>
                                                <th>单位</th>
                                                <th>价格(元)</th>
                                                <th>数量</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 warehousing-table">
                                <div class="panel display hidden panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">陈列</h3>
                                    </div>
                                    <div class="panel-container table-responsive">
                                        <div class="display-goods-parents-container">
                                            <span class="display-goods-date"></span>
                                            <table class="table display-goods-container table-bordered table-th-color table-center">
                                                <thead>
                                                <tr>
                                                    <th>月份</th>
                                                    <th>商品编号</th>
                                                    <th>商品</th>
                                                    <th>单位</th>
                                                    <th>数量</th>
                                                    <th>操作</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <table class="table display-fee-container table-bordered table-th-color table-center">
                                            <thead>
                                            <tr>
                                                <th>时间</th>
                                                <th>现金</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 warehousing-table">
                                <div class="gifts panel hidden panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">赠品</h3>
                                    </div>
                                    <div class="panel-container table-responsive ">
                                        <table class="table gifts-list-container table-bordered table-th-color table-border-none table-center">
                                            <thead>
                                            <tr>
                                                <th>商品编号</th>
                                                <th>商品</th>
                                                <th>单位</th>
                                                <th>数量</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>

                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">订单备注</h3>
                                    </div>
                                    <div class="panel-container ">
                                        <input name="order_remark" type="text" class="order-notes"
                                               placeholder="请填写订单备注"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 statistics text-right">
                                <p>商品总额: <b class="red goods-total">￥0.00</b></p>
                                <p><input type="hidden" name="amount"></p>
                                {{--<p>陈列费: <b class="red display-fee">-￥0.00</b></p>--}}
                                <p>订单应付金额: <b class="red total_amount">￥0.00</b></p>
                                <p>
                                    <button id="form-submit" type="submit" class="btn btn-primary">提交订单</button>
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            tdTips();
            $("body").on("click", ".modal table tr", function () {
                $(this).children("td").children("input[type='radio']").prop("checked", true);
            });
            $('.display-goods-container .display-fee-container').hide();

        })
    </script>
@stop
