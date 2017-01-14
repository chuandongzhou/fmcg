@section('body')

    <div class="modal fade" id="templetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg templet-dialog">
            <div class="modal-content templet-content" style="margin:auto">
                <div class="modal-body text-center" style="padding:0;">
                    <img style="width:100%" class="templet-img" src="">
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
            //模板选择成功事件
            var templeteModal = $('#templetModal');
            templeteModal.on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget),
                    src = parent.prev('img').length?parent.prev('img').attr('src'):parent.data('src'),
                    templeteImg = templeteModal.find('.templet-img');
                parent.data('width')?$('.modal-dialog').css('width',parent.data('width')):'';
                templeteImg.attr('src', src);
            });
        });
    </script>
@stop