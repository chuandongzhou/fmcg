@extends('index.menu-master')

@section('subtitle', '个人中心-修改密码')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> >
    <a href="{{ url('personal/security/index') }}">安全设置</a>>
    <span class="second-level">修改密码</span>
@stop
@section('right')
    <div class="row margin-clear">
        <div class="col-sm-12 security-setting-wrap">
            <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/validate-old-password') }}"
                  method="post"
                  data-done-url="{{ url('personal/security/edit-password') }}">
                <div class="item title">您在通过原来密保手机验证码重新设置密保手机号</div>
                <div class="item">

                    <div class="form-group">
                        <label class="col-sm-3 control-label"> 请输入原密码 : </label>
                        <div class="col-sm-5">
                            <input class="form-control" type="password" name="old_password"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"> </label>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-success">下一步</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @parent
@stop
