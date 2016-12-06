@extends('index.menu-master')

@section('subtitle', '个人中心-安全设置')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> >
    <a href="{{ url('personal/security/index') }}">安全设置</a>>
    <span class="second-level">修改密码</span>
@stop
@section('right')
    @include('includes.success-meg')
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
    @parent
@stop
@section('js')
    @parent
    <script>
        $(function () {
            //重置密码成功提示
            $('.submitBtn').on('done.hct.ajax', function (data, textStatus) {
                $('#shippingAddressModal').modal('hide');
                $(this).html(data.message || '操作成功');
                $('.success-meg-content').html( data.message || '操作成功');
                showSuccessMeg($('form').data('doneUrl'));
                return false;
            });

        });
    </script>
@stop
