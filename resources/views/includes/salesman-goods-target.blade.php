@section('body')
    <div class="modal modal1 fade" id="salesmanGoodsTarget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width:800px;">
            <div class="modal-content" style="width:800px;margin:auto">
                <div class="modal-header choice-header prop-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>单品目标</span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class=" table-responsive table-wrap">
                        <table class="table-bordered table  table-target-goods table-center public-table">
                            <thead>
                            <tr align="center">
                                <th>商品名称</th>
                                <th>单位</th>
                                <th>目标数量</th>
                                <th>已完成</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script type="text/javascript">
        var modal = $('#salesmanGoodsTarget');
        modal.on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget),
                salesmanId = parent.data('id'),
                month = parent.data('month');
            $.ajax({
                url: site.api('business/salesman/' + salesmanId + '/goods-target'),
                method: 'get',
                data: {"month": month}
            }).done(function (data) {
                var goods = data.goodsTarget, html = '';

                for (var i in goods) {
                    var item = goods[i];
                    html += '<tr>';
                    html += '<td> ' + item.name + ' </td>';
                    html += '<td>' + item.pivot.pieces_name + ' </td>';
                    html += '<td> ' + item.pivot.num + '  </td>';
                    html += '<td>  ' + item.salesNum + ' </td>';
                    html += '</tr>';
                }
                $('.table-target-goods').find('tbody').html(html);

            }).fail(function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR['responseJSON']['message']);
            })
        })
    </script>

@stop