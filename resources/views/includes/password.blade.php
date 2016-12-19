@section('body')
    <div class="modal modal1 fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width:800px;">
            <div class="modal-content" style="width:800px;margin:auto">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>选择修改登录密码方式</span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="col-sm-4">
                        <input type="radio" name="change-password-by"
                               data-url="{{ url('personal/security/validate-phone?type=password') }}" checked/>通过密保手机号
                    </div>
                    <div class="col-sm-4">
                        <input type="radio" name="change-password-by"
                               data-url="{{ url('personal/security/by-old-password') }}"/>通过原登录密码

                    </div>
                </div>
                <div class="modal-footer middle-footer text-left">
                    <a href="javascript:" type="button" class="next-step btn btn-success">下一步</a>
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
            $('.next-step').on('click', function () {
                $('input[name="change-password-by"]').each(function () {
                    var obj = $(this);
                    if (obj.is(':checked')) {
                        window.location.href = obj.data('url');
                        return false;
                    }
                })
            })
        });
    </script>
@stop
