@section('body')
    @parent
    <div class="modal fade in" id="salesmanOrder" tabindex="-1" role="dialog" aria-labelledby="salesmanOrderModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/business/order/change') }}" method="post"
                      data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="id"/>

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="cropperModalLabel">订单修改<span class="extra-text"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="price">单价:</label>

                            <div class="col-sm-10 col-md-5">
                                <input type="text" name="price" class="form-control" placeholder="请输入单价"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="num">数量:</label>

                            <div class="col-sm-10 col-md-5">
                                <input type="text" name="num" class="form-control" placeholder="请输入数量"/>
                            </div>
                        </div>

                        <div class="form-group row hidden">
                            <label class="col-sm-2 control-label " for="amount">退货金额:</label>

                            <div class="col-sm-10 col-md-5">
                                <input type="text" name="amount" disabled class="form-control" placeholder="请输入退货金额"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="pieces">单位:</label>

                            <div class="col-sm-2 col-md-2">
                                <select name="pieces" class="form-control">
                                    <option value="">请选择单位</option>
                                    @foreach(cons()->valueLang('goods.pieces') as $id=> $pieces)
                                        <option value="{{ $id }}">{{ $pieces }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary btn-sm btn-add" data-text="确定">
                            确定
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var salesmanOrderModal = $('#salesmanOrder'),
                    priceControl = $('input[name="price"]'),
                    numControl = $('input[name="num"]'),
                    piecesControl = $('select[name="pieces"]'),
                    amountControl = $('input[name="amount"]'),
                    idControl = $('input[name="id"]');
            salesmanOrderModal.on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget),
                        price = parent.data('price'),
                        id = parent.data('id'),
                        num = parent.data('num'),
                        pieces = parent.data('pieces'),
                        type = parent.data('type');
                        amount = parent.data('amount');
                priceControl.val(price);
                numControl.val(num);
                piecesControl.val(pieces);
                idControl.val(id);
                amountControl.val(amount);

                if (type == '{{ cons('salesman.order.goods.type.mortgage') }}') {
                    priceControl.prop('disabled', true).closest('.form-group').addClass('hidden');
                    piecesControl.prop('disabled', true).closest('.form-group').addClass('hidden');
                } else if (type == '{{ cons('salesman.order.goods.type.return') }}') {
                    piecesControl.prop('disabled', true).closest('.form-group').addClass('hidden');
                    priceControl.prop('disabled', true).closest('.form-group').addClass('hidden');
                    amountControl.prop('disabled', false).closest('.form-group').removeClass('hidden');
                }

            }).on('hidden.bs.modal', function () {
                priceControl.prop('disabled', false).closest('.form-group').removeClass('hidden');
                piecesControl.prop('disabled', false).closest('.form-group').removeClass('hidden');
                amountControl.prop('disabled', true).closest('.form-group').addClass('hidden');
            });
        })
    </script>
@stop