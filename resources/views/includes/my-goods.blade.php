@section('body')
    <div class="modal modal1 fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width:800px;">
            <div class="modal-content" style="width:800px;margin:auto">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>请选择以下商品</span>
                        {{--<span>已选商品 <span--}}
                        {{--class="checked-goods-num">{{ count($shop->shopRecommendGoods) }}</span> 件</span>--}}
                    </div>
                </div>
                <div class="modal-body">
                    <div class="list-penal clearfix goods-list">

                    </div>
                    <div class="text-center page">
                        <ul class="pagination">
                        </ul>
                    </div>
                </div>
                <div class="modal-footer middle-footer text-center">
                    <button type="button" class="btn btn-success goods-submit">提交</button>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {

            var allGoodsId = "{{ $goodsId or '' }}", goodsId = [], url = '{{ $getGoodsUrl }}';
            //推荐商品ID

            if (allGoodsId) {
                goodsId = allGoodsId.split(",");
            }

            //首次加载店铺商品
            $('.my-goods').click(function () {
                getGoods(1);
            });
            //分页查询
            $('.page ul').on('click', 'li', function () {
                var obj = $(this);
                var id = obj.data('page');
                if (id) {
                    getGoods(id);
                }
            });
            //checkbox推荐商品选择

            $('.goods-list').on('click', '.choice', function () {
                var obj = $(this);

                if (obj.is(':checked')) {
                    goodsId.push(obj.data('id'));
                    $('.checked-goods-num').html(goodsId.length);
                } else {
                    if ($.inArray(obj.data('id') + '', goodsId) != -1) {
                        goodsId.splice($.inArray(obj.data('id') + '', goodsId), 1);
                    }
                    $('.checked-goods-num').html(goodsId.length);
                }
            });
            //提交推荐商品
            $('.goods-submit').click(function () {
                var obj = $(this);
                if (goodsId.length > 15) {
                    alert('最多选择15个推荐商品');
                    return false;
                }

                obj.prop('disabled', true);
                obj.html('<i class="fa fa-spinner fa-pulse"></i> 操作中...');
                $.ajax({
                    url: '{{ $setAdvertUrl }}',
                    method: 'post',
                    data: {goodsId: goodsId}
                }).done(function (data) {
                    $('.goods-submit').html('添加成功');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);

                }).fail(function (jqXHR) {
                    $('.goods-submit').prop('disabled', false);
                    alert('添加失败');
                    obj.html('提交');
                });
            });

            // 获取商品分页内容
            function getGoods(currentPage) {
                var oldHtml = $('.goods-list').html();
                $('.page ul li').prop('disabled', true);
                $('.goods-list').html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
                $.ajax({
                    url: url + '?currentPage=' + currentPage,
                    method: 'get'
                }).done(function (data) {
                    var allPage = parseInt(data.allPage),
                        currentPage = parseInt(data.currentPage),
                        html = "",
                        allGoods = data.goods,
                        pageHtml = '';
                    if (allGoods.length == 0) {
                        $('.close').click();
                        alert('您的店铺还未添加商品');
                        return false;
                    }
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
                                    pageHtml += '<li data-page="' + (i + 1) + '" class="active" ><spa>' + (i + 1) + '</spa></li>';
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
                    for (var i = 0; i < allGoods.length; i++) {
                        html += '<div class="commodity commodity-index-img ">';
                        if ($.inArray(allGoods[i]['id'] + '', goodsId) != -1) {
                            html += '<input class="choice" type="checkbox" data-id="' + allGoods[i]['id'] + '" checked>';
                        } else {
                            html += '<input class="choice" type="checkbox" data-id="' + allGoods[i]['id'] + '" >';
                        }

                        html += '<div class="img-wrap">' +
                            '       <img class="commodity-img lazy" src="' + allGoods[i]['image_url'] + '">' +
                            '   </div>' +
                            '   <div class="content-panel">' +
                            '       <a href="#">' +
                            '           <p class="commodity-name">' + allGoods[i].name + '</p>' +
                            '           <p class="sell-panel">' +
                            '              <b class="money red">¥' + allGoods[i]['price'] + '/' + allGoods[i]['pieces'] + '</b>' +
                            '           </p>' +
                            '       </a>' +
                            '   </div>' +
                            '</div>';
                    }
                    $('.goods-list').html(html);
                    $('.page ul').html(pageHtml);

                }).fail(function (jqXHR) {
                    if (!$('.goods-list').children('.img-wrap').length) {
                        $('.close').click();
                    }
                    $('.page ul li').prop('disabled', false);
                    $('.goods-list').html(oldHtml);
                    alert('获取失败');
                });
            }
        });
    </script>
@stop
