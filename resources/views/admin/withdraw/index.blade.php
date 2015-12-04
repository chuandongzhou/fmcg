@extends('admin.master')
@include('includes.timepicker')
@section('right-container')
    <form class="form-horizontal" method="get" action="{{ url('admin/system-withdraw') }}" autocomplete="off">

        <div class="form-group">
            <label for="order_num" class="col-sm-2 control-label">提现单号：</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" name="withdraw_id" placeholder="请输入订单号"
                       value="{{ $withdrawId or '' }}">
            </div>
        </div>

        <div class="form-group">
            <label for="trade_num" class="col-sm-2 control-label">交易单号：</label>

            <div class="col-sm-4">
                <input type="text" value="{{  $tradeNo or  '' }}" class="form-control"
                       name="trade_no" placeholder="请输入交易号">
            </div>
        </div>

        <div class="form-group">
            <label for="account" class="col-sm-2 control-label">商家账号：</label>

            <div class="col-sm-4">
                <input type="text" value="{{ $userName or '' }}" class="form-control"
                       name="user_name" placeholder="请输入商家账号">
            </div>
        </div>
        <div class="form-group">
            <label for="account" class="col-sm-2 control-label">申请时间：</label>

            <div class="col-sm-6 time-limit">
                <input type="text" class="inline-control datetimepicker" name="started_at" value="{{ $startedAt  }}"> 至
                <input type="text" class="inline-control datetimepicker" name="end_at" value="{{ $endAt }}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <button type="submit" class="btn btn-bg btn-primary search-by-get">查询</button>
                &nbsp;&nbsp;&nbsp;&nbsp;
                {{--<a href="{{ url('admin/system-trade/export-to-excel?' . $linkUrl) }}" class="btn btn-bg btn-warning">导出</a>--}}
            </div>
        </div>
        <table class="table table-striped table-center">
            <thead>
            <tr>
                <th>提现单号</th>
                <th>提现金额</th>
                <th>商家账号</th>
                <th>银行卡所有人</th>
                <th>银行账号</th>
                <th>银行名称</th>
                <th>开户行地址</th>
                <th>状态</th>
                <th>交易单号</th>
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
                                    <a class="rollback btn btn-danger" data-target="#rollback" data-toggle="modal"
                                       data-id='{{ $withdraw->id }}'>
                                        <i class="fa fa-trash-o"></i> 回退
                                    </a>
                                @endif
                                @if($withdraw->status == cons('withdraw.pass'))
                                    {{--打款需要交易号，回退需要回退原因--}}
                                    <a class="payment btn btn-success" data-target="#payment" data-toggle="modal"
                                       data-id='{{ $withdraw->id }}'>
                                        <i class="fa fa-edit"></i> 已打款
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>没有符合条件的信息</td>
                </tr>
            @endif
            </tbody>
        </table>
    </form>
    {!! $withdraws->render() !!}
    <div class="modal fade in" id="rollback" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <p class="modal-title">回退原因:</p>
                </div>
                <div class="modal-body">
                    <div class="text-left">
                        <textarea name="reason" cols="40" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary btn-sm btn-add ajax" data-text="确定"
                                data-url="{{ url('admin/system-withdraw/failed') }}" data-method="put">确定
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="payment" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <p class="modal-title">交易单号:
                        <input type="text" name="tradeNo"/>
                    </p>
                </div>
                <div class="modal-body">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary btn-sm btn-add ajax" data-text="确定"
                                data-url="{{ url('admin/system-withdraw/payment') }}" data-method="put">确定
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            withdrawOperateEvents();
            formSubmitByGet();
        });
    </script>
@stop