@extends('admin.master')
@include('includes.uploader')
@include('includes.timepicker')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $advert->id ? 'put' : 'post' }}"
          action="{{ url('admin/advert-' . $type . '/' . $advert->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="referer" autocomplete="off">
        @if($type == 'category'|| $type == 'left-category')
            <div class="form-group">
                <label class="col-sm-2 control-label">地址</label>

                <div class="col-sm-3">
                    <select name="province_id" data-id="{{ $advert->province_id }}"
                            class="address-province form-control">
                    </select>
                </div>
                <div class="col-sm-3">
                    <select name="city_id" data-id="{{ $advert->city_id }}" class="address-city form-control">
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
        @endif
        @if($type == 'category' || $type == 'left-category')
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">商品分类</label>

                <div class="col-sm-4">
                    <select name="category_id" class="form-control" id="category_id">
                        <option value="">---请选择商品分类---</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $advert->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">广告名称</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入广告名"
                       value="{{ $advert->name }}">
            </div>
        </div>
        <div class="form-group">
            <label for="upload-file" class="col-sm-2 control-label">广告图片</label>

            <div class="col-sm-4">
                <span data-name="image" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件({{ $size }})
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>

                <div class="image-preview w160">
                    <img src="{{ $advert->image_url }}" class="img-thumbnail">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">排序</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="sort" name="sort" placeholder="请输入排序"
                       value="{{ $advert->sort }}">
            </div>
        </div>

        <div class="form-group">
            <label for="url" class="col-sm-2 control-label">广告URL</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="url" name="url" placeholder="请输入广告URL"
                       value="{{ $advert->url or 'http://' }}">
            </div>
            <div class="col-sm-push-2 col-sm-10">
                <p class="help-block">必须以http://开头</p>
            </div>
        </div>

        <div class="form-group" id="date-time">
            <label class="col-sm-2 control-label">起止时间</label>

            <div class="col-sm-2 time-limit">
                <input type="text" class="form-control datetimepicker" name="start_at" placeholder="起始时间"
                       value="{{ $advert->start_at }}"/>
            </div>

            <div class="col-sm-2 time-limit">
                <input type="text" class="form-control datetimepicker" name="end_at" placeholder="结束时间"
                       value="{{ $advert->end_at }}"/>
            </div>
            <div class="col-sm-push-2 col-sm-10">
                <p class="help-block">结束时间为空时，表示广告永久有效。</p>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-success">{{ $advert->id ? '修改' : '添加' }}</button>
                <a href="{{ url('admin/advert-' . $type) }}" class="btn btn-bg btn-primary">返回</a>

            </div>
        </div>
    </form>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop