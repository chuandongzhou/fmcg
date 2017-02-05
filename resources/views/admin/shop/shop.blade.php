@extends('admin.master')
@include('includes.cropper')
@include('includes.templet-model')
{{--@include('includes.address' , ['model' => 'shop'])--}}
@section('subtitle' , '用户管理')

@section('right-container')
    <div class="col-sm-12 personal-center">
        <form class="form-horizontal ajax-form" method="put"
              action="{{ url('admin/shop/'.$shop->id) }}" data-help-class="col-sm-push-2 col-sm-10"
              autocomplete="off">
            <div class="col-sm-12 user-show">
                @include('includes.shop')
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="username">推广码:</label>

                    <div class="col-sm-10 col-md-6">
                        <input class="form-control" id="spreading_code" name="spreading_code" placeholder="请输入推广码"
                               value="{{ $shop->spreading_code }}"
                               type="text">
                    </div>
                </div>
                <div class="col-sm-12 text-center save">
                    <button class="btn btn-bg btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>
                    <button class="btn btn-bg btn-warning" type="button" onclick="javascript:history.go(-1)"><i
                                class="fa fa-reply"></i> 取消
                    </button>
                </div>
            </div>
        </form>
    </div>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
@stop

