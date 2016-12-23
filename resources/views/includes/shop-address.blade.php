@section('body')
    <div class="modal modal1 fade" id="shopAddressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width:800px;">
            <div class="modal-content" style="width:800px;margin:auto">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>选择配送区域</span>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-center modal-address-table">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>区域</th>
                            <th>备注</th>
                            <th>最低配送额(元)
                            </th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($goods->shopDeliveryArea as $shopDeliveryArea)
                            <tr>
                                <td>{{ $shopDeliveryArea->id }}</td>
                                <td>{{ $shopDeliveryArea->area_name }}</td>
                                <td>{{ $shopDeliveryArea->address }}</td>
                                <td>{{ $shopDeliveryArea->min_money }}</td>
                                <td><input  name="choice-address"
                                           data-address-name="{{ $shopDeliveryArea->address_name }}"
                                           data-province-id="{{ $shopDeliveryArea->province_id }}"
                                           data-city-id="{{ $shopDeliveryArea->city_id }}"
                                           data-district-id="{{ $shopDeliveryArea->district_id }}"
                                           data-street-id="{{$shopDeliveryArea->street_id}}"
                                           data-area-name="{{ $shopDeliveryArea->area_name }}"
                                           data-address="{{ $shopDeliveryArea->address }}"
                                           data-min-money="{{ $shopDeliveryArea->min_money }}" type="checkbox"
                                     {{ in_array($shopDeliveryArea->address_name,$goods->deliveryArea->pluck('address_name')->toArray())?'checked':'' }}></td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer middle-footer text-right">
                    <button type="button" class="btn btn-success btn-add">提交</button>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script>
        $(function () {
            //删除地址
            $('.address-list').on('click', '.close-icon', function () {
                $(this).parent().fadeOut('normal', function () {
                    var self = $(this);
                    $(this).remove();
                    $('input[name="choice-address"]').each(function(){
                        var obj = $(this);
                        if(obj.data('address-name')==(self.children('input[name="area[area_name][]"]').val()+self.children('input[name="area[address][]"]').val())){
                            obj.prop('checked',false);
                        }
                    });
                });
            });
            //添加地址
            $('.btn-add').click(function () {
                var selected = $('input[name="choice-address"]');
                var addHtml = '';
                selected.each(function () {
                    var obj = $(this);

                    if (obj.is(':checked')) {
                        var addressName = obj.data('address-name'),
                                provinceId = obj.data('province-id'),
                                cityId = obj.data('city-id'),
                                districtId = obj.data('district-id'),
                                streetId = obj.data('street-id'),
                                areaName = obj.data('area-name'),
                                addressText = obj.data('address'),
                                minMoney = obj.data('min-money');
                        addHtml += '<div class="col-sm-10 fa-border show-map">' +
                                areaName + addressText +'('+minMoney+')'+
                                '<input type="hidden" name="area[id][]" value=""/>' +
                                '<input type="hidden" name="area[province_id][]" value="' + provinceId + '"/>' +
                                '<input type="hidden" name="area[city_id][]" value="' + cityId + '"/>' +
                                '<input type="hidden" name="area[district_id][]" value="' + districtId + '"/>' +
                                '<input type="hidden" name="area[street_id][]" value="' + streetId + '"/>' +
                                '<span class="fa fa-times-circle pull-right close-icon"></span>' +
                                '<input type="hidden" name="area[area_name][]" value="' + areaName + '"/>' +
                                '<input type="hidden" name="area[address][]" value="' + addressText + '"/>' +
                                '<input type="hidden" name="area[min_money][]" value="' + minMoney + '"/> ' +
                                '</div>';
                    }
                });

                $('.modal-header .close').trigger('click');
                $('.address-list ').html(addHtml);
            });

        });
    </script>
@stop
