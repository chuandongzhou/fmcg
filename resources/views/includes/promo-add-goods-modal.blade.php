<div class="modal modal1 fade" id="chooseGoods" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:820px;height:650px">
            <div class="modal-header choice-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <div class="modal-title forgot-modal-title" id="myModalLabel">
                    <span>选择商品</span>
                </div>
            </div>
            <div class="modal-body  ">
                <div class="warehousing-control-search">
                    <input name="condition" type="text" class="control" placeholder="商品名称/商品条形码"/>
                    <input type="button" onclick="promoGoodsSearch()" class="control  btn btn-blue-lighter" value="搜索"/>
                </div>
                <table class="table table-center salesman-goods-table table-bordered public-table  margin-clear">
                    <thead>
                    <tr>
                        <th>选择</th>
                        <th>商品名称</th>
                        <th>商品条形码</th>
                    </tr>
                    </thead>
                </table>
                <div class="salesman-table-wrap warehousing-table-wrap">
                    <table class="table table-center salesman-goods-table table-bordered public-table  margin-clear">
                        <tbody class="goods-list">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer middle-footer text-right">
                <button data-dismiss="modal" onclick="chooseSubmit()" type="button" class="btn btn-success">提交</button>
            </div>
        </div>
    </div>
</div>
@section('js')
    @parent
    <script>
        var
                chooseGoodsModal = $('#chooseGoods'),
                form = chooseGoodsModal.find('form'),
                tbody = chooseGoodsModal.find($('tbody.goods-list'));

        chooseGoodsModal.on('shown.bs.modal', function (e) {
            OBJ = $(e.relatedTarget);
            table = OBJ.parent().parent().find('table>tbody');
            var ids = Array();
            table.find($('input[name = ids]')).each(function () {
             ids.push($(this).val())
             });
            $.ajax({
                url: site.api('promo/get-goods'),
                method: 'post',
                data: {'ids': ids}
            }).done(function (data) {
                makeHtml(data)
            });

        });

        promoGoodsSearch = function () {
            var condition = $('input[name = condition]').val();
            $.ajax({
                url: site.api('promo/get-goods'),
                method: 'post',
                data: {'condition': condition}
            }).done(function (data) {
                makeHtml(data)
            });
        };

        function makeHtml(goods) {
            var data = goods['promoGoods']['data'];
            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += '<tr>';
                html += '<td><input name="ids" class="child" data-pieces_1="' + data[i].goods.goods_pieces.pieces_level_1 + '" data-pieces_2="' + data[i].goods.goods_pieces.pieces_level_2 +
                        '"data-pieces_3="' + data[i].goods.goods_pieces.pieces_level_3 +
                        '"data-pieces_1_lang="' + data[i].goods.goods_pieces.pieces_level_1_lang +
                        '"data-pieces_2_lang="' + data[i].goods.goods_pieces.pieces_level_2_lang +
                        '"data-pieces_3_lang="' + data[i].goods.goods_pieces.pieces_level_3_lang +
                        '"data-name="' + data[i].goods.name +
                        '"value=' + data[i].goods.id + ' type="checkbox"></td>';
                html += '<td>' + data[i].goods.name + '</td>';
                html += '<td>' + data[i].goods.bar_code + '</td>';
                html += '</tr>';
            }
            tbody.html(html)
        }

        chooseSubmit = function () {
            tbody.find($(".child:checked")).each(function () {
                var self = $(this),
                        goodsName = self.data('name'),
                        pieces_1 = self.data('pieces_1'),
                        pieces_2 = self.data('pieces_2'),
                        pieces_3 = self.data('pieces_3'),
                        pieces_1_lang = self.data('pieces_1_lang'),
                        pieces_2_lang = self.data('pieces_2_lang'),
                        pieces_3_lang = self.data('pieces_3_lang');
                // goods_detail_url = window.location.protocol + '//' + window.location.host + '/goods/' + $(this).val();
                var prefix = OBJ.hasClass('rebate') ? 'rebate' : 'condition';
                var html = '';
                html += '<tr><td>';
                html += '<div>' + goodsName + '</div>';
                html += '</td><td><select name="'+prefix+'[unit][]">';
                option = '<option value="">请选择</option>';
                if (pieces_1 != null) {
                    option += '<option value=' + pieces_1 + '>' + pieces_1_lang + '</option>';
                }
                if (pieces_2 != null) {
                    option += '<option value=' + pieces_2 + '>' + pieces_2_lang + '</option>';
                }
                if (pieces_3 != null) {
                    option += '<option value=' + pieces_3 + '>' + pieces_3_lang + '</option>';
                }
                html += option;
                html += ' </select></td><td>';
                html += '<input type="text" name="'+prefix+'[quantity][]" class= "num" placeholder="输入数量"/>';
                html += '<input type="hidden" disabled name="ids" value='+self.val()+' />';
                html += '<input type="hidden" name="'+prefix+'[goods_id][]" value='+self.val()+' />';
                html += '</td></tr>';
                table.append(html);
                $(this).parents('tr').remove()
            });
        }
    </script>
@stop
