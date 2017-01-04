@extends('index.menu-master')

@section('subtitle', '个人中心-安全设置')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> >
    <a href="{{ url('personal/security/index') }}">安全设置</a>>
    <span class="second-level">修改密保手机</span>
@stop
@section('right')
    @include('includes.success-meg')
    <div class="row margin-clear">
        <div class="col-sm-12 security-setting-wrap">
            <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/edit-backup-phone') }}"
                  method="post" data-done-url="{{ url('personal/security/index') }}">
                <div class="item title">您在通过原来密保手机验证码重新设置密保手机号</div>
                <div class="item">
                    <div class="form-group">
                        <label class="col-sm-3 control-label"> 新密保手机 : </label>
                        <div class="col-sm-5">
                            <input class="form-control" name="backup_mobile"/>
                        </div>
                        <div class="col-sm-2">
                            <a class="btn code-btn send-sms ajax"
                               data-url="{{url('api/v1/personal/security/new-backup-sms')}}" data-method="get"
                               data-done-then="none" data-prevent-default="none">
                                获取验证码
                            </a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"> 手机验证码 : </label>
                        <div class="col-sm-5">
                            <input class="form-control" name="code"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"> </label>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-success btn-submit submitBtn">提交</button>
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
            $('.send-sms').on('done.hct.ajax', function (data, textStatus) {
                $('body').find('.loading').remove();
                var $this = $(this);
                // 成功进行倒计时
                timeIntervalFunc({
                    tick: function (i) {
                        $this.html(i + ' 秒后重试');
                    },
                    done: function () {
                        $this.button('reset');
                    },
                    count: 120
                });
            })
    </script>
@stop
