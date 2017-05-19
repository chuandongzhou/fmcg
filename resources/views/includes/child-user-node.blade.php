@section('body')
    <div class="modal modal1 fade" id="childUserNodeModal" tabindex="-1" role="dialog"
         aria-labelledby="childUserNodeLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width:850px;">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>设置权限</span>
                    </div>
                </div>
                <div class="modal-body padding-clear">
                    <iframe src="" width="100%" style="border: 0px; min-height: 540px"></iframe>
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
            var childUserNodeModal = $('#childUserNodeModal'),
                iframe = childUserNodeModal.find('iframe');
            childUserNodeModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget), id = obj.data('id');
                iframe.attr('src', '{{ url('personal/child-user') }}' + '/' + id);
            }).on('hide.bs.modal', function () {
                iframe.attr('src', '');
            });
        });
    </script>
@stop
