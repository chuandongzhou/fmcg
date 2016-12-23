@section('body')
    <div class="modal fade" id="salesmanModal" tabindex="-1" role="dialog" aria-labelledby="salesmanModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="cropperModalLabel">
                        添加业务员<span class="extra-text"></span>
                    </div>
                </div>
                <form class="form-horizontal ajax-form"
                      data-help-class="col-sm-push-2 col-sm-10"
                      data-done-url="{{ url('business/salesman') }}" data-no-loading="true" autocomplete="off">
                    <div class="modal-body ">

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="username">头像:</label>

                            <div class="col-sm-10 col-md-6">
                                <button class="btn btn-success btn-sm" data-height="128" data-width="128"
                                        data-target="#cropperModal" data-toggle="modal" data-name="avatar"
                                        type="button">
                                    本地上传(128x128)
                                </button>
                                <div class="image-preview">
                                    <img class="img-thumbnail" src="">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="account">账号:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="account" name="account" placeholder="请输入业务员账号"
                                       type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="password">密码:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="password" name="password" placeholder="请输入密码"
                                       type="password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="password_conformation">确认密码:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="password_confirmation" name="password_confirmation"
                                       placeholder="请重复输入密码"
                                       type="password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="name">名称:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="name" name="name" placeholder="请输入业务员名称"
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="contact_information">联系方式:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="contact_information" name="contact_information"
                                       placeholder="请输入业务员联系方式"
                                       type="text">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer middle-footer">
                        <button class="btn btn-success pull-right" data-method="post"
                                data-url="{{ url('api/v1/business/salesman' ) }}" type="submit">提交
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script>
        $(function () {
            var salesmanModel = $('#salesmanModal'),
                    form = salesmanModel.find('form'),
                    targetParent = null,
                    salesmanId = 0,
                    avatarThumbnail = $('img.img-thumbnail'),
                    avatar = $('input[name="avatar"]'),
                    account = $('input[name="account"]'),
                    password = $('input[name="password"]'),
                    passwordConfirmation = $('input[name="password_confirmation"]'),
                    name = $('input[name="name"]'),
                    contactInformation = $('input[name="contact_information"]'),
                    submitBtn = $('button[type="submit"]'),
                    modelTitle = $('.modal-title');

            salesmanModel.on('show.bs.modal', function (e) {
                targetParent = $(e.relatedTarget);
                salesmanId = targetParent.data('id');
                if (salesmanId) {
                    modelTitle.html('编辑业务员');
                    $.get(site.api('business/salesman/' + salesmanId), '', function (data) {
                        var salesman = data.salesman;
                        avatarThumbnail.attr('src', salesman.avatar_url);
                        account.val(salesman.account).prop('disabled', true);
                        name.val(salesman.name);
                        contactInformation.val(salesman.contact_information);
                        submitBtn.data('method', 'put').data('url', site.api('business/salesman/') + salesmanId);
                    }, 'json')

                }
            }).on('hidden.bs.modal', function () {
                modelTitle.html('添加业务员');
                avatarThumbnail.attr('src', '');
                account.val('').prop('disabled', false);
                name.val('');
                password.val('');
                passwordConfirmation.val('');
                contactInformation.val('');
                submitBtn.data('method', 'post').data('url', '{{ url('api/v1/business/salesman' ) }}');
                avatar.remove();
                form.formValidate('reset');
            });

        })
    </script>
@stop