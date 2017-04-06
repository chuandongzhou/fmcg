@extends('admin.master')
@include('includes.timepicker')
@section('right-container')
    <form class="form-horizontal" method="get" action="{{ url('admin/system-withdraw') }}" autocomplete="off">

        <div class="form-group">
            <label for="order_num" class="col-sm-2 control-label">提现单号：</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" name="withdraw_id" placeholder="请输入订单号"
                       value="{{ $data['withdraw_id'] or '' }}">
            </div>
        </div>

        <div class="form-group">
            <label for="trade_num" class="col-sm-2 control-label">交易单号：</label>

            <div class="col-sm-4">
                <input type="text" value="{{  $data['trade_no'] or  '' }}" class="form-control"
                       name="trade_no" placeholder="请输入交易号">
            </div>
        </div>

        <div class="form-group">
            <label for="account" class="col-sm-2 control-label">商家账号：</label>

            <div class="col-sm-4">
                <input type="text" value="{{ $data['user_name'] or '' }}" class="form-control"
                       name="user_name" placeholder="请输入商家账号">
            </div>
        </div>
        <div class="form-group">
            <label for="account" class="col-sm-2 control-label">申请时间：</label>

            <div class="col-sm-6 time-limit">
                <input type="text" class="inline-control datetimepicker" name="started_at"
                       value="{{ $data['started_at'] or ''  }}"> 至
                <input type="text" class="inline-control datetimepicker" name="end_at"
                       value="{{ $data['end_at'] or '' }}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <button type="submit" class="btn btn-bg btn-primary search-by-get">查询</button>
                &nbsp;&nbsp;&nbsp;&nbsp;
                {{--<a href="{{ url('admin/system-trade/export-to-excel?' . $linkUrl) }}" class="btn btn-bg btn-warning">导出</a>--}}
            </div>
        </div>
    </form>
    <table class="table table-striped table-center">
        <thead>
        <tr>
            <th>单号</th>
            <th>金额</th>
            <th>商家账号</th>
            <th>开户人</th>
            <th>银行账号</th>
            <th>银行名称</th>
            <th>开户行地址</th>
            <th>状态</th>
            <th width="5%">交易单号</th>
            <th>申请时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @if($withdraws->count())
            @foreach($withdraws as $withdraw)
                <tr>
                    <td>{{ $withdraw->id }}</td>
                    <td>{{ $withdraw->amount }}</td>
                    <td>{{ $withdraw->user->user_name }}</td>
                    <td>{{ $withdraw->card_holder }}</td>
                    <td>{{ $withdraw->card_number }}</td>
                    <td>{{ cons()->valueLang('bank.type')[$withdraw->card_type] }}</td>
                    <td>{{ $withdraw->card_address }}</td>
                    <td>{{ $withdraw->status_info }}</td>
                    <td>{{$withdraw->trade_no }}</td>
                    <td>{{$withdraw->created_at }}</td>
                    <td>
                        <div class="btn-group btn-group-xs">
                            @if($withdraw->status == cons('withdraw.review'))
                                <a class="btn btn-primary ajax" data-method="put"
                                   data-url="{{ url('admin/system-withdraw/pass') }}"
                                   data-data={!! json_encode(['withdraw_id'=>$withdraw->id]) !!}
                                >
                                    <i class="fa fa-edit"></i> 通过
                                </a>
                                <a class="rollback btn btn-danger" data-target="#rollbackModal" data-toggle="modal"
                                   data-id='{{ $withdraw->id }}'>
                                    <i class="fa fa-reply"></i> 回退
                                </a>
                            @endif
                            @if($withdraw->status == cons('withdraw.pass'))
                                {{--打款需要交易号，回退需要回退原因--}}
                                <a class="payment btn btn-success" data-target="#paymentModal" data-toggle="modal"
                                   data-id='{{ $withdraw->id }}'>
                                    <i class="fa fa-edit"></i> 已打款
                                </a>
                                <a class="btn btn-default" data-target="#payPasswordModal" data-toggle="modal"
                                   data-url="{{ url('admin/system-withdraw/send/' . $withdraw->id) }}">
                                    <i class="fa fa-money"></i> 打款
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr align="center">
                <td colspan="11">没有符合条件的信息</td>
            </tr>
        @endif
        </tbody>
    </table>

    {!! $withdraws->appends($data)->render() !!}

    <div class="modal fade in" id="rollbackModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <form class="form-horizontal ajax-form no-prompt"
                      action="{{  url('admin/system-withdraw/failed') }}"
                      data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                        <div class="modal-title forgot-modal-title" id="shippingAddressModalLabel">
                            <span class="header-content">回退原因</span>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="text-left">
                            <textarea class="form-control" name="reason"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="withdraw_id"/>
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary btn-sm btn-add ajax" data-text="确定"
                                data-method="put">确定
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <form class="form-horizontal ajax-form no-prompt"
                      action="{{ url('admin/system-withdraw/payment') }}"
                      data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                        <div class="modal-title forgot-modal-title" id="shippingAddressModalLabel">
                            <span class="header-content">打款单号</span>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="trade"><span class="red">*</span>
                                交易单号:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="trade_no" name="trade_no" placeholder="请输入交易单号"
                                       value="" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="withdraw_id"/>
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消
                        </button>
                        <button type="button" class="btn btn-primary btn-sm btn-add ajax" data-text="确定"
                                data-method="put">确定
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="payPasswordModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <form class="form-horizontal ajax-form no-prompt"
                      data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                        <div class="modal-title forgot-modal-title" id="shippingAddressModalLabel">
                            <span class="header-content">支付密码</span>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="pay-password"><span class="red">*</span>
                                支付密码:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="pay-password" name="pay_password" placeholder="请输入支付密码"
                                       value="" type="password">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消
                        </button>
                        <button type="button" class="btn btn-primary btn-sm btn-pay ajax" data-text="确定"
                                data-method="post">确定
                        </button>
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
            //withdrawOperateEvents();
            var paymentModal = $('#paymentModal'),
                rollbackModal = $('#rollbackModal'),
                payPasswordModal = $('#payPasswordModal'),
                form = paymentModal.find('form'),
                rollbackForm = rollbackModal.find('form'),
                payPasswordBtn = payPasswordModal.find('.btn-pay');

            paymentModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget), id = obj.data('id');
                form.find('input[name="withdraw_id"]').val(id);
            });
            rollbackModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget), id = obj.data('id');
                rollbackForm.find('input[name="withdraw_id"]').val(id);
            });
            payPasswordModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget), url = obj.data('url');
                payPasswordBtn.data('url', url);
            });
            formSubmitByGet();
        });
    </script>
@stop