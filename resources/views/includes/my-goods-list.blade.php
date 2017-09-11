<div class="modal modal1 fade" id="chooseGoods" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
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
                    <input name="condition" type="text" class="control" placeholder="商品名称/商品条形码">
                    <input type="button" class="control  btn btn-blue-lighter" value="搜索">
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

            <div class="text-center page">
                <ul class="pagination">
                </ul>
            </div>
            <div class="modal-footer middle-footer text-right">
                <button data-dismiss="modal" type="button" class="btn btn-submit btn-success">提交</button>
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
            goodsList = chooseGoodsModal.find($('tbody.goods-list')),
            table = null;

        chooseGoodsModal.on('show.bs.modal', function (e) {
            table = $('.table-selected-goods').find('tbody');
            $('input[name = condition]').val('');
            paginate(1)

        });


        function paginate(page) {
            var page = page || 1,
                condition = $('input[name = condition]').val(),
                data = {'condition': condition} || '';

            goodsList.html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
            $('.page ul li').prop('disabled', true);
            $.ajax({
                url: site.api('my-goods/goods?page=' + page),
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
                pageHtml = '',
                html = '';
            if (allGoods.length == 0) {
                $('.close').click();
                alert('您的店铺还未添加商品');
                return false;
            }

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

            //已添加商品html
            table.find($('input.ids')).each(function () {
                ids.push($(this).val())
            });


            for (var i = 0; i < allGoods.length; i++) {
                var exist = $.inArray(String(allGoods[i].id), ids);
                html += '<tr><td>';
                if (exist >= 0) {
                    html += '<input name="ids" class="goods" type="checkbox" disabled="">'
                } else {
                    html += '<input name="ids" class="goods" data-pieces_1="' + allGoods[i].goods_pieces.pieces_level_1 + '" data-pieces_2="' + allGoods[i].goods_pieces.pieces_level_2 +
                        '"data-pieces_3="' + allGoods[i].goods_pieces.pieces_level_3 +
                        '"data-pieces_1_lang="' + allGoods[i].goods_pieces.pieces_level_1_lang +
                        '"data-pieces_2_lang="' + allGoods[i].goods_pieces.pieces_level_2_lang +
                        '"data-pieces_3_lang="' + allGoods[i].goods_pieces.pieces_level_3_lang +
                        '"data-name="' + allGoods[i].name +
                        '"value=' + allGoods[i].id + ' type="checkbox">';
                }
                html += '</td><td>' + allGoods[i].name + '</td>';
                html += '<td>' + allGoods[i].bar_code + '</td>';
                html += '</tr>';
            }
            goodsList.html(html);
            $('.page ul').html(pageHtml);
        }


        $('.page ul').on('click', 'li', function () {
            var obj = $(this);
            var page = obj.data('page');
            if (page) {
                paginate(page);
            }
        });


    </script>
@stop
