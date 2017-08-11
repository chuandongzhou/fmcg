<div class="modal fade" id="orderDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header choice-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <div class="modal-title forgot-modal-title" id="myModalLabel">
                    <span>查看详情</span>
                </div>
            </div>
            <div class="modal-body padding-clear">
                <div class="row order-detail">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">收货人信息</h3>
                            </div>
                            <div class="panel-container table-responsive">
                                <table class="table table-bordered table-center">
                                    <tr>
                                        <th>参与客户</th>
                                        <th>联系人</th>
                                        <th>联系电话</th>
                                        <th>收货地址</th>
                                    </tr>
                                    <tr>
                                        <td name="clint_name">佳味良品</td>
                                        <td name="contact">依恋</td>
                                        <td name="contact_information">18780369854</td>
                                        <td name="shipping_address_name">四川省成都市青羊区草市街街道哦哈哈哈</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">订单商品</h3>
                            </div>
                            <div class="panel-container table-responsive">
                                <table class="table goodsTable table-bordered table-center">

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var orderDetail = $('div#orderDetail');
            orderDetail.on('shown.bs.modal', function (parent) {
                var obj = $(parent.relatedTarget),
                        order_id = obj.data('order_id'),
                        url = site.api('promo/apply/' + order_id + '/partake-order'),
                        goodsList = '';
                client_name = $('td[name = client_name]'),
                        contact = $('td[name = contact]'),
                        contact_information = $('td[name = contact_information]'),
                        shipping_address_name = $('td[name = shipping_address_name]');
                if (order_id) {
                    $.ajax({
                        url: url,
                        method: 'post'
                    }).done(function (data) {
                        client_name.html(data.client.name);
                        contact.html(data.client.contact);
                        contact_information.html(data.client.contact_information);
                        shipping_address_name.html(data.client.shipping_address_name);
                        var orderGoods = data.order.order_goods;
                        goodsList = '<tr class="menu"> <th>商品图片</th> <th>商品名称</th> <th>商品价格</th> <th>订货数量</th> <th>金额</th></tr>';
                        for (var i = 0; i < orderGoods.length; ++i) {
                            goodsList += "<tr>";
                            goodsList += "<td><img class='store-img' src=" + orderGoods[i].goods_image + "></td>";
                            goodsList += "<td width='30%'> <div class='product-panel'><a class='product-name' href=''>" + orderGoods[i].goods_name + "</a></div></td>";
                            goodsList += "<td>" + orderGoods[i].price +'/'+ orderGoods[i].pieces_name + "</td>";
                            goodsList += "<td>" + orderGoods[i].num + "</td>";
                            goodsList += "<td>" + orderGoods[i].amount + "</td>";
                            goodsList += "</tr>";
                        }
                        goodsList += '<tr><td colspan="5" class="pay-item">商品总数:<span class="red">' + data.orderGoodsCount + '</span>&nbsp;&nbsp;&nbsp;&nbsp;总额 : <span class="red">￥' + data.orderGoodsPriceCount + '</span> </td></tr>';
                        $('.goodsTable').append(goodsList)
                    });
                }
            }).on('hidden.bs.modal', function () {
                $('.goodsTable').html('')
            });
        });
    </script>
@stop