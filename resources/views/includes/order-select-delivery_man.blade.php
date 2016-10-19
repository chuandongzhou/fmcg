@section('body')
    @parent
    <div class="modal fade in" id="sendModal" tabindex="-1" role="dialog" aria-labelledby="sendModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="form-horizontal ajax-form" action="{{ url('api/v1/order/batch-send') }}" method="post"
                  data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                <div class="modal-content" style="width:70%;margin:auto">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                        <h4>选择配送人员</h4>
                    </div>
                    <div class="modal-body">
                        @if(isset($delivery_man) && $delivery_man->count())
                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="name">配送人员:</label>

                            </div>
                            <div class="form-group row deliverymen">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-10">
                                    @foreach($delivery_man as $index => $item)
                                        <div class="col-sm-3">
                                            <input type="checkbox" name="delivery_man_id[]"
                                                   value="{{ $index }}"> {{ $item }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            没有配送人员信息,请设置。<a href="{{ url('personal/delivery-man') }}">去设置</a>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消
                        </button>
                        @if(isset($delivery_man) && $delivery_man->count())
                            <button type="submit" class="btn btn-primary  btn-send"
                                    data-text="确定" data-method="put">确定
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script>
        $(function () {
            var sendModal = $('#sendModal'), cropperParent = null, btnSend = sendModal.find('.btn-send') /*, addDelivery = $('.add-delivery')*/;

            sendModal.on('shown.bs.modal', function (e) {
                cropperParent = $(e.relatedTarget);
                if (cropperParent.hasClass('send-goods')) {
                    btnSend.attr('data-data', '{"order_id" : "' + cropperParent.data('id') + '"}');
                } else if (cropperParent.hasClass('batch-send')) {
                    cropperParent.closest('form').find('input.order_id').each(function () {
                        var target = $(this);
                        if (target.is(':checked')) {
                            btnSend.before('<input type="hidden" name="order_id[]" value="' + target.val() + '">');
                        }
                    });
                }
                btnSend.on('click', function () {
                    var deliveryMenCount = sendModal.find('input[type="checkbox"]:checked').length;
                    if (deliveryMenCount == 0) {
                        alert('请选择配送人员');
                        return false;
                    }
                });

                /*addDelivery.on('click', function () {
                 var deliveryInfoClone = $(this).parents('.deliverymen').clone();
                 deliveryInfoClone.addClass('delivery-info-clone');
                 deliveryInfoClone.find('.add-delivery').remove();
                 deliveryInfoClone.appendTo($('.modal-body'));
                 })*/


            }).on('hidden.bs.modal', function () {
                btnSend.removeAttr('data-data');
                $('.delivery-info-clone').remove();
//                addDelivery.unbind('click');
            });
        });
    </script>
@stop