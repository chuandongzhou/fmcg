<div class="form-group">
    <label class="col-sm-2 control-label" for="id">店家ID:</label>

    <div class="col-sm-10 col-md-6">
        <span class="form-control" style="border: none; box-shadow: none">{{ $shop->id }}</span>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label" for="username">店家LOGO:</label>

    <div class="col-sm-10 col-md-6">
        <button class="btn btn-primary btn-sm" data-height="100" data-width="100"
                data-target="#cropperModal" data-toggle="modal" data-name="logo" type="button">
            本地上传(100x100)
        </button>
        <div class="image-preview">
            <img class="img-thumbnail"
                 src="{{ $shop->logo_url }}">
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label" for="username">店家名称:</label>

    <div class="col-sm-10 col-md-6">
        <input class="form-control" id="name" name="name" placeholder="请输入店家名称"
               value="{{ $shop->name }}"
               type="text">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label" for="username">联系人:</label>

    <div class="col-sm-10 col-md-6">
        <input class="form-control" id="contact_person" name="contact_person" placeholder="请输入联系人"
               value="{{ $shop->contact_person }}"
               type="text">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label" for="contact_info">联系方式:</label>

    <div class="col-sm-10 col-md-6">
        <input class="form-control" id="contact_info" name="contact_info" placeholder="请输入联系方式"
               value="{{ $shop->contact_info }}"
               type="text">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label" for="min_money">最低配送额:</label>

    <div class="col-sm-10 col-md-6">
        <input class="inline-control" id="min_money" name="min_money" placeholder="请输入最低配送额"
               value="{{ $shop->min_money }}"
               type="text">元
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label" for="username">店家简介:</label>

    <div class="col-sm-10 col-md-6">
                            <textarea class="form-control" placeholder="请输入店家简介" rows="4" id="introduction"
                                      name="introduction">{{ $shop->introduction }}</textarea>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label" for="username">营业执照:</label>

    <div class="col-sm-10 col-md-6">
        @if(!$shop->license)
            <div class="progress collapse">
                <div class="progress-bar progress-bar-striped active"></div>
            </div>
            <span data-name="license" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>
        @endif
        <div class="image-preview w160">
            <img src="{{ $shop->license_url }}" class="img-thumbnail">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="username">食品流通许可证:</label>

    <div class="col-sm-10 col-md-6">
        @if(!$shop->businessLicense)
            <div class="progress collapse">
                <div class="progress-bar progress-bar-striped active"></div>
            </div>
            <span data-name="business_license" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>
        @endif
        <div class="image-preview w160">
            <img src="{{ $shop->business_license_url }}"
                 class="img-thumbnail">
        </div>
    </div>
</div>
@if($shop->user_type == cons('user.type.supplier'))
    <div class="form-group">
        <label class="col-sm-2 control-label" for="username">代理合同(可选):</label>

        <div class="col-sm-10 col-md-6">
            @if(!$shop->agencyContract)
                <div class="progress collapse">
                    <div class="progress-bar progress-bar-striped active"></div>
                </div>
                <span data-name="agency_contract" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>
            @endif
            <div class="image-preview w160">
                <img src="{{ $shop->agency_contract_url }}"
                     class="img-thumbnail">
            </div>
        </div>
    </div>
@endif
@if($shop->user_type > cons('user.type.retailer'))
    <div class="form-group">
        <label class="col-sm-2 control-label">图片</label>

        <div class="col-sm-10">
            <button data-height="400" data-width="1000" data-target="#cropperModal" data-toggle="modal"
                    data-loading-text="图片已达到最大数量" class="btn btn-primary btn-sm" type="button"
                    id="pic-upload">
                请选择图片文件(1000x400)
            </button>

            <div class="row pictures">
                <div class="hidden">
                    <input type="hidden" value="" name="images[id][]">
                    <input type="hidden" value="" name="images[path][]">
                    <input type="text" value="" name="images[name][]"
                           class="form-control input-sm">
                </div>
                @foreach($shop->images as $image)
                    <div class="col-xs-3">
                        <div class="thumbnail">
                            <button aria-label="Close" class="close" type="button"><span
                                        aria-hidden="true">×</span>
                            </button>
                            <img alt="" src="{{ $image->url  }}">
                            <input type="hidden" value="{{ $image->id }}" name="images[id][]">
                            <input type="hidden" value="{{ $image->path }}" name="images[path][]">
                            <input type="text" value="{{ $image->name }}" name="images[name][]"
                                   class="form-control input-sm">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<div class="form-group shop-address address-panel">
    <label class="col-sm-2 control-label">所在地</label>

    <div class="col-sm-3">
        <select data-group="shop" name="address[province_id]"
                data-id="{{ $shop->shopAddress ? $shop->shopAddress->province_id : '' }}"
                class="address-province form-control address">
        </select>
    </div>
    <div class="col-sm-3">
        <select data-group="shop" name="address[city_id]"
                data-id="{{  $shop->shopAddress ? $shop->shopAddress->city_id : '' }}"
                class="address-city form-control address">
        </select>
    </div>
    <div class="col-sm-2">
        <select data-group="shop" name="address[district_id]"
                data-id="{{ $shop->shopAddress ? $shop->shopAddress->district_id : '' }}"
                class="address-district form-control address">
        </select>
    </div>
    <div class="col-sm-2">
        <select data-group="shop" name="address[street_id]"
                data-id="{{ $shop->shopAddress ? $shop->shopAddress->street_id : '' }}"
                class="address-street form-control address"></select>
    </div>
    <div class="hidden address-text">
        <input type="hidden" name="address[area_name]" class="area-name"
               value="{{ $shop->shopAddress ? $shop->shopAddress->area_name : '' }}"/>
        <input type="hidden" name="x_lng" class="lng" value="{{ $shop->x_lng }}"/>
        <input type="hidden" name="y_lat" class="lat" value="{{ $shop->y_lat }}"/>
    </div>
</div>
<div class="form-group">
    <label for="address" class="col-sm-2 control-label">详细地址</label>

    <div class="col-sm-10 col-md-6">
        <input type="text" placeholder="请输入详细地址" name="address[address]" id="address"
               class="form-control"
               value="{{ $shop->shopAddress ? $shop->shopAddress->address : '' }}">

        <div data-group="shop" class="baidu-map" id="shop"
             data-lng="{{ $shop->x_lng }}"
             data-lat="{{ $shop->y_lat }}">
        </div>

    </div>

</div>
    {{--@if($shop->user->type != cons('user.type.retailer'))--}}
        {{--<div class="form-group">--}}
            {{--<label class="col-sm-2 control-label" for="username">配送区域:</label>--}}

            {{--<div class="col-sm-10 col-md-8 padding-clear">--}}
                {{--<div class="col-sm-12">--}}
                    {{--<a id="add-address" class="btn btn-default" href="javascript:" data-target="#addressModal"--}}
                       {{--data-toggle="modal" data-loading-text="地址达到最大数量">添加配送区域</a>--}}
                {{--</div>--}}
                {{--<div class="address-list col-lg-12">--}}
                    {{--<div class="hidden">--}}
                        {{--<input type="hidden" name="area[id][]" value=""/>--}}
                        {{--<input type="hidden" name="area[province_id][]" value=""/>--}}
                        {{--<input type="hidden" name="area[city_id][]" value=""/>--}}
                        {{--<input type="hidden" name="area[district_id][]" value=""/>--}}
                        {{--<input type="hidden" name="area[street_id][]" value=""/>--}}
                        {{--<input type="hidden" name="area[area_name][]" value=""/>--}}
                        {{--<input type="hidden" name="area[address][]" value=""/>--}}
                    {{--</div>--}}
                    {{--@foreach ($shop->deliveryArea as $area)--}}
                        {{--<div class="col-sm-12 fa-border show-map">{{ $area->address_name }}--}}
                            {{--<span class="fa fa-times-circle pull-right close"></span>--}}
                            {{--<input type="hidden" name="area[id][]" value="{{ $area->id }}"/>--}}
                            {{--<input type="hidden" name="area[province_id][]" value="{{ $area->province_id }}"/>--}}
                            {{--<input type="hidden" name="area[city_id][]" value="{{ $area->city_id }}"/>--}}
                            {{--<input type="hidden" name="area[district_id][]" value="{{ $area->district_id }}"/>--}}
                            {{--<input type="hidden" name="area[street_id][]" value="{{ $area->street_id }}"/>--}}
                            {{--<input type="hidden" name="area[area_name][]" value="{{ $area->area_name }}"/>--}}
                            {{--<input type="hidden" name="area[address][]" value="{{ $area->address }}"/>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--@endif--}}

@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">

        $(function () {
            picFunc();
            var baiduMap = initMap();
            addressSelectChange(true, baiduMap);
        })
    </script>
@stop