@section('body')
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form address-form" action=""
                      method="" data-help-class="col-sm-push-2 col-sm-10"
                      data-done-url="{{ url('personal/delivery-area') }}" data-no-loading="true" autocomplete="off">
                    <div class="modal-header choice-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                        <div class="modal-title forgot-modal-title" id="addressModalLabel">
                            <span>添加配送区域</span>
                        </div>
                    </div>
                    <div class="modal-body address-select">
                        <div class="address-group">
                            <div class="form-group row address-panel">
                                <label class="col-sm-2 control-label" for="num">配送区域:</label>
                                <div class="col-sm-10">
                                    <select class="address address-province inline-control add-province"
                                            name="province_id">
                                        <option selected="selected" value="">请选择省市/其他...</option>
                                    </select>

                                    <select class="address address-city inline-control add-city" name="city_id">
                                        <option selected="selected" value="">请选择城市...</option>
                                    </select>

                                    <select class="address address-district inline-control add-district"
                                            name="district_id">
                                        <option selected="selected" value="">请选择区/县...</option>
                                    </select>

                                    <select class="address address-street inline-control add-street useless-control">
                                        <option selected="selected" value="">请选择街道...</option>
                                    </select>
                                    <div class="hidden address-text">
                                        <input type="hidden" name="area_name" class="area-name"
                                               value=""/>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="num">最低配送额:</label>

                                <div class="col-sm-10 col-md-5">
                                    <input type="text" name="min_money" class="form-control min-money"
                                           placeholder="请输入最低配送额"/>
                                </div>
                            </div>

                            <div class="form-group row address-detail">
                                <label class="col-sm-2 control-label" for="num">备注:</label>

                                <div class="col-sm-10 col-md-5">
                                    <input type="text" name="address" class="form-control detail-address"
                                           placeholder="请输入备注"/>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer middle-footer">
                        <button type="submit" class="btn btn-success btn-sm btn-add  pull-right" data-text="保存">保存
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js-lib')
    @parent
    <script role='reload' type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            addressSelectChange();
            var addressModal = $('#addressModal'),
                    form = addressModal.find('form'), province = $('select[name="province_id"]'),
                    city = $('select[name="city_id"]'),
                    district = $('select[name="district_id"]'),
                    street = $('.address-street');
            addressModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget);
                $('#addressModal span').html(obj.hasClass('personal-add') ? '添加配送区域' : '编辑配送区域');
                var id = obj.data('id') || '',
                        address = obj.data('address') || '',
                        minMoney = obj.data('min-money') || '',
                        provinceId = obj.data('province-id') || '',
                        cityId = obj.data('city-id') || '',
                        districtId = obj.data('district-id') || '',
                        areaName = obj.data('area-name') || '';
                province.data('id', provinceId);
                city.data('id', cityId);
                district.data('id', districtId);
                $('input[name="min_money"]').val(minMoney);
                $('input[name="area_name"]').val(areaName);
                $('input[name="address"]').val(address);
                form.attr('action', site.api(obj.hasClass('personal-add') ? 'personal/delivery-area' : 'personal/delivery-area/' + id));
                form.attr('method', obj.hasClass('personal-add') ? 'post' : 'put');

                setAddress(province, city, district, street);

            }).on('hide.bs.modal', function (e) {
                form.formValidate('reset');
            });

            @if(isset($model) && $model == 'shop')
                addAddFunc();
            @endif



        })
        ;
    </script>
@stop