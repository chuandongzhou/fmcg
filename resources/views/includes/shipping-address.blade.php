@section('body')
    <div class="modal modal1  fade" id="shippingAddressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header prop-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="shippingAddressModalLabel">
                        <span class="header-content">添加收货地址</span>
                    </div>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal ajax-form no-prompt"
                          action="{{ url('api/v1/personal/shipping-address') }}"
                          method="post" data-help-class="col-sm-push-2 col-sm-10" data-no-loading="true" autocomplete="off">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="card_number"><span class="red">*</span>
                                收货人:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="consigner" name="consigner" placeholder="请输入收货人"
                                       value="" type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="card_holder"><span class="red">*</span>
                                联系电话:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="phone" name="phone" placeholder="请输入联系电话" value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group shop-address">
                            <label class="col-sm-2 control-label"><span class="red">*</span> 所在地</label>

                            <div class="col-sm-3">
                                <select name="province_id" class="address-province form-control address">
                                    <option value="" selected="selected">请选择省市/其他...</option>

                                </select>
                            </div>
                            <div class="col-sm-3 pd-left-clear">
                                <select name="city_id" class="address-city form-control address">
                                    <option value="" selected="selected">请选择城市...</option>
                                </select>
                            </div>
                            <div class="col-sm-2 pd-left-clear">
                                <select name="district_id" class="address-district form-control address">
                                    <option value="" selected="selected">请选择区/县...</option>
                                </select>
                            </div>
                            <div class="col-sm-2 pd-left-clear">
                                <select name="street_id" class="address-street form-control  address"
                                        style="display: none;">
                                    <option value="" selected="selected">请选择街道...</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label">详细地址</label>

                            <div class="col-sm-10 col-md-6">
                                <input type="hidden" name="area_name" value="">
                                <input type="text" placeholder="请输入详细地址" name="address" id="address"
                                       class="form-control" value="">
                                <input type="hidden" name="x_lng" value="">
                                <input type="hidden" name="y_lat" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-push-2 col-sm-10">
                                <div id="address-map"
                                     style="margin-top:20px;overflow: hidden;zoom: 1;position: relative;height: 350px;width:100%;">

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer middle-footer text-center">
                            <button type="submit" class="btn btn-success submitBtn">提交</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @parent
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {

//            //操作成功提示
//            $('.submitBtn').on('done.hct.ajax', function (data, textStatus) {
//                $('#shippingAddressModal').modal('hide');
//                $('.success-meg-content').html(data.message || '操作成功');
//                showSuccessMeg();
//                return false;
//            });
            //地址变化
            var province = $('select[name="province_id"]'),
                    city = $('select[name="city_id"]'),
                    district = $('select[name="district_id"]'),
                    street = $('select[name="street_id"]');
            $('select.address').change(function () {
                var provinceVal = province.val() ? province.find("option:selected").text() : '',
                        cityVal = city.val() ? city.find("option:selected").text() : '',
                        districtVal = district.val() ? district.find("option:selected").text() : '',
                        streetVal = street.val() ? street.find("option:selected").text() : '';
                $('input[name="area_name"]').val(provinceVal + cityVal + districtVal + streetVal);
            });
            var shippingAddressModal = $('#shippingAddressModal'),
                    form = shippingAddressModal.find('form');

            //点击添加或者编辑按钮修改数据

            shippingAddressModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget);
                $('#shippingAddressModalLabel span').html(obj.hasClass('add') ?'添加收货地址':'编辑收货地址');
                var id = obj.data('id') || '',
                        address = obj.data('address') || '',
                        provinceId = obj.data('province-id') || '',
                        cityId = obj.data('city-id') || '',
                        districtId = obj.data('district-id') || '',
                        streetId = obj.data('street-id') || '',
                        areaName = obj.data('area-name') || '',
                        x_lng = obj.data('x-lng') || 0,
                        y_lat = obj.data('y-lat') || 0,
                        consigner = obj.data('consigner') || '',
                        phone = obj.data('phone') || '';
                province.data('id', provinceId);
                city.data('id', cityId);
                district.data('id', districtId);
                street.data('id', streetId);
                $('input[name="consigner"]').val(consigner);
                $('input[name="phone"]').val(phone);
                $('input[name="address"]').val(address);
                $('input[name="area_name"]').val(areaName);
                $('input[name="x_lng"]').val(x_lng);
                $('input[name="y_lat"]').val(y_lat);
                form.attr('action', site.api(obj.hasClass('add') ?'personal/shipping-address':'personal/shipping-address/' + id));
                form.attr('method', obj.hasClass('add') ? 'post' : 'put');

                setAddress(province, city, district, street);
                getShopAddressMap(x_lng, y_lat);
            }).on('hide.bs.modal', function (e) {
                form.formValidate('reset');
            });

        })
    </script>
@stop