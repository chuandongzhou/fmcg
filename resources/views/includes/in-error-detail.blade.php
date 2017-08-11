<div class="modal modal1 fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content" style="width:800px;margin:auto">
            <div class="modal-header choice-header prop-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <div class="modal-title forgot-modal-title" id="myModalLabel">
                    <span>查看</span>
                </div>
            </div>
            <div class="modal-body">
                <div class="row warh-error-modal">
                    <div class="col-sm-7 item">
                        <label>进货单号:</label>
                        <span class="order_number"></span>
                    </div>
                    <div class="col-sm-5 item">
                        <label>日期:</label>
                        <span class="date"></span>
                    </div>
                    <div class="col-sm-12 item">
                        <label>商品名称: </label>
                        <div class="content product-name">

                        </div>
                    </div>
                    <div class="col-sm-12 item">
                        <label>商品条形码: </label>
                        <div class="content bar_code">

                        </div>
                    </div>
                    <div class="col-sm-12 item">
                        <table class="table-bordered table table-center">
                            <thead>
                            <tr>
                                <th>生产日期</th>
                                <th>数量</th>
                            </tr>
                            </thead>
                            <tbody class="out-detail">

                            </tbody>
                        </table>
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

            var modal = $('.viewDetail'), error_modal = $('div.warh-error-modal');

            modal.click(function () {
                var obj = $(this),
                        orderGoods = obj.data('order_goods');
                error_modal.find('tbody.out-detail').html('');
                $.post(site.api('inventory/goods-in-error'), {'orderGoods': orderGoods}, function (data) {
                    error_modal.find('span.order_number').html(obj.parents('tr').find('td:eq(2)').html());
                    error_modal.find('span.date').html(obj.parents('tr').find('td:eq(3)').html());
                    error_modal.find('div.product-name').html(obj.parents('tr').find('td:eq(0)').html());
                    error_modal.find('div.bar_code').html(obj.parents('tr').find('td:eq(1)').html());
                    html = '';
                    for (var i = 0; i < data.inventory.length; i++) {
                        html += '<tr><td>';
                        html += data.inventory[i].production_date;
                        html += '</td><td>';
                        html += data.inventory[i].transformation_quantity;
                        html += '</td></tr>'
                    }
                    error_modal.find('tbody.out-detail').html(html)
                });

            });
        });
    </script>
@stop