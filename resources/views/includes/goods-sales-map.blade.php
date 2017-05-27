@section('body')
    @parent
    <div class="modal fade" id="goodsSalesModal" tabindex="-1" role="dialog" aria-labelledby="goodsSalesModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <div class="modal-title" id="myModalLabel">分析图势</div>
                </div>
                <div class="modal-body">
                    <ul class="modal-list">
                        <li><span>时间 :</span> {{ $beginDay }} 至 {{ $endDay }} </li>
                        <li>
                            <span>区域 :</span>
                            <span class="address-name"><i class="fa fa-spinner fa-pulse"></i></span></li>
                        <li>
                            <span>商品ID :</span>
                            <span class="goods-id"><i class="fa fa-spinner fa-pulse"></i></span>
                        </li>
                        <li>
                            <span>商品条形码 :</span>
                            <span class="bar-code"><i class="fa fa-spinner fa-pulse"></i></span>
                        </li>
                        <li>
                            <span>商品名称 :</span>
                            <span class="goods-name" {{--style="width: 30px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"--}}>
                                <i class="fa fa-spinner fa-pulse"></i>
                            </span>
                        </li>
                    </ul>
                    <div id="myChart" class="chart"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal -->
    </div>

@stop
@section('js-lib')
    @parent
    <script src="{{ asset('js/echarts.common.min.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var goodsIdPanel = $('.goods-id'),
                barcodePanel = $('.bar-code'),
                goodsNamePanel = $('.goods-name'),
                addressAddressPanel = $('.address-name');
            $('#goodsSalesModal').on('shown.bs.modal', function (e) {
                var parent = $(e.relatedTarget),
                    id = parent.data('id'),
                    beginDay = parent.data('beginDay'),
                    endDay = parent.data('endDay'), obj = $('#myChart')[0],
                    provinceId = parent.data('provinceId'),
                    cityId = parent.data('cityId');
                $.get(site.url('admin/operation-data/goods-sales-map/' + id), {
                    begin_day: beginDay,
                    end_day: endDay
                }, function (data) {
                    var legend = ['金额'], goods = data.goods;
                    goodsIdPanel.html(goods.id);
                    barcodePanel.html(goods.bar_code);
                    goodsNamePanel.html(goods.name);
                    addressAddressPanel.html(getAddressName(provinceId, cityId));
                    echartsSet(obj, '商品销售金额统计', legend, data.dates, [
                        {
                            name: legend[0],
                            type: 'line',
                            data: data.orderGoodsList
                        }
                    ]);

                }, 'json');
            }).on('hide.bs.modal', function(){
                goodsIdPanel.html('<i class="fa fa-spinner fa-pulse"></i>');
                barcodePanel.html('<i class="fa fa-spinner fa-pulse"></i>');
                goodsNamePanel.html('<i class="fa fa-spinner fa-pulse"></i>');
            })
        })

        /**
         * 获取地址名
         * @param provinceId
         * @param cityId
         * @returns {string}
         */
        function getAddressName(provinceId, cityId) {
            var address = '';
            if (provinceId) {
                address += addressData[provinceId][0];
                if (cityId) {
                    address += addressData[cityId][0];
                }
            } else {
                address = '无';
            }
            return address
        }
    </script>
@stop
