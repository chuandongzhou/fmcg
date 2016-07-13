@extends('index.menu-master')
@section('subtitle', '个人中心-配送人员')

@section('right')
    <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/delivery-area/' . $area->id) }}"
          method="{{ $area->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('personal/delivery-area') }}" autocomplete="off">

        <div class="form-group row address-panel">
            <label class="col-sm-2 control-label" for="num">配送区域:</label>
            <div class="col-sm-10">
                <select class="address-province inline-control address" name="province_id" data-id="{{ $area->province_id  }}">
                    <option value="">请选择省市/其他...</option>
                </select>

                <select class="address-city inline-control address" name="city_id" data-id="{{ $area->city_id }}">
                    <option value="">请选择城市...</option>
                </select>

                <select class="address-district inline-control address" name="district_id" data-id="{{ $area->district_id }}">
                    <option value="">请选择区/县...</option>
                </select>

                <select class="address-street inline-control useless-control">
                    <option value="">请选择街道...</option>
                </select>
            </div>
            <div class="hidden address-text">
                <input type="hidden" name="area_name" class="area-name"
                       value="{{ $area->area_name }}"/>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 control-label" for="num">最低配送额:</label>

            <div class="col-sm-10 col-md-5">
                <input type="text" name="min_money" class="form-control"
                       placeholder="请输入最低配送额" value="{{ $area->min_money }}"/>
            </div>
        </div>

        <div class="form-group row address-detail">
            <label class="col-sm-2 control-label" for="num">备注:</label>

            <div class="col-sm-10 col-md-5">
                <input type="text" name="address" class="form-control"
                       placeholder="请输入备注" value="{{ $area->address }}"/>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-push-2 col-sm-10">
                <button class="btn btn-primary" type="submit">提交</button>
                <a href="javascript:history.go(-1)" class="btn btn-cancel">取消</a>
            </div>
        </div>
    </form>
    @parent
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        addressSelectChange();
    </script>
@stop

