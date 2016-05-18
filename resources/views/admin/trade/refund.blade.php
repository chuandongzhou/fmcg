@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/refund/refund') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" id="parent"/></th>
                <th>订单号</th>
                <th>卖家账号</th>
                <th>交易号</th>
                <th>金额</th>
                <th>交易时间</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($trades as $trade)
                <tr>
                    <th scope="row"><input type="checkbox" class="child" name="id[]" value="{{ $trade->id }}"/></th>
                    <td>{{ $trade->order_id }}</td>
                    <td>{{ $trade->account }}</td>
                    <td>{{ $trade->trade_no }}</td>
                    <td>{{ $trade->amount }}</td>
                    <td>{{ $trade->success_at }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">

                            <a class="btn btn-primary ajax no-prompt" data-method="post"
                               data-url="{{ url('admin/refund/refund') }}"
                               data-data='{"id":"{{ $trade->id }}"}'
                            >
                                <i class="fa fa-check"></i> 退款
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-primary ajax no-prompt" data-method="post">
                <i class="fa fa-adjust"></i> 批量退款
            </button>
        </div>
    </form>
    {!! $trades->render() !!}
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('#parent', '.child');
        })
    </script>
@stop