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
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="customerAddressMapModalLabel">
                        <span>客户分布图</span>
                    </div>
                </div>

                <div class="modal-body">

                    <div id="customer-map">
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
        $(function () {
            var customerAddressMapModal = $('#customerAddressMapModal');
            customerAddressMapModal.on('shown.bs.modal', function (e) {
                var mapData = customerMapData();

                // 百度地图API功能
                var mp = new BMap.Map("customer-map");
                mp.centerAndZoom(new BMap.Point(mapData[0]['lng'], mapData[0]['lat']), 15);
                mp.enableScrollWheelZoom();
                // 复杂的自定义覆盖物
                function ComplexCustomOverlay(point, text, mouseoverText, href) {
                    this._point = point;
                    this._text = text;
                    this._overText = mouseoverText;
                    this._href = href;
                }

                ComplexCustomOverlay.prototype = new BMap.Overlay();
                ComplexCustomOverlay.prototype.initialize = function (map) {
                    this._map = map;
                    var div = this._div = document.createElement("div");
                    div.style.position = "absolute";
                    div.style.zIndex = BMap.Overlay.getZIndex(this._point.lat);
                    div.style.backgroundColor = "#EE5D5B";
                    div.style.border = "1px solid #BC3B3A";
                    div.style.color = "white";
                    div.style.height = "24px";
                    div.style.padding = "2px";
                    div.style.lineHeight = "18px";
                    div.style.paddingLeft = "5px";
                    div.style.paddingRight = "5px";
                    div.style.whiteSpace = "nowrap";
                    div.style.MozUserSelect = "none";
                    div.style.fontSize = "12px";
                    div.setAttribute("data-href",this._href) ;

                    var span = this._span = document.createElement("span");
                    div.appendChild(span);
                    span.appendChild(document.createTextNode(this._text));
                    var that = this;

                    var arrow = this._arrow = document.createElement("div");
                    arrow.style.background = "url({{ asset('images/map-label.png') }}) no-repeat";
                    arrow.style.position = "absolute";
                    arrow.style.width = "11px";
                    arrow.style.height = "10px";
                    arrow.style.top = "22px";
                    arrow.style.left = "10px";
                    arrow.style.overflow = "hidden";
                    div.appendChild(arrow);

                    div.onmouseover = function () {
                        this.style.backgroundColor = "#6BADCA";
                        this.style.borderColor = "#0000ff";
                        this.style.cursor = "pointer";
                        this.getElementsByTagName("span")[0].innerHTML = that._overText;
                        arrow.style.backgroundPosition = "0px -20px";
                    };

                    div.onmouseout = function () {
                        this.style.backgroundColor = "#EE5D5B";
                        this.style.borderColor = "#BC3B3A";
                        this.getElementsByTagName("span")[0].innerHTML = that._text;
                        arrow.style.backgroundPosition = "0px 0px";
                    };

                    mp.getPanes().labelPane.appendChild(div);

                    return div;
                };
                ComplexCustomOverlay.prototype.draw = function () {
                    var map = this._map;
                    var pixel = map.pointToOverlayPixel(this._point);
                    this._div.style.left = pixel.x - parseInt(this._arrow.style.left) + "px";
                    this._div.style.top = pixel.y - 30 + "px";
                };

                ComplexCustomOverlay.prototype.addEventListener = function (event, fun) {
                    this._div['on' + event] = fun;
                }

                for (var i = 0; i < mapData.length; i++) {
                    var txt = mapData[i]['number'], mouseoverTxt = txt + " " + mapData[i]['name'], href = mapData[i]['href'] || false;

                    var myCompOverlay = new ComplexCustomOverlay(new BMap.Point(mapData[i]['lng'], mapData[i]['lat']), txt, mouseoverTxt, href);

                    mp.addOverlay(myCompOverlay);

                    myCompOverlay.addEventListener('click', function () {
                        var href = $(this).data('href');
                        if (href) {
                            window.open(href);
                        }
                    });

                }
            });
        })
    </script>
@stop