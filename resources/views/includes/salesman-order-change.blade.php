@section('body')
    @parent
    <div class="modal fade in" id="salesmanOrder" tabindex="-1" role="dialog" aria-labelledby="salesmanModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>商品修改</span>
                    </div>
                </div>
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/business/order/change') }}" method="post"
                      data-help-class="col-sm-push-2 col-sm-10" data-no-loading="true" autocomplete="off">
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="id"/>
                    <div class="modal-body ">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="account"> 单价:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="price" name="price" placeholder="请输入单价"
                                       type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="password"> 数量:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="num" name="num" placeholder="请输入数量"
                                >
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
                                <select name="pieces" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10 col-md-6">
                                <button type="submit" class="btn btn-submit btn-success">提交</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="giftModal" tabindex="-1" role="dialog" aria-labelledby="giftModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>赠品修改</span>
                    </div>
                </div>
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/business/order/gift') }}" method="put"
                      data-help-class="col-sm-push-2 col-sm-10" data-no-loading="true" autocomplete="off">
                    <div class="modal-body ">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="num"> 数量:</label>

                            <div class="col-sm-10 col-md-4">
                                <input class="form-control" id="num" name="num" placeholder="请输入数量">
                                <input type="hidden" name="order_id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="num"> 单位:</label>

                            <div class="col-sm-10 col-md-4">
                                <select class="form-control" name="pieces">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit btn-success">提交</button>
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
                    type = parent.data('type'),
                    amount = parent.data('amount'),
                    goodsId = parent.data('goodsId');
                getGoodsPieces(goodsId, pieces);
                priceControl.val(price);
                numControl.val(num);
                idControl.val(id);
                amountControl.val(amount);

                if (type == '{{ cons('salesman.order.goods.type.return') }}') {
                    priceControl.prop('disabled', true).closest('.form-group').addClass('hidden');
                    amountControl.prop('disabled', false).closest('.form-group').removeClass('hidden');
                }

            }).on('hidden.bs.modal', function () {
                priceControl.prop('disabled', false).closest('.form-group').removeClass('hidden');
                piecesControl.prop('disabled', false).closest('.form-group').removeClass('hidden');
                amountControl.prop('disabled', true).closest('.form-group').addClass('hidden');
            });

            $('#giftModal').on('show.bs.modal', function (e) {
                var obj = $(this),
                    parent = $(e.relatedTarget),
                    id = parent.data('id'),
                    orderId = parent.data('orderId'),
                    piecesList = parent.data('piecesList'),
                    pieces = parent.data('pieces'),
                    numPanel = obj.find('input[name="num"]');
                obj.find('input[name="order_id"]').val(orderId);
                url = site.api('business/order/gift/') + id;
                obj.find('.btn-submit').data('url', url);
                numPanel.val(parent.data('num'));

                getGoodsPieces(id, pieces);
                obj.find('.btn-submit').on('click', function () {
                    if (!parseInt(numPanel.val()) || parseInt(numPanel.val()) <= 0) {
                        var obj = $(this);
                        obj.html('请正确填写数量');
                        setTimeout(function () {
                            obj.html('提交');
                        }, 3000);
                        return false;
                    }
                })
            })

        })
    </script>
@stop