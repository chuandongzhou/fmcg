@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/goods-column/index') }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="refresh" autocomplete="off">

        <div class="form-group">
            <label class="col-sm-2 control-label">地址</label>

            <div class="col-sm-3">
                <select name="province_id" class="address-province form-control">
                </select>
            </div>
            <div class="col-sm-3">
                <select name="city_id" class="address-city form-control">
                </select>
            </div>
            <div class="col-sm-2">
                <select name="district_id" class="address-district form-control hide useless-control">
                </select>
            </div>
            <div class="col-sm-2">
                <select name="street_id" class="address-street form-control hide useless-control"></select>
            </div>
        </div>

        <div class="form-group">
            <label for="id_list" class="col-sm-2 control-label">栏目</label>

            <div class="col-sm-4">
                <select name="cate_level_1" class="form-control">
                    <option value="">===请选择===</option>
                    @foreach($cates as $id => $cate)
                        <option value="{{ $id }}">{{ $cate['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <input type="button" class="btn btn-get-id" data-url="{{ url('api/v1/goods/column-id') }}" value="查询">
            </div>
        </div>

        <div class="form-group">
            <label for="id_list" class="col-sm-2 control-label">默认显示的id</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="id_list" name="id_list"
                       value=""
                       placeholder="默认显示的id">( 按 '|' 隔开，最多10条)
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">确定</button>
            </div>
        </div>
    </form>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $('.btn-get-id').on('click', function () {
            var provinceId = $('select[name="province_id"]').val(),
                    cityId = $('select[name="city_id"]').val(),
                    cateId = $('select[name="cate_level_1"]').val(),
                    url = $(this).data('url');

            if (!provinceId || !cateId) {
                alert('请选择省或默认id');
                return false;
            }
            $.get(url, {'province_id': provinceId, 'city_id': cityId, 'cate_level_1': cateId}, function (data) {
                $('input[name="id_list"]').val(data.length ? data.join('|') : '');
            }, 'json')
        })
    </script>
@stop