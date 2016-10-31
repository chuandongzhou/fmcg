@section('body')
    @parent
    <div class="modal fade in" id="mortgageGoodsModal" tabindex="-1" role="dialog"
         aria-labelledby="mortgageGoodsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:80%;margin:auto;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="cropperModalLabel">抵费商品<span class="extra-text"></span></h4>
                </div>
                <div class="modal-body" style="height: 500px; overflow: scroll">
                    <table class="table table-bordered table-center mortgage-goods-list">
                        <thead>
                        <tr>
                            <th></th>
                            <th>商品名称</th>
                            <th>单位</th>
                            <th>数量</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4">
                                <img src="{{ asset('images/loading.gif') }}">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消
                    </button>
                    <button type="button" class="btn btn-primary add-btn" disabled>确定</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var mortgageGoodsModal = $('#mortgageGoodsModal'),
                    mortgageGoodsList = mortgageGoodsModal.find('.mortgage-goods-list'),
                    addBtn = $('.add-btn');
            mortgageGoodsModal.on('shown.bs.modal', function (e) {
                var getUrl = site.api('business/mortgage-goods');
                $.ajax({
                    url: getUrl,
                    method: 'get'
                }).done(function (data, textStatus, jqXHR) {
                    var mortgageGoods = data['mortgageGoods'], mortgageGoodsHtml = '';
                    for (var i in mortgageGoods) {
                        var mortgage = mortgageGoods[i]
                        mortgageGoodsHtml += '<tr class="mortgage-goods-item">';
                        mortgageGoodsHtml += '<td><input type="checkbox" class="checked"></td>';
                        mortgageGoodsHtml += '<td class="goods-name">' + mortgage.goods_name + '</td>';
                        mortgageGoodsHtml += '<td class="pieces-name">' + mortgage.pieces_name + '</td>';
                        mortgageGoodsHtml += '<td><input type="text" class="goods-num" value="100" data-id="' + mortgage.id + '"></td>';
                        mortgageGoodsHtml += '</tr>';
                    }

                    mortgageGoodsList.children('tbody').html(mortgageGoodsHtml);
                    addBtn.prop('disabled', false);
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    var json = jqXHR['responseJSON'];
                    mortgageGoodsList.children('tbody').html('<tr> <td colspan="4">' + json.message + '</td> </tr>');
                });

                addBtn.on('click', function () {
                    var mortgageGoodsGroup = $('.mortgage-goods-group'), mortgageGoodsGroupHtml = ''
                    mortgageGoodsList.find('.mortgage-goods-item').each(function () {
                        var obj = $(this), goodsNumInput = obj.find('.goods-num');
                        if (obj.find('.checked').is(':checked') && parseInt(goodsNumInput.val()) > 0) {
                            var num = parseInt(goodsNumInput.val()),
                                    id = goodsNumInput.data('id'),
                                    name = obj.find('.goods-name').html(),
                                    pieces = obj.find('.pieces-name').html();

                            mortgageGoodsGroupHtml += '<li class="clearfix">';
                            mortgageGoodsGroupHtml += ' <label class="control-label col-sm-4 text-left-important">' + name + '</label>';
                            mortgageGoodsGroupHtml += ' <label class="control-label col-sm-3">' + num + pieces + '</label>';
                            mortgageGoodsGroupHtml += ' <input type="hidden" name="mortgage_goods[' + id + ']" value="' + num + '" >';
                            mortgageGoodsGroupHtml += '</li>';
                        }
                    });
                    mortgageGoodsGroup.html(mortgageGoodsGroupHtml);
                    $('.close').click();
                })


            }).on('hide.bs.modal', function (e) {
                mortgageGoodsList.children('tbody').html('<tr><td colspan="4"> <img src="{{ asset('images/loading.gif') }}"> </td></tr>');
                addBtn.prop('disabled', true).unbind('click');
            })
        })
    </script>
@stop

