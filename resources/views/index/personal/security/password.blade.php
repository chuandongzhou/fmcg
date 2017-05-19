@extends('index.manage-master')

@section('subtitle', '个人中心-安全设置')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/info') }}">个人中心</a> >
                    <a href="{{ url('personal/security/index') }}">安全设置</a>>
                    <span class="second-level">修改密码</span>
                </div>
            </div>
            <div class="row margin-clear">
                <div class="col-sm-12 security-setting-wrap">
                    <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/edit-password') }}"
                          method="post"
                          data-done-url="{{ url('personal/security/index') }}">
                        <div class="item title">您在通过原密码重新设置登录密码</div>
                        <div class="item">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> 输入新密码 : </label>
                                <div class="col-sm-5">
                                    <input class="form-control" name="password" type="password"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> 确认密码: </label>
                                <div class="col-sm-5">
                                    <input class="form-control" type="password" id="password-confirm"
                                           name="password_confirmation"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> </label>
                                <div class="col-sm-5">
                                    <button type="submit" class="btn btn-success submitBtn">提交</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
