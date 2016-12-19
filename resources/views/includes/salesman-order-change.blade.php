@section('body')
    @parent
    <div class="modal fade in" id="salesmanOrder" tabindex="-1" role="dialog" aria-labelledby="salesmanModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        {{--<span>商品编号111</span>--}}
                    </div>
                </div>
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/business/order/change') }}" method="post"
                      data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
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

            //获取商品单位
            var getGoodsPieces = function (goodsId, defaultPieces) {
                $.get(site.api('goods/goods-pieces/' + goodsId), '', function (data) {
                    var piecesName = data['piecesName'], options = '<option value="">请选择单位</option>';
                    for (var i in piecesName) {
                        if (i == defaultPieces)
                            options += '<option value="' + i + '" selected>' + piecesName[i] + '</option>';
                        else
                            options += '<option value="' + i + '">' + piecesName[i] + '</option>';
                    }
                    $('select[name="pieces"]').html(options);
                }, 'json')
            }
        })
    </script>
@stop