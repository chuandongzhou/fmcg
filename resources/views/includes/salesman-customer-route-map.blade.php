@section('css')
    @parent
    <style type="text/css">
        #customer-map {
            margin-top: 20px;;
            height: 400px;
            width: 100%;
        }
    </style>
@stop
@section('body')
    @parent
    <div class="modal fade" id="customerAddressMapModal" tabindex="-1" role="dialog"
         aria-labelledby="customerAddressMapModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="shippingAddressMapModalLabel">
                        <span>客户拜访线路图</span>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-2 col-md-3">
                            <input type="checkbox" class="shop-point marker-display" checked/> 店铺所在位置<img
                                    src="{{ asset('images/map-icon/icon_s_1.png') }}">
                        </div>
                        <div class="col-sm-2 col-md-4">
                            <input type="checkbox" class="salesman-point marker-display" checked/> 业务员提交拜访时所在位置<img
                                    src="{{ asset('images/map-icon/icon_p_1.png') }}">
                        </div>
                    </div>

                    <div id="customer-map">
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var customerAddressMapModal = $('#customerAddressMapModal');
            customerAddressMapModal.on('shown.bs.modal', function (e) {
                    var mapData = customerMapData();

                    // 百度地图API功能
                    var mp = new BMap.Map("customer-map", {enableMapClick: false}),
                        point = new BMap.Point(mapData[0]['businessLng'], mapData[0]['businessLat']);

                    mp.centerAndZoom(point, 15);
                    mp.enableScrollWheelZoom();

                    // var shopPointArray = new Array(), pointArray = new Array();

                    //mp.centerAndZoom(point, 15);

                    var shopMarkerArray = [],
                        markerArray = [];


                    for (var i = 0; i < mapData.length; i++) {
                        var txt = mapData[i]['number'] + " " + mapData[i]['name'];

                        var shopPoint = new BMap.Point(mapData[i]['businessLng'], mapData[i]['businessLat']);
                        var salesmanPoint = new BMap.Point(mapData[i]['lng'], mapData[i]['lat']);

                        /*  shopPointArray.push(shopPoint);
                         pointArray.push(point);*/
                        var ShopIconUrl = 'http://dingbaida.com/images/map-icon/icon_s_' + (i + 1) + '.png';
                        var IconUrl = 'http://dingbaida.com/images/map-icon/icon_p_' + (i + 1) + '.png';
                        var shopIcon = new BMap.Icon(ShopIconUrl, new BMap.Size(30, 60));
                        var icon = new BMap.Icon(IconUrl, new BMap.Size(30, 60));

                        var shopMarker = new BMap.Marker(shopPoint, {icon: shopIcon});  // 创建标注
                        var marker = new BMap.Marker(salesmanPoint, {icon: icon});  // 创建标注

                        shopMarkerArray.push(shopMarker);
                        markerArray.push(marker);

                        mp.addOverlay(shopMarker);              // 将标注添加到地图中
                        mp.addOverlay(marker);              // 将标注添加到地图中

                        addClickHandler(txt, shopMarker);
                        addClickHandler(txt, marker);
                    }

                    //点击事件
                    function addClickHandler(content, marker) {
                        marker.addEventListener("click", function (e) {
                                openInfo(content, e)
                            }
                        );
                    }

                    //打开消息框
                    function openInfo(content, e) {
                        var p = e.target;
                        var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
                        var infoWindow = new BMap.InfoWindow(content, {
                            width: 50,     // 信息窗口宽度
                            height: 25     // 信息窗口高度
                        });  // 创建信息窗口对象
                        mp.openInfoWindow(infoWindow, point); //开启信息窗口
                    }

                    // 显示/隐藏marker
                    function display(isChecked, markers) {
                        for (var j = 0; j < markers.length; j++) {
                            isChecked ? markers[j].show() : markers[j].hide();
                        }
                    }

                    $('.marker-display').on('change', function () {
                        var obj = $(this),
                            isChecked = obj.is(':checked'),
                            markers = obj.hasClass('shop-point') ? shopMarkerArray : markerArray;

                        display(isChecked, markers);

                    });
                }
            );
        })
    </script>
@stop