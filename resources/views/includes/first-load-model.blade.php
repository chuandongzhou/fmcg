@section('body')
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="locationrModalLabel">选择所在地<span class="extra-text"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="address-group">
                        <label class="control-label">所在地 : </label>
                        <select class="inline-control address-province  location-province" data-group="location">
                            <option selected="selected" value="">请选择省市/其他...</option>
                        </select>

                        <select class="inline-control address-city location-city" data-group="location">
                            <option selected="selected" value="">请选择城市...</option>
                        </select>

                        <select class="inline-control address-district useless-control hide" data-group="location">
                            <option selected="selected" value="">请选择区/县...</option>
                        </select>

                        <select class="inline-control address-street add-street useless-control hide"
                                data-group="location">
                            <option selected="selected" value="">请选择街道...</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm btn-location" data-text="确定">确定
                    </button>
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
        $('#locationModal').modal({
            backdrop: 'static',
            keyboard: false
        });

        $('.btn-location').on('click', function () {
            var self = $(this),
                    locationProvinceId = $('select.location-province').val(),
                    locationCityId = $('select.location-city').val();
            self.button({
                loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中...',
                failText: '请选择省或城市'
            });
            if (!locationProvinceId || !locationCityId) {
                self.button('fail');
                setTimeout(function () {
                    self.button('reset');
                }, 3000);
                return false;
            }
            setCookie('province_id', locationProvinceId);
            setCookie('city_id', locationCityId);
            window.location.reload();
        })
    </script>
@stop