@section('body')
    @parent
    <div class="modal modal1 fade" id="mortgageGoodsModal" tabindex="-1" role="dialog" aria-labelledby="mortgageGoodsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width:820px;">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="mortgageGoodsModalLabel">
                        <span>设置抵费商品</span>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-center salesman-goods-table table-bordered public-table  margin-clear">
                        <thead>
                        <tr>
                            <th >选择</th>
                            <th >商品名称</th>
                            <th >单位</th>
                            <th >数量</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="salesman-table-wrap">
                        <table class="table table-center salesman-goods-table table-bordered public-table  margin-clear mortgage-goods-list">
                            <tbody >
                            <tr>
                                <td colspan="4">
                                    <img src="{{ asset('images/loading.gif') }}">
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer middle-footer text-right">
                    <button type="button" class="btn btn-success add-btn">提交</button>
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
                    mortgageGoodsList =$('.mortgage-goods-list'),
                    addBtn = $('.add-btn');
            mortgageGoodsModal.on('shown.bs.modal', function (e) {
                $.ajax({
                    url: '{{ $url }}',
                    method: 'get'
                }).done(function (data, textStatus, jqXHR) {
                    var mortgageGoodsIds = new Array();
                    $('.mortgage-goods-group').children('input').each(function () {
                        mortgageGoodsIds.push($(this).data('id') + '');
                    });
                    var mortgageGoods = data['mortgageGoods'], mortgageGoodsHtml = '';
                    for (var i in mortgageGoods) {
                        var mortgage = mortgageGoods[i]
                        mortgageGoodsHtml += '<tr class="mortgage-goods-item">';
                        if ($.inArray(mortgage.id + '', mortgageGoodsIds) != -1) {
                            mortgageGoodsHtml += '<td><input type="checkbox" class="checked" checked></td>';
                        } else {
                            mortgageGoodsHtml += '<td><input type="checkbox" class="checked"></td>';
                        }
                        mortgageGoodsHtml += '<td class="goods-name">' + mortgage.goods_name + '</td>';
                        mortgageGoodsHtml += '<td class="pieces-name">' + mortgage.pieces_name + '</td>';
                        var num = $.inArray(mortgage.id + '', mortgageGoodsIds) != -1 ? $('input[data-id="' + mortgage.id + '"]').val() : '';
                        mortgageGoodsHtml += '<td><input type="text" placeholder="请填写数量" class="goods-num" value="' + num + '" data-id="' + mortgage.id + '"></td>';
                        mortgageGoodsHtml += '</tr>';
                    }
                    mortgageGoodsList.children('tbody').html(mortgageGoodsHtml);
                    addBtn.prop('disabled', false);
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    var json = jqXHR['responseJSON'];
                    mortgageGoodsList.children('tbody').html('<tr> <td colspan="4">' + json.message + '</td> </tr>');
                });

                addBtn.on('click', function () {

                    var mortgageGoodsGroup = $('.mortgage-goods-group'), mortgageGoodsGroupHtml = '', mortgageGoodsNum = 0;
                    mortgageGoodsList.find('.mortgage-goods-item').each(function () {
                        var obj = $(this), goodsNumInput = obj.find('.goods-num');
                        if (obj.find('.checked').is(':checked') && parseInt(goodsNumInput.val()) > 0) {
                            mortgageGoodsNum++;
                            var num = parseInt(goodsNumInput.val()),
                                    id = goodsNumInput.data('id'),
                                    name = obj.find('.goods-name').html(),
                                    pieces = obj.find('.pieces-name').html();
                            mortgageGoodsGroupHtml += ' <input data-id="' + id + '" type="hidden" name="mortgage_goods[' + id + ']" value="' + num + '" >';
                        }
                    });
                    mortgageGoodsGroup.html(mortgageGoodsGroupHtml);
                    $('.mortgage-goods-num').html(mortgageGoodsNum);
                    $('.close').click();
                })


            }).on('hide.bs.modal', function (e) {
                mortgageGoodsList.children('tbody').html('<tr><td colspan="4"> <img src="{{ asset('images/loading.gif') }}"> </td></tr>');
                addBtn.prop('disabled', true).unbind('click');
            })
        })
    </script>
@stop

