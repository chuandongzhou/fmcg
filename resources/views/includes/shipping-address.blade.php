@section('body')
    <div class="modal fade" id="shippingAddressModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="cropperModalLabel">选择要添加的地址<span class="extra-text"></span></h4>
                </div>
                <div class="modal-body address-select">
                    <form class="form-horizontal ajax-form"
                          action="{{ url('api/v1/personal/shipping-address') }}"
                          method="post" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="card_number">收货人:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="consigner" name="consigner" placeholder="请输入收货人"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="card_holder">联系电话:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="phone" name="phone" placeholder="请输入联系电话"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">所在地</label>

                            <div class="col-sm-3">
                                <select name="province_id" class="address-province form-control address">
                                    <option selected="selected" value="">请选择省市/其他...</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <select name="city_id" class="address-city form-control address">
                                    <option selected="selected" value="">请选择城市...</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select name="district_id" class="address-district form-control address">
                                    <option selected="selected" value="">请选择区/县...</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select name="street_id"   class="address-street form-control address"></select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label">详细地址</label>

                            <div class="col-sm-10 col-md-6">
                                <input type="hidden" name="area_name" value=""/>
                                <input type="text" placeholder="请输入详细地址" name="address" id="address" class="form-control"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm btn-add" data-text="添加">添加</button>
                                <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">关闭</button>
                            </div>
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
            $('select.address').change(function () {
                var provinceControl = $('select[name="province_id"]'),
                        cityControl = $('select[name="city_id"]'),
                        districtControl = $('select[name="district_id"]'),
                        streetControl = $('select[name="street_id"]'),
                        provinceVal = provinceControl.val() ? provinceControl.find("option:selected").text() : '',
                        cityVal = cityControl.val() ? cityControl.find("option:selected").text() : '',
                        districtVal = districtControl.val() ? districtControl.find("option:selected").text() : '',
                        streetVal = streetControl.val() ? streetControl.find("option:selected").text() : '';
                $('input[name="area_name"]').val(provinceVal + cityVal + districtVal + streetVal);
            })
        })
    </script>
@stop