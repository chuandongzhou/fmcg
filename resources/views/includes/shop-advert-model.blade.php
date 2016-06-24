@section('body')
    <div class="modal fade" id="shopAdvertModal" tabindex="-1" role="dialog" aria-labelledby="noticeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="cropperModalLabel"><span class="extra-text"></span></h4>
                </div>
                <div class="modal-body notice-content">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop