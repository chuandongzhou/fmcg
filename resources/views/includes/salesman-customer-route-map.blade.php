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
@section('right')
    <div class="modal fade" id="customerAddressMapModal" tabindex="-1" role="dialog"
         aria-labelledby="customerAddressMapModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="shippingAddressMapModalLabel">客户分布图<span class="extra-text"></span>
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row">

                        <div class="col-sm-2 col-md-3">
                            <img src="{{ asset('images/icon_shop.png') }}"> 店铺所在位置
                        </div>
                        <div class="col-sm-2 col-md-4">
                            <img src="{{ asset('images/icon_pope.png') }}"> 业务员提交拜访时所在位置
                        </div>
                    </div>

                    <div id="customer-map">
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
            var customerAddressMapModal = $('#customerAddressMapModal');
            customerAddressMapModal.on('shown.bs.modal', function (e) {
                        var mapData = customerMapData();

                        // 百度地图API功能
                        var mp = new BMap.Map("customer-map", {enableMapClick: false}),
                                point = new BMap.Point(mapData[0]['businessLng'], mapData[0]['businessLat']);

                        mp.centerAndZoom(point, 15);
                        mp.enableScrollWheelZoom();

                        var shopPointArray = new Array(), pointArray = new Array();

                        for (var i = 0; i < mapData.length; i++) {
                            var txt = mapData[i]['number'] + " " + mapData[i]['name'], href = mapData[i]['href'] || false;

                            var shopPoint = new BMap.Point(mapData[i]['businessLng'], mapData[i]['businessLat']);
                            var point = new BMap.Point(mapData[i]['lng'], mapData[i]['lat']);

                            shopPointArray.push(shopPoint);
                            pointArray.push(point);

                            var shopIcon = new BMap.Icon("{{ asset('images/icon_shop.png') }}", new BMap.Size(30, 60));
                            var icon = new BMap.Icon("{{ asset('images/icon_pope.png') }}", new BMap.Size(30, 60));

                            var shopMarker = new BMap.Marker(shopPoint, {icon: shopIcon});  // 创建标注
                            var marker = new BMap.Marker(point, {icon: icon});  // 创建标注
                            mp.addOverlay(shopMarker);              // 将标注添加到地图中
                            mp.addOverlay(marker);              // 将标注添加到地图中

                            //mp.centerAndZoom(point, 15);
                            var opts = {
                                width: 50,     // 信息窗口宽度
                                height: 25     // 信息窗口高度
                            };
                            var infoWindow = new BMap.InfoWindow(txt, opts);  // 创建信息窗口对象
                            shopMarker.addEventListener("click", function () {
                                mp.openInfoWindow(infoWindow, shopPoint); //开启信息窗口
                            });
                            marker.addEventListener("click", function () {
                                mp.openInfoWindow(infoWindow, point); //开启信息窗口
                            });

                        }

                        var shopPolyline = new BMap.Polyline(shopPointArray, {
                            strokeColor: "red",
                            strokeWeight: 1,
                            strokeOpacity: 0.8
                        });
                        var polyline = new BMap.Polyline(pointArray, {
                            strokeColor: "blue",
                            strokeWeight: 1,
                            strokeOpacity: 0.8
                        });
                        console.log(pointArray);
                        mp.addOverlay(shopPolyline);
                        mp.addOverlay(polyline);

                        addArrow(shopPolyline, 2, Math.PI / 7, 'red');
                        addArrow(polyline, 2, Math.PI / 7, 'blue');

                        function addArrow(polyline, length, angleValue, color) { //绘制箭头的函数
                            var linePoint = polyline.getPath();//线的坐标串
                            var arrowCount = linePoint.length;
                            for (var i = 1; i < arrowCount; i++) { //在拐点处绘制箭头
                                var pixelStart = mp.pointToPixel(linePoint[i - 1]);
                                var pixelEnd = mp.pointToPixel(linePoint[i]);
                                var angle = angleValue;//箭头和主线的夹角
                                var r = length; // r/Math.sin(angle)代表箭头长度
                                var delta = 0; //主线斜率，垂直时无斜率
                                var param = 0; //代码简洁考虑
                                var pixelTemX, pixelTemY;//临时点坐标
                                var pixelX, pixelY, pixelX1, pixelY1;//箭头两个点
                                if (pixelEnd.x - pixelStart.x == 0) { //斜率不存在是时
                                    pixelTemX = pixelEnd.x;
                                    if (pixelEnd.y > pixelStart.y) {
                                        pixelTemY = pixelEnd.y - r;
                                    }
                                    else {
                                        pixelTemY = pixelEnd.y + r;
                                    }
                                    //已知直角三角形两个点坐标及其中一个角，求另外一个点坐标算法
                                    pixelX = pixelTemX - r * Math.tan(angle);
                                    pixelX1 = pixelTemX + r * Math.tan(angle);
                                    pixelY = pixelY1 = pixelTemY;
                                }
                                else  //斜率存在时
                                {
                                    delta = (pixelEnd.y - pixelStart.y) / (pixelEnd.x - pixelStart.x);
                                    param = Math.sqrt(delta * delta + 1);

                                    if ((pixelEnd.x - pixelStart.x) < 0) //第二、三象限
                                    {
                                        pixelTemX = pixelEnd.x + r / param;
                                        pixelTemY = pixelEnd.y + delta * r / param;
                                    }
                                    else//第一、四象限
                                    {
                                        pixelTemX = pixelEnd.x - r / param;
                                        pixelTemY = pixelEnd.y - delta * r / param;
                                    }
                                    //已知直角三角形两个点坐标及其中一个角，求另外一个点坐标算法
                                    pixelX = pixelTemX + Math.tan(angle) * r * delta / param;
                                    pixelY = pixelTemY - Math.tan(angle) * r / param;

                                    pixelX1 = pixelTemX - Math.tan(angle) * r * delta / param;
                                    pixelY1 = pixelTemY + Math.tan(angle) * r / param;
                                }

                                var pointArrow = mp.pixelToPoint(new BMap.Pixel(pixelX, pixelY));
                                var pointArrow1 = mp.pixelToPoint(new BMap.Pixel(pixelX1, pixelY1));
                                var Arrow = new BMap.Polyline([
                                    pointArrow,
                                    linePoint[i],
                                    pointArrow1
                                ], {strokeColor: color, strokeWeight: 1, strokeOpacity: 0.8});
                                mp.addOverlay(Arrow);
                            }
                        }

                    }
            );
        })
    </script>
@stop