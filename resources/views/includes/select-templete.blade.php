@section('body')
    <div class="modal fade" id="templeteModal" tabindex="-1" role="dialog" aria-labelledby="templeteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form" action="javascript" data-help-class="col-sm-push-2 col-sm-10"
                      data-no-loading="true"
                      autocomplete="off">
                    <div class="modal-header choice-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                        <div class="modal-title forgot-modal-title" id="templeteModalLabel">
                            <span>选择模版</span>
                        </div>
                    </div>

                    <div class="modal-body address-select">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label">选择头部模版:</label>

                            <div class="col-sm-10 col-md-6">
                                <select class="form-control col-sm-6"></select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer clearfix">
                        <button type="button" class="btn btn-blue btn-submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script type="text/javascript">
        var modal = $('#templeteModal'),
            modalBody = modal.find('.modal-body'),
            templetesPanel = modalBody.find('select'),
            redirectUrl = null;
        $('.btn-print').on('click', function () {
            var that = $(this);
            redirectUrl = that.data('url');
            if (!templetesPanel.children('option').length) {
                common.loading('show');
                $.get(site.api('templete'), '', function (data) {
                    var tempHeaders = data.tempHeaders, options = '';
                    if (!tempHeaders.length) {
                        common.loading('hide');
                        window.location.href = redirectUrl;
                        return false;
                    }

                    for (var i in tempHeaders) {
                        var detail = tempHeaders[i].name + ' ' + tempHeaders[i].contact_person + ' ' + tempHeaders[i].contact_info
                        isSelect = tempHeaders[i].is_default ? 'selected' : '';
                        options += '<option value="' + tempHeaders[i].id + '" ' + isSelect + '>' + detail + '</option>';
                    }
                    templetesPanel.html(options);
                    common.loading('hide');
                    modal.modal('show');
                })
            } else {
                modal.modal('show');
            }
        })
        modal.find('.btn-submit').on('click', function () {
            var templeteId = templetesPanel.val();
            window.location.href = redirectUrl + '&templete_id=' + templeteId;
        })
    </script>
@stop