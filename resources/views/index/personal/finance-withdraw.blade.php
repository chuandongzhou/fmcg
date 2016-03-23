@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '个人中心-商家信息')

@section('right')
    <form method="get" action="{{ url('personal/finance/withdraw') }}" autocomplete="off">
        <div class="row">
            <div class="col-sm-12 ">
                @include('index.personal.finance-common')
                <div class="table-responsive">
                    <div class="time form-group">
                        提现单号 : <input type="text" class=" inline-control" name="withdrawId" value="" />
                        时间段： <input class="datetimepicker inline-control" name="start_time" data-format="YYYY-MM-DD"
                                    type="text"
                                    value="{{ $data['start_time'] or '' }}"> 至
                        <input class="datetimepicker inline-control" name="end_time" data-format="YYYY-MM-DD"
                               value="{{ $data['end_time'] or '' }}"
                               type="text">
                        <input type="submit" class="btn btn-default search-by-get">
                    </div>

                    <table class="table table-bordered table-center">
                        <thead>
                            <tr>
                                <th>提现单号</th>
                                <th>提现金额</th>
                                <th>银行卡所有人</th>
                                <th>银行账号</th>
                                <th>银行名称</th>
                                <th>状态</th>
                                <th>交易单号</th>
                                <th>操作详情</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($flag)
                                @foreach($withdraws as $withdraw)
                                    <tr>
                                        <td>{{ $withdraw->id }}</td>
                                        <td>{{ $withdraw->amount }}</td>
                                        <td>{{ $withdraw->card_holder }}</td>
                                        <td>{{ $withdraw->card_number }}</td>
                                        <td>{{ cons()->valueLang('bank.type')[$withdraw->card_type] }}</td>
                                        <td>{{ $withdraw->status_info }} </td>
                                        <td>{{ $withdraw->trade_no }} </td>
                                        <td><a class="show-item btn btn-success" data-target="#withdraw-item" data-toggle="modal"
                                               data-data='{!! json_encode(['created_at'=>$withdraw->created_at->toDateTimeString(),'failed_at'=>$withdraw->failed_at->toDateTimeString(),'pass_at'=>$withdraw->pass_at->toDateTimeString(),'payment_at'=>$withdraw->payment_at->toDateTimeString(),'reason'=>$withdraw->reason]) !!}' >详细信息</a></td>
                                    </tr>
                                @endforeach

                            @elseif(is_null($withdraws))
                                <tr>
                                    查无此订单
                                </tr>
                            @else
                                <tr>
                                    <td>{{ $withdraws->id }}</td>
                                    <td>{{ $withdraws->amount }}</td>
                                    <td>{{ $withdraws->userBanks->card_number }}</td>
                                    <td>{{ cons()->valueLang('bank.type')[$withdraws->userBanks->card_type] }}</td>
                                    <td>{{ cons()->valueLang('withdraw')[$withdraws->status] }} </td>
                                    <td>{{ $withdraws->trade_no }} </td>
                                    <td><a class="show-item btn btn-success" data-target="#withdraw-item" data-toggle="modal"
                                           data-data='{!! json_encode(['created_at'=>$withdraws->created_at->toDateTimeString(),'failed_at'=>$withdraws->failed_at->toDateTimeString(),'pass_at'=>$withdraws->pass_at->toDateTimeString(),'payment_at'=>$withdraws->payment_at->toDateTimeString(),'reason'=>$withdraws->reason]) !!}' >详细信息</a></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
    @include('includes.withdraw')
    <div class="modal fade in" id="withdraw-item" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <p class="modal-title">操作详情:
                    </p>
                </div>
                <div class="modal-body">
                    <div class="text-left">
                        <p>创建时间：xxxxxxxx</p>
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">关闭</button>
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
            picFunc();
            getWithdrawTimeItem();
            formSubmitByGet();
        });
    </script>
@stop



