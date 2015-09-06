@extends('index.manage-left')

@section('subtitle', '个人中心-修改密码')

@section('right')
    <div class="col-sm-10  personal-center">
        <div class="col-sm-12 switching">
            <a href="#" class="btn">商家信息</a>
            <a href="#" class="btn">体现账号</a>
            <a href="#" class="btn">人员管理</a>
            <a href="#" class="btn">配送人员</a>
            <a href="#" class="btn active">修改密码</a>
            <a href="#" class="btn">账号余额</a>
        </div>
        <form class="form-horizontal ajax-form" method="put"
              action="{{ url('api/v1/personal-center/password') }}" data-help-class="col-sm-push-2 col-sm-10">
            <div class="col-sm-9 user-show">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="password-old">旧密码:</label>

                    <div class="col-sm-10 col-md-6">
                        <input class="form-control"  id="password-old" name="old_password" placeholder="请输入原密码" type="password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="password-old">新密码:</label>

                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" id="password-new" name="password" placeholder="请输入新密码" type="password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="password-confirm">确认新密码:</label>

                    <div class="col-sm-10 col-md-6">
                        <input type="password" placeholder="请确认新密码" id="password-confirm" name="password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="col-sm-12 text-center save">
                    <button class="btn btn-bg btn-primary" type="submit"><i class="fa fa-save"></i> 提交</button>
                </div>

            </div>
        </form>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            picFunc();
        })
    </script>
@stop
