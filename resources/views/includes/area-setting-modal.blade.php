<div class="modal modal1 fade" id="area" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content" style="width:800px;margin:auto">
            <div class="modal-header choice-header prop-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <div class="modal-title forgot-modal-title" id="myModalLabel">
                    <span class="modal_name">添加业务区域</span>
                </div>
            </div>
            <form class="form-horizontal ajax-form area" action="{{asset('api/v1/business/area')}}" method="post"
                  data-help-class="col-sm-push-2 col-sm-10"
                  autocomplete="off">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="num"> 区域名称:</label>
                        <div class="col-sm-10 col-md-5">
                            <input type="text" name="name" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-group row address-detail">
                        <label class="col-sm-2 control-label" for="num"> 备注:</label>
                        <div class="col-sm-10 col-md-8">
                            <input type="text" name="remark" class="form-control" maxlength="30" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer middle-footer text-center">
                    <button type="submit" class="btn btn-success">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('js')
    <script>
        $(function () {
            var add_modify = $('div#area'),
                    form = $('form.area');
            add_modify.on('shown.bs.modal', function (parent) {
                var obj = $(parent.relatedTarget);
                action = '添加业务区域';
                if (obj.hasClass('edit')) {
                    action = '修改业务区域';
                    form.attr('method', 'put');
                    form.attr('action', site.api('business/area/' + obj.data('id')));
                    $(this).find('input[ name = name ]').val(obj.data('name'));
                    $(this).find('input[ name = remark ]').val(obj.data('remark'))
                }
                $(this).find('.modal_name').html(action);
            }).on('hidden.bs.modal', function () {
                $(this).find('input').val('');
                $('p.ajax-error').remove();
            });
        })
    </script>
@endsection