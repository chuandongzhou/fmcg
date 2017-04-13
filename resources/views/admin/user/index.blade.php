@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal" action="{{ url('admin/user') }}" method="get" autocomplete="off">
        <label for="name">用户名:</label>
        <input type="text" name="name" class="time inline-control" value="{{ $filter['name'] }}"/>
        @if ($filter['type'] == 'wholesaler' || $filter['type'] == 'supplier')
            <label for="deposit_pay">保证金:</label>
            <select class="inline-control" name="deposit_pay">
                <option value="">全部</option>
                <option value="1" {{ $filter['depositPay'] ==='1' ? 'selected' : '' }}>已缴纳</option>
                <option value="0" {{ $filter['depositPay'] ==='0' ? 'selected' : '' }}>未缴纳</option>
            </select>
        @endif
        <input type="hidden" name="type" value="{{ $filter['type'] }}">
        <input type="submit" class="btn btn-default  search-by-get" value="查询"/>
    </form>

    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/admin/') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" id="parent"/></th>
                <th>用户名</th>
                <th>昵称</th>
                @if ($filter['type'] == 'wholesaler' || $filter['type'] == 'supplier')
                    <th>保证金</th>
                    <th>到期时间</th>
                @endif
                <th>注册时间</th>
                <th>状态</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <th scope="row"><input type="checkbox" class="child" name="uid[]" value="{{ $user->id }}"/></th>
                    <td>{{ $user->user_name }}</td>
                    <td>{{ $user->shop_name }}</td>
                    @if ($filter['type'] == 'wholesaler' || $filter['type'] == 'supplier')
                        <td>{{ $user->deposit ? '已' : '未' }}缴纳</td>
                        <td>{{ $user->expire_at }}</td>
                    @endif
                    <td>{{ $user->created_at }}</td>
                    <td>{{ cons()->valueLang('status', $user->status) }}</td>
                    <td>
                        <div class="btn-group-xs" role="group">
                            <a class="btn btn-blue" href="{{ url('admin/user/' . $user->id . '/edit') }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <a class="btn btn-default" href="{{ url('admin/shop/' . $user->shop['id'] . '/edit') }}">
                                <i class="fa fa-user"></i> 查看
                            </a>
                            <a type="button" class="btn btn-danger ajax" data-method="delete" data-danger="真的要删除吗？"
                               data-url="{{ url('admin/user/' . $user->id) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </a>
                            @if ($filter['type'] == 'wholesaler' || $filter['type'] == 'supplier')
                                @if($user->deposit)
                                    <a class="btn btn-default" href="javascript:" data-toggle="modal"
                                       data-target="#expireModal" data-id="{{ $user->id }}">
                                        <i class="fa fa-money"></i> 续费
                                    </a>
                                @else
                                    <a class="btn btn-default ajax" data-danger="确定已缴纳吗？"
                                       data-url="{{ url('admin/user/' . $user->id . '/deposit') }}">
                                        <i class="fa fa-check"></i> 已缴纳保证金
                                    </a>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="delete"
                    data-url="{{ url('admin/user/batch') }}">
                <i class="fa fa-trash-o"></i> 删除
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-primary ajax" data-method="put" data-data='{"status":1}'
                    data-url="{{ url('admin/user/switch') }}">
                <i class="fa fa-adjust"></i> 启用
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="put" data-data='{"status":0}'
                    data-url="{{ url('admin/user/switch') }}">
                <i class="fa fa-trash-o"></i> 禁用
            </button>
        </div>
    </form>
    <div class="right">
        {!! $users->appends(array_filter($filter))->render() !!}
    </div>
    @if ($filter['type'] == 'wholesaler' || $filter['type'] == 'supplier')
        <div class="modal fade" id="expireModal" tabindex="-1" role="dialog" aria-labelledby="expireModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form class="form-horizontal ajax-form address-form" action="{{ url('admin/user/expire') }}"
                          method="post" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                        <div class="modal-header choice-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                            <div class="modal-title forgot-modal-title" id="addressModalLabel">
                                <span>帐户续费</span>
                            </div>
                        </div>
                        <div class="modal-body address-select">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="month">月份:</label>

                                <div class="col-sm-10 col-md-6">
                                    <input class="form-control" id="month" name="month" placeholder="请输入要缴费的月份"
                                           type="text">
                                    <input type="hidden" name="user_id">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer middle-footer">
                            <button type="submit" class="btn btn-success pull-right no-prompt">确认</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('#parent', '.child');
            formSubmitByGet();
            $('#expireModal').on('shown.bs.modal', function (e) {
                var obj = $(this),
                    targetParent = $(e.relatedTarget),
                    id = targetParent.data('id');
                obj.find('input[name="user_id"]').val(id);
            })
        })
    </script>
@stop