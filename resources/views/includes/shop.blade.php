<table class="table table-border table-center">
    <tr>
        <td>店家ID</td>
        <td>{{ $shop->id }}</td>
        <td>店家LOGO</td>
        <td class="text-left-important">
           <span class="image-preview">
             <img class="img-thumbnail"
                  src="{{ $shop->logo_url }}" width="100" height="100">
           </span>
            <button class="btn upload-img" data-height="100" data-width="100"
                    data-target="#cropperModal" data-toggle="modal" data-name="logo" type="button">
                本地上传(100x100)
            </button>
        </td>
    </tr>
    <tr>
        <td>店家名称</td>
        <td class="text-left-important">
            <input class="form-control" id="name" name="name" placeholder="请输入店家名称"
                   value="{{ $shop->name }}"
                   type="text">
        </td>
        <td>最低配送额</td>
        <td class="text-left-important">
            <input class="inline-control" id="min_money" name="min_money" placeholder="请输入最低配送额"
                   value="{{ $shop->min_money }}"
                   type="text">元
        </td>
    </tr>
    <tr>
        <td>联系人</td>
        <td class="text-left-important">
            <input class="form-control" id="contact_person" name="contact_person" placeholder="请输入联系人"
                   value="{{ $shop->contact_person }}"
                   type="text">
        </td>
        <td>联系方式</td>
        <td class="text-left-important">
            <input class="form-control" id="contact_info" name="contact_info" placeholder="请输入联系方式"
                   value="{{ $shop->contact_info }}"
                   type="text">
        </td>
    </tr>
    <tr>
        <td>店家简介</td>
        <td colspan="3" class="text-left-important">
             <textarea class="form-control" placeholder="请输入店家简介" rows="4" id="introduction"
                       name="introduction">{{ $shop->introduction }}</textarea>
        </td>
    </tr>
    <tr>
        <td>所在地</td>
        <td colspan="3" class="text-left-important address-panel">
            <select data-group="shop" name="address[province_id]"
                    data-id="{{ $shop->shopAddress ? $shop->shopAddress->province_id : '' }}"
                    class="address-province  address">
            </select>
            <select data-group="shop" name="address[city_id]"
                    data-id="{{  $shop->shopAddress ? $shop->shopAddress->city_id : '' }}"
                    class="address-city address">
            </select>
            <select data-group="shop" name="address[district_id]"
                    data-id="{{ $shop->shopAddress ? $shop->shopAddress->district_id : '' }}"
                    class="address-district address">
            </select>
            <select data-group="shop" name="address[street_id]"
                    data-id="{{ $shop->shopAddress ? $shop->shopAddress->street_id : '' }}"
                    class="address-street  address"></select>
            <div class="hidden address-text">
                <input type="hidden" name="address[area_name]" class="area-name"
                       value="{{ $shop->shopAddress ? $shop->shopAddress->area_name : '' }}"/>
                <input type="hidden" name="x_lng" class="lng" value="{{ $shop->x_lng }}"/>
                <input type="hidden" name="y_lat" class="lat" value="{{ $shop->y_lat }}"/>
            </div>
        </td>
    </tr>
    <tr>
        <td rowspan="2">详细地址</td>
        <td colspan="3" class="text-left-important">
            <input type="text" placeholder="请输入详细地址" name="address[address]" id="address"
                   class="form-control"
                   value="{{ $shop->shopAddress ? $shop->shopAddress->address : '' }}">
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <div data-group="shop" class="baidu-map" id="shop"
                 data-lng="{{ $shop->x_lng }}"
                 data-lat="{{ $shop->y_lat }}">
            </div>
        </td>
    </tr>
    <tr>
        <td>营业执照</td>
        <td class="text-left-important">
            <div class="preview image-preview">
                <img src="{{ $shop->license_url }}"  width="100" height="100">
                <a class="{{ !$shop->license?'hidden':'' }}  templet-modal" href="javascript:;" data-src="{{ $shop->license_url }}" data-target="#templetModal" data-toggle="modal">点击预览</a>
            </div>
            @if(!$shop->license)
                <span data-name="business_license" class="btn upload-img fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>
            @endif
        </td>
        <td>食品流通许可证</td>
        <td class="text-left-important">
            <div class="preview image-preview">
                <img src="{{ $shop->business_license_url }}"
                   width="100" height="100" >
                <a class="{{ !$shop->businessLicense?'hidden':'' }} templet-modal" href="javascript:;" data-src="{{ $shop->business_license_url }}" data-target="#templetModal" data-toggle="modal">点击预览</a>
            </div>
            @if(!$shop->businessLicense)
                <span data-name="business_license" class="btn upload-img fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>
            @endif

        </td>
    </tr>
    @if($shop->user_type == cons('user.type.supplier'))
    <tr>
        <td>代理合同</td>
        <td colspan="3" class="text-left-important">
            <div class="image-preview preview">
                <img src="{{ $shop->agency_contract_url }}" width="100" height="100"
                     >
                <a class="{{ !$shop->agencyContract?'hidden':'' }} templet-modal"  href="javascript:;" data-src="{{ $shop->agency_contract_url }}" data-target="#templetModal" data-toggle="modal">点击预览</a>
            </div>
            @if(!$shop->agencyContract)
                <span data-name="agency_contract" class="btn upload-img fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>
            @endif
        </td>
    </tr>
    @endif
</table>
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
            //选择文件显示图片预览层
            $('.fileinput-button').click(function(){
                $(this).siblings('.preview').children('a').removeClass('hidden');
            });



        })
    </script>
@stop