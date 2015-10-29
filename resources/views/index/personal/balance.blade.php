@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '个人中心-商家信息')

@section('right')
    @include('index.personal.tabs')
    <form method="get" action="{{ url('personal/balance') }}">
        <div class="row">
            <div class="col-sm-12 ">
                <div class="account-balance">
                    <label>账户余额 :</label>
                    <b class="balance red">￥{{ $balance }}</b>
                    <a class="btn btn-primary" data-target="#withdraw" data-toggle="modal">提现</a>
                </div>
                <div class="personal-center">
                    <div class=" switching">
                        <a href="{{ url('personal/balance') }}" class="btn active">流水账</a>
                        <a href="{{ url('personal/withdraw') }}" class="btn">提现记录</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <p class="time">
                        时间段 <input class="datetimepicker" name="start_time" data-format="YYYY-MM-DD" type="text"
                                   value="{{ $startTime }}"> 至
                        <input class="datetimepicker" name="end_time" data-format="YYYY-MM-DD"
                               value="{{ $endTime }}"
                               type="text">
                        <input type="submit" class="btn btn-warning">
                    </p>
                    <table class="table table-bordered table-center">
                        <thead>
                            <tr>
                                <th>订单号</th>
                                <th>支付金额</th>
                                <th>手续费</th>
                                <th>支付平台</th>
                                <th>交易号</th>
                                <th>交易时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tradeInfo as $trade)
                                <tr>
                                    <td>{{ $trade->order_id }}</td>
                                    <td><b class="red">￥{{ $trade->amount }}</b></td>
                                    <td>{{ $trade->target_fee }}</td>
                                    <td>{{ cons()->valueLang('trade.pay_type')[$trade->pay_type] }}</td>
                                    <td>{{ $trade->trade_no }}</td>
                                    <td>{{ $trade->success_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade in" id="withdraw" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <p class="modal-title">提现金额:
                            <span class="extra-text">
                              ￥<input type="text" name="amount" />
                            </span>
                        <span class="tip" style="display: none;color:red;" >请输入数字</span>
                    </p>
                </div>
                <div class="modal-header">
                   <p class="modal-title">提现账号:
                            <span class="extra-text">
                                @if(empty($bankInfo))
                                    没得账号
                                @else
                                    <select name="bank" >
                                        @foreach($bankInfo as $bank)
                                            <option {{$bank->is_default == 1 ? 'selected' :""}} value="{{ $bank->id }}">{{ $bank->card_holder }}--{{ $bank->card_number }}--{{ $bank->card_address }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </span>
                    </p>
                </div>

                <div class="modal-body">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary btn-sm btn-add ajax" data-text="确定" data-url="{{ url('api/v1/personal/withdraw/add-withdraw') }}" data-method="post">确定</button>
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
            getWithdraw({{ $balance }});
        });
    </script>
@stop
