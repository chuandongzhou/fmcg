<!-- 弹出层 -->
@section('body')
    <div class="modal fade" id="qrcodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">店铺二维码</h4>
                </div>
                <div class="modal-body text-center">
                    <div id="qrcode"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">关闭
                    </button>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/lib/jquery/qrcode/jquery.qrcode.min.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var qrcodeModal = $('#qrcodeModal');
            qrcodeModal.on('show.bs.modal', function (e) {
                var codeParent = $(e.relatedTarget),
                        url = codeParent.data('url'),
                        qrcodeDiv = qrcodeModal.find('div#qrcode');
                if (!qrcodeDiv.children('canvas').length) {
                    qrcodeDiv.qrcode(url);
                }
            });
        })
    </script>
@stop
