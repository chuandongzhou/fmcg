@section('body')
    <div class="modal fade" id="noticeModal" tabindex="-1" role="dialog" aria-labelledby="noticeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span></span>
                    </div>
                </div>
                <div class="modal-body notice-content">

                </div>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 5000
            });
            $('.content-title').on('click', function () {
                var obj = $(this);
                $('.modal-title').html(obj.attr('title'));
                $('.notice-content').html(obj.data('content'));
            })
        });
    </script>
@stop