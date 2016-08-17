@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/user/multi_audit') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" id="parent"/></th>
                <th>用户名</th>
                <th>昵称</th>
                <th>账号类别</th>
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
                    <td>{{ $user->shop ? $user->shop_name : '' }}</td>
                    <td>{{ cons()->valueLang('user.type', $user->type) }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ cons()->valueLang('user.audit_status', $user->audit_status) }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-default" href="{{ url('admin/shop/' . $user->shop['id'] . '/edit') }}">
                                <i class="fa fa-user"></i> 查看
                            </a>
                            <a class="btn btn-primary ajax" data-method="put"
                               data-url="{{ url('admin/user/audit/' . $user->id) }}"
                               data-data='{"status":"{{ cons('user.audit_status.pass') }}"}'
                            >
                                <i class="fa fa-check"></i> 审核通过
                            </a>
                            @if($user->audit_status != cons('user.audit_status.not_pass'))
                                <a class="audit-not-pass btn btn-danger" data-target="#audit" data-toggle="modal"
                                   data-url="{{ url('admin/user/audit/' . $user->id) }}">
                                    <i class="fa fa-close"></i> 审核不通过
                                </a>

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
            <button type="button" class="btn btn-primary ajax" data-method="put"
                    data-data={"status":"{{ cons('user.audit_status.pass') }}"}>
                <i class="fa fa-adjust"></i> 批量审核通过
            </button>
        </div>
        {{--<div class="btn-group btn-group-xs" role="group">--}}
        {{--<button type="button" class="btn btn-danger ajax" data-method="put"--}}
        {{--data-data={"status":"{{ cons('user.audit_status.not_pass') }}"}>--}}
        {{--<i class="fa fa-trash-o"></i> 批量审核不通过--}}
        {{--</button>--}}
        {{--</div>--}}
    </form>
    {!! $users->render() !!}

    <div class="modal fade in" id="audit" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="cropperModalLabel">不通过原因<span class="extra-text"></span></h4>
                </div>
                <div class="modal-body address-select">
                    <form class="form-horizontal ajax-form" action=""
                          method="put" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="name">原因:</label>

                            <div class="col-sm-10 col-md-8">
                                <textarea class="form-control" name="reason"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="modal-footer">
                                <button type="submit" data-data='{"status":"{{ cons('user.audit_status.not_pass') }}"}'
                                        class="btn btn-primary btn-sm btn-add" data-text="提交">提交
                                </button>
                                <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">关闭
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('#parent', '.child');

            var model = $('#audit');
            $('a.audit-not-pass').click(function () {
                var actionUrl = $(this).data('url');
                model.find('form').attr('action', actionUrl);
            })
            model.on('hidden.bs.modal', function () {
                alert('343');
                $(this).find('form').attr('action', '');
            });
        })
    </script>
@stop