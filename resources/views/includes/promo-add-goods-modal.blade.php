<div class="modal modal1 fade" id="chooseGoods" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                    <input name="condition" type="text" class="control" placeholder="商品名称/商品条形码"/>
                    <input type="button" onclick="promoGoodsSearch()" class="control  btn btn-blue-lighter" value="搜索"/>
                    <span class="prompt">最多可选十款商品</span>
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
                <label class="all-check">
                    <input type="checkbox" onclick="onCheckChange(this,'.goods')" id="parent"> 全部勾选</label>
            </div>
            <div class="text-center page">
                <ul class="pagination">
                </ul>
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
            $('input[name = condition]').val('');
            tbody.html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
            paginate(1)

        });

        promoGoodsSearch = function () {
            paginate()
        };

        function paginate(page) {
            var page = page || 1,
                    condition = $('input[name = condition]').val(),
                    data = {'condition': condition} || '';
            $('.page ul li').prop('disabled', true);
            $.ajax({
                url: site.api('promo/goods?page=' + page),
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
            table.find($('input[name = ids]')).each(function () {
                ids.push($(this).val())
            });
            html = '';

            for (var i = 0; i < allGoods.length; i++) {
                var exist = $.inArray(String(allGoods[i].goods.id), ids);
                html += '<tr><td>';
                if (exist >= 0) {
                    html += '已添加'
                } else {
                    html += '<input name="ids" class="goods" data-pieces_1="' + allGoods[i].goods.goods_pieces.pieces_level_1 + '" data-pieces_2="' + allGoods[i].goods.goods_pieces.pieces_level_2 +
                            '"data-pieces_3="' + allGoods[i].goods.goods_pieces.pieces_level_3 +
                            '"data-pieces_1_lang="' + allGoods[i].goods.goods_pieces.pieces_level_1_lang +
                            '"data-pieces_2_lang="' + allGoods[i].goods.goods_pieces.pieces_level_2_lang +
                            '"data-pieces_3_lang="' + allGoods[i].goods.goods_pieces.pieces_level_3_lang +
                            '"data-name="' + allGoods[i].goods.name +
                            '"value=' + allGoods[i].goods.id + ' type="checkbox">';
                }
                html += '</td><td>' + allGoods[i].goods.name + '</td>';
                html += '<td>' + allGoods[i].goods.bar_code + '</td>';
                html += '</tr>';
            }
            tbody.html(html);
            $('.page ul').html(pageHtml);
        }

        chooseSubmit = function () {
            var alreadyExists = table.children('tr').length;
            var goodsChecked = tbody.find($(".goods:checked"));
            if ((goodsChecked.length) > 10) {
                alert('最多可选十款商品!')
            }
            goodsChecked.each(function (item) {
                if (item > (9 - alreadyExists)) {
                    return
                }
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
                html += '</td><td><select name="' + prefix + '[unit][]">';
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
                html += '<input type="text" name="' + prefix + '[quantity][]" class= "num" placeholder="输入数量"/>';
                html += '<input type="hidden" disabled name="ids" value=' + self.val() + ' />';
                html += '<input type="hidden" name="' + prefix + '[goods_id][]" value=' + self.val() + ' />';
                html += '</td><td><i onclick="deleteChoose(this)" class="iconfont red icon-shanchu2"></i></td></tr>';
                table.append(html);
                $(this).parents('tr').remove()
            });
        };
        $('.page ul').on('click', 'li', function () {
            var obj = $(this);
            var page = obj.data('page');
            if (page) {
                paginate(page);
            }
        });

        //删除选择的商品
        function deleteChoose(obj) {
            if (confirm('确定删除？')) {
                $(obj).parents('tr').remove()
            }
        }
    </script>
@stop
