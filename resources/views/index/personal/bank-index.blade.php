@extends('index.menu-master')
@section('subtitle', '个人中心-提现账号')

@section('right')
    @include('index.personal.tabs')
    <form action="#" method="post">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div>
                    <label>默认收款账号</label>
                    <a class="add" href="{{ url('personal/bank/create') }}">
                        <label><span class="fa fa-plus"></span></label>
                        添加账号
                    </a>
                </div>
                <table class="table table-bordered table-center">
                    <thead>
                    <tr>
                        <th>卡号</th>
                        <th>银行</th>
                        <th>开户人</th>
                        <th>所在地</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($defaultBank as $bank)
                        <tr>
                            <td>
                                {{ $bank['card_number'] }}
                            </td>
                            <td>
                                {{ cons()->valueLang('bank.type' , $bank['card_type']) }}
                            </td>
                            <td>
                                {{ $bank['card_holder'] }}
                            </td>
                            <td>
                                {{ $bank['card_address'] }}
                            </td>
                            <td>
                                <a href="{{ url('personal/bank/' . $bank['id'] . '/edit') }}"
                                   class="btn btn-success">编辑</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            <div class="col-sm-12 table-responsive">
                <div>
                    <label>备用收款账号</label>
                </div>
                <table class="table-bordered table table-center">
                    <thead>
                    <tr>
                        <th>卡号</th>
                        <th>银行</th>
                        <th>开户人</th>
                        <th>所在地</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($userBanks as $bank)
                        <tr>
                            <td>
                                {{ $bank['card_number'] }}
                            </td>
                            <td>
                                {{ cons()->valueLang('bank.type' , $bank['card_type']) }}
                            </td>
                            <td>
                                {{ $bank['card_holder'] }}
                            </td>
                            <td>
                                {{ $bank['card_address'] }}
                            </td>
                            <td>
                                <a class="btn btn-primary ajax"
                                   data-url="{{ url('api/v1/personal/bank-default/'.$bank['id']) }}"
                                   data-method="post">
                                    设置为默认
                                </a>
                                <a href="{{ url('personal/bank/' . $bank['id'] . '/edit') }}"
                                   class="btn btn-success">编辑</a>
                                <a class="btn btn-cancel ajax"
                                   data-url="{{ url('api/v1/personal/bank/'.$bank['id']) }}"
                                   data-method="delete">删除</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    @parent
@stop
