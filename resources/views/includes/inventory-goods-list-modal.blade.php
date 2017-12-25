<div class="modal modal1 fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:820px;height:700px">
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
                        <tbody class="goods-list" name="goods-list">

                        </tbody>
                    </table>
                </div>
                <label class="all-check">
                    <input type="checkbox" onclick="onCheckChange(this,'.goods')" id="parent"> 全部勾选</label>
            </div>
            <div class="text-center page">
                <ul class="pagination">
                </ul>
            </div>
            <div class="modal-footer middle-footer text-right">
                <button data-dismiss="modal" type="button" onclick="chooseSubmit()" class="btn btn-success">提交</button>
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
                $('input[name = nameOrCode]').val('')
                tbody.html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
                paginate(1)
            });

            inventory_search = function () {
                nameOrCode = $('input[name = nameOrCode]').val();
                paginate(1, {'nameOrCode': nameOrCode})
            };

            chooseSubmit = function () {
                tbody.find($(".goods:checked")).each(function () {
                    if ($(this).prop('disabled')) {
                        return
                    }
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

                    var cost_tips = $(this).next().next().next().next('input[name=cost-tips]').data('cost_tips');
                    var surplus_inventory = $(this).next().next().next().next().next($('input[name=surplus-iventory]')).data('surplus_inventory');

                    var tr = $('tr.inventory-template:eq(0)').clone().removeClass('modal');
                    tr.find('input[name=ids]').val($(this).val());
                    tr.find('input[name=ids]').addClass('child');
                    tr.find('img').attr('src', img_url);
                    tr.find('a').attr('href', goods_detail_url);
                    tr.find('a').html(goods_name);
                    tr.find('.datetimepicker').attr('name', name_prefix + '[production_date][0]').each(makeDate());
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
                    tr.find('select').attr('name', name_prefix + '[pieces][0]').html(option);
                    tr.find('input.cost').attr('name', name_prefix + '[cost][0]');
                    tr.find('span.cost-tips').html(cost_tips);
                    tr.find('span.surplus-inventory').html('剩余库存: ' + surplus_inventory);
                    tr.find('input.inventory').attr('name', name_prefix + '[quantity][0]');
                    tr.find('textarea').attr('name', name_prefix + '[remark][0]');
                    choosedGoodsList.append(tr);
                    $(this).parents('tr').remove()
                });
            };

            function paginate(page, data) {
                var page = page || 1,
                        nameOrCode = $('input[name = nameOrCode]').val(),
                        data = data || {'nameOrCode': nameOrCode};
                $('.page ul li').prop('disabled', true);
                $.ajax({
                    url: site.api('inventory/goods-list?page=' + page),
                    method: 'get',
                    data: data
                }).done(function (data) {
                    makeHtml(data)
                });
            }

            function makeHtml(data) {
                var allPage = parseInt(data.goods.last_page),
                        currentPage = parseInt(data.goods.current_page),
                        ids = new Array(),
                        allGoods = data.goods.data,
                        pageHtml = '';
                if (allGoods.length == 0) {
                    $('.close').click();
                    alert('您的店铺还未添加商品');
                    return false;
                }
                $('input#parent').attr('checked', false);
                //添加分页html
                //前一页
                if (currentPage == 1) {
                    pageHtml += '<li class="disabled"> <span>«</span></li>';
                } else {
                    pageHtml += '<li data-page="' + (currentPage - 1) + '" > <a>«</a></li>';
                }
                //数字分页信息
                if (allPage < 12) {
                    for (var i = 0; i < allPage; i++) {
                        if ((i + 1) == currentPage) {
                            pageHtml += '<li data-page="' + (i + 1) + '" class="active" ><span>' + (i + 1) + '</span></li>';
                        } else {
                            pageHtml += '<li data-page="' + (i + 1) + '" ><a>' + (i + 1) + '</a></li>';
                        }
                    }
                } else {
                    if (currentPage < 7) {
                        for (var i = 0; i < 8; i++) {
                            if ((i + 1) == currentPage) {
                                pageHtml += '<li data-page="' + (i + 1) + '" class="active" ><span>' + (i + 1) + '</span></li>';
                            } else {
                                pageHtml += '<li data-page="' + (i + 1) + '" ><a>' + (i + 1) + '</a></li>';
                            }
                        }
                        pageHtml += '<li class="disabled"  ><span>...</span></li>' +
                                '<li data-page="' + ( allPage - 1) + '" ><a>' + (allPage - 1) + '</a></li>' +
                                '<li data-page="' + allPage + '" ><a>' + allPage + '</a></li>';

                    } else if (currentPage >= 7 && currentPage <= (allPage - 5)) {
                        pageHtml += '<li data-page="1"><a>1</a></li>' +
                                '<li data-page="2"><a>2</a></li>' +
                                '<li class="disabled"  ><span>...</span></li>' +
                                '<li data-page="' + ( currentPage - 3) + '" ><a>' + (currentPage - 3) + '</a></li>' +
                                '<li data-page="' + ( currentPage - 2) + '" ><a>' + (currentPage - 2) + '</a></li>' +
                                '<li data-page="' + ( currentPage - 1) + '" ><a>' + (currentPage - 1) + '</a></li>' +
                                '<li data-page="' + currentPage + '" class="active" ><span>' + currentPage + '</span></li>' +
                                '<li data-page="' + ( currentPage + 1) + '" ><a>' + (currentPage + 1) + '</a></li>' +
                                '<li data-page="' + ( currentPage + 2) + '" ><a>' + (currentPage + 2) + '</a></li>' +
                                '<li data-page="' + ( currentPage + 3) + '" ><a>' + (currentPage + 3) + '</a></li>' +
                                '<li class="disabled"  ><span>...</span></li>' +
                                '<li data-page="' + ( allPage - 1) + '" ><a>' + (allPage - 1) + '</a></li>' +
                                '<li data-page="' + allPage + '" ><a>' + allPage + '</a></li>';
                    } else {
                        pageHtml += '<li data-page="1"><a>1</a></li>' +
                                '<li data-page="2"><a>2</a></li>' +
                                '<li class="disabled"  ><span>...</span></li>';
                        for (var i = 8; i >= 0; i--) {
                            if ((allPage - i) == currentPage) {
                                pageHtml += '<li data-page="' + (allPage - i) + '" class="active" ><span>' + (allPage - i) + '</span></li>';
                            } else {
                                pageHtml += '<li data-page="' + (allPage - i) + '" ><a>' + (allPage - i) + '</a></li>';
                            }
                        }

                    }

                }

                //后一页
                if (currentPage == allPage) {
                    pageHtml += '<li class="disabled"> <span>»</span></li>';
                } else {
                    pageHtml += '<li data-page="' + (currentPage + 1) + '" ><a>»</a></li>';
                }
                //添加商品html
                choosedGoodsList.find($('input[name = ids]')).each(function () {
                    ids.push($(this).val())
                });
                html = '';

                for (var i = 0; i < allGoods.length; i++) {
                    var exist = $.inArray(String(allGoods[i].id), ids);
                    html += '<tr><td>';
                    if (exist >= 0) {
                        html += '已添加'
                    } else {
                        html += '<input name="ids" class="goods" type="checkbox" value=' + allGoods[i].id + '>';
                    }
                    html += '<input name="pieces" ' +
                            'data-pieces_level_1_lang = ' + allGoods[i].goods_pieces.pieces_level_1_lang + ' ' +
                            'data-pieces_level_2_lang = ' + allGoods[i].goods_pieces.pieces_level_2_lang + ' ' +
                            'data-pieces_level_3_lang = ' + allGoods[i].goods_pieces.pieces_level_3_lang + ' ' +
                            'data-pieces_level_1 = ' + allGoods[i].goods_pieces.pieces_level_1 + ' ' +
                            'data-pieces_level_2 = ' + allGoods[i].goods_pieces.pieces_level_2 + ' ' +
                            'data-pieces_level_3 = ' + allGoods[i].goods_pieces.pieces_level_3 + ' ' +
                            'type="hidden">';
                    html += '<input name="image_url" type="hidden" data-image_url=' + allGoods[i].image_url + '>';
                    html += '<input name="name" type="hidden" data-name=' + allGoods[i].name + '>';
                    html += '<input name="cost-tips" type="hidden" data-cost_tips=' + allGoods[i].cost_tips + '>';
                    html += '<input name="surplus-inventory" type="hidden" data-surplus_inventory=' + allGoods[i].surplus_inventory + '>';
                    html += '</td>'
                    html += '<td data-name=' + allGoods[i].name + '>' + allGoods[i].name + '</td>';
                    html += '<td data-bar_code=' + allGoods[i].bar_code + '>' + allGoods[i].bar_code + '</td>'
                    html += '</tr>'
                }
                tbody.html(html);
                $('.page ul').html(pageHtml);
            }

            $('.page ul').on('click', 'li', function () {
                var obj = $(this);
                var page = obj.data('page');
                if (page) {
                    paginate(page);
                }
            });
        });
    </script>
@stop