@section('right')
    <div class="modal fade" id="shippingAddressMapModal" tabindex="-1" role="dialog"
         aria-labelledby="shippingAddressMapModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="shippingAddressMapModalLabel">收货人地址地图<span class="extra-text"></span>
                    </h4>
                </div>

                <div class="modal-body">
                    <div id="address-map"
                         style="margin-top:20px;;height: 400px;width:100%;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var shippingAddressMapModal = $('#shippingAddressMapModal');
            shippingAddressMapModal.on('shown.bs.modal', function (e) {
                getShopAddressMap({!!  isset($order->shippingAddress)? $order->shippingAddress->x_lng : 0  !!}, {!! isset($order->shippingAddress)? $order->shippingAddress->y_lat : 0  !!});
            });
        })
    </script>
@stop