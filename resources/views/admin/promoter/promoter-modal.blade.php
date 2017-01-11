@include('includes.timepicker')
@section('body')
    @parent
    <div class="modal fade" id="promoterModal" tabindex="-1" role="dialog" aria-labelledby="promoterModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <div class="modal-title dis-modal-title" id="myModalLabel">添加推广员</div>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal ajax-form" method="post" action="{{ url('admin/promoter') }}"
                          data-help-class="col-sm-push-2 col-sm-10"
                          data-done-url="{{ url('admin/promoter') }}" autocomplete="off">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">推广员姓名:</label>

                            <div class="col-xs-5">
                                <input class="form-control control-radius" name="name" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">联系方式:</label>

                            <div class="col-xs-5">
                                <input class="form-control control-radius" name="contact" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">生效时间:</label>

                            <div class="col-xs-5">
                                <input class="form-control control-radius datetimepicker" data-format="YYYY-MM-DD"
                                       name="start_at" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">过期时间:</label>
                            <div class="col-xs-5">
                                <input class="form-control control-radius datetimepicker" data-format="YYYY-MM-DD"
                                       name="end_at" type="text">
                            </div>
                            <div class="col-xs-2">
                                <label class="control-label">不填时为永久</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label"></label>
                            <div class="col-xs-5">
                                <button class="btn btn-blue control" type="submit">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal -->
@stop
@section('js')
    @parent
    <script type="text/javascript">
        var promoterModal = $('#promoterModal'),
            namePanel = promoterModal.find('input[name="name"]'),
            contactPanel = promoterModal.find('input[name="contact"]'),
            startAtPanel = promoterModal.find('input[name="start_at"]'),
            endAtPanel = promoterModal.find('input[name="end_at"]'),
            submitPanel = promoterModal.find('button[type="submit"]');
        promoterModal.on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget),
                id = parent.data('id'),
                name = parent.data('name'),
                contact = parent.data('contact'),
                start_at = parent.data('startAt'),
                end_at = parent.data('endAt');
            namePanel.val(name);
            contactPanel.val(contact);
            startAtPanel.val(start_at);
            endAtPanel.val(end_at);
            if (id) {
                submitPanel.data('method', 'put');
                submitPanel.data('url', '{{ url("admin/promoter") }}' + '/' + id);
            }
        }).on('hide.bs.modal', function () {
            submitPanel.data('method', 'post');
            submitPanel.data('url', '{{  url("admin/promoter") }}');
        })
    </script>
@stop