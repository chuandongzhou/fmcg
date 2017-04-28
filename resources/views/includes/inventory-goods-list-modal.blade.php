<div class="modal modal1 fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                    <input name="nameOrCode" type="text" class="control" placeholder="商品名称/商品条形码"/>
                    <input type="button" onclick="inventory_search()" class="control  btn btn-blue-lighter" value="搜索"/>
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
                        <tbody name="goods-list">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer middle-footer text-right">
                <button data-dismiss="modal" type="button"  onclick="chooseSubmit()" class="btn btn-success">提交</button>
            </div>
        </div>
    </div>
</div>

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {

            var
                    InventoryModal = $('#myModal'),
                    form = InventoryModal.find('form'),
                    tbody = InventoryModal.find($('tbody[name = goods-list]')),
                    choosedGoodsList = $('tbody[name = choosed_goods_list]');

            InventoryModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget);
                $ids = new Array();
                choosedGoodsList.find($('input[name = ids]')).each(function () {
                    $ids.push($(this).val())
                });
                $.ajax({
                    url: site.api('inventory/get-goods'),
                    method: 'post',
                    data: {'ids': $ids}
                }).done(function (data) {
                    makeHtml(data)
                });

            });

            inventory_search = function () {

                nameOrCode = $('input[name = nameOrCode]').val();
                if (!nameOrCode) return;
                $.ajax({
                    url: site.api('inventory/get-goods'),
                    method: 'post',
                    data: {'nameOrCode': nameOrCode}
                }).done(function (data) {
                    makeHtml(data)
                });
            };

            function makeHtml(goods) {
                data = goods['goods']['data'];
                cate = goods['cate']
                html = '';
                for (var i = 0; i < data.length; i++) {
                    html += '<tr><td>';
                    html += '<input name="ids" class="child" type="checkbox" value=' + data[i].id + '>';
                    html += '<input name="pieces" ' +
                            'data-pieces_level_1_lang = ' + data[i].goods_pieces.pieces_level_1_lang + ' ' +
                            'data-pieces_level_2_lang = ' + data[i].goods_pieces.pieces_level_2_lang + ' ' +
                            'data-pieces_level_3_lang = ' + data[i].goods_pieces.pieces_level_3_lang + ' ' +
                            'data-pieces_level_1 = ' + data[i].goods_pieces.pieces_level_1 + ' ' +
                            'data-pieces_level_2 = ' + data[i].goods_pieces.pieces_level_2 + ' ' +
                            'data-pieces_level_3 = ' + data[i].goods_pieces.pieces_level_3 + ' ' +
                            'type="hidden">';
                    html += '<input name="image_url" type="hidden" data-image_url=' + data[i].image_url + '>';
                    html += '<input name="name" type="hidden" data-name=' + data[i].name + '>';
                    html += '</td>'
                    html += '<td data-name=' + data[i].name + '>' + data[i].name + '</td>';
                    html += '<td data-bar_code=' + data[i].bar_code + '>' + data[i].bar_code + '</td>'
                    html += '</tr>'
                }
                tbody.html(html)
            }

            chooseSubmit = function () {

                tbody.find($(".child:checked")).each(function () {

                    var img_url = $(this).next().next($('input[name=image_url]')).data('image_url');
                    var pieces = $(this).next($('input[name=pieces]'));
                    var pieces_level_1 = pieces.data('pieces_level_1');
                    var pieces_level_2 = pieces.data('pieces_level_2');
                    var pieces_level_3 = pieces.data('pieces_level_3');
                    var pieces_level_1_lang = pieces.data('pieces_level_1_lang');
                    var pieces_level_2_lang = pieces.data('pieces_level_2_lang');
                    var pieces_level_3_lang = pieces.data('pieces_level_3_lang');
                    var goods_name = $(this).next().next().next($('input[name=name]')).data('name');
                    var goods_detail_url = window.location.protocol + '//' + window.location.host + '/goods/' + $(this).val();
                    var name_prefix = 'goods[' + $(this).val() + ']';

                    var tr = $('tr.inventory-template:eq(0)').clone().removeClass('modal');
                    tr.find('input[name=ids]').val($(this).val());
                    tr.find('input[name=ids]').addClass('child');
                    tr.find('img').attr('src', img_url);
                    tr.find('a').attr('href', goods_detail_url);
                    tr.find('a').html(goods_name);
                    tr.find('.datetimepicker').attr('name', name_prefix + '[production_date][]').each(makeDate());
                    option = '<option>请选择</option>';
                    if (pieces_level_1 != null) {
                        option += '<option value=' + pieces_level_1 + '>' + pieces_level_1_lang + '</option>';
                    }
                    if (pieces_level_2 != null) {
                        option += '<option value=' + pieces_level_2 + '>' + pieces_level_2_lang + '</option>';
                    }
                    if (pieces_level_3 != null) {
                        option += '<option value=' + pieces_level_3 + '>' + pieces_level_3_lang + '</option>';
                    }
                    tr.find('select').attr('name', name_prefix + '[pieces][]').html(option);
                    tr.find('input.cost').attr('name', name_prefix + '[cost][]');
                    tr.find('input.inventory').attr('name', name_prefix + '[quantity][]');
                    tr.find('textarea').attr('name', name_prefix + '[remark][]');
                    choosedGoodsList.append(tr);
                    $(this).parents('tr').remove()
                });
            }

        });
    </script>
@stop