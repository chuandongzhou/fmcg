@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '个人中心-商家信息')

@section('right')
    <form method="get" action="{{ url('personal/withdraw') }}" autocomplete="off">
        <div class="row">
            <div class="col-sm-12 ">
                <div class="account-balance">
                    <label>账户余额 :</label>
                    <b class="balance red">￥{{ $balance }}</b>
                </div>
                <div class="protected-balance">
                    <label>结算保护余额 :</label>
                    <b class="balance red">￥{{ $protectedBalance }}</b>
                </div>
                <div class="can-withdraw-balance">
                    <label>可提现余额 :</label>
                    <b class="balance red">￥{{ sprintf('%.2f' , $balance - $protectedBalance) }}</b>
                    <a class="btn btn-primary" data-target="#withdraw" data-toggle="modal">提现</a>
                </div>

                <div class="personal-center">
                    <div class=" switching">
                        <a href="{{ url('personal/balance') }}" class="btn">流水账</a>
                        <a href="{{ url('personal/withdraw') }}" class="btn active">提现记录</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <p class="time">
                        提现单号 : <input type="text" name="withdrawId" value="" />
                        时间段 : <input class="datetimepicker" name="start_time" data-format="YYYY-MM-DD" type="text"
                                   value="{{ $startTime }}"> 至
                        <input class="datetimepicker" name="end_time" data-format="YYYY-MM-DD"
                               value="{{ $endTime }}"
                               type="text">
                        <input type="submit" class="btn btn-warning" value="查看">
                    </p>

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
        });
    </script>
@stop



