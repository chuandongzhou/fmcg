@extends('admin.master')
@include('includes.treetable')
@section('subtitle' , '图片管理')

@section('right-container')
    <form class="form-horizontal" action="{{ url('admin/images') }}" method="get" autocomplete="off">
        <div id="container">
            <div class="form-group">
                <div class="row col-lg-12">
                    <div class="col-sm-4">
                        <input type="text" name="bar_code" class="form-control" value="{{ $barCode }}" placeholder="请输入条形码"/>
                    </div>
                    <div class="col-sm-2">
                        <input type="submit" class="btn btn-default" value="查询">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <div class="row goods-pictures">
                        @foreach($goodsImage as $id => $image)
                            <div class="col-xs-6 col-sm-4 col-md-3">
                                <div class="thumbnail">
                                    <a aria-label="Cloa'ue" class="close btn ajax" type="button"
                                       data-url="{{ url('admin/images',[$image->id]) }}"
                                       data-method="delete">
                                        <span aria-hidden="true" type="button">×</span>
                                    </a>
                                    <img alt="" src="{{ $image->image_url }}">
                                    <label class="form-control">{{ $image->image['name'] }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {!! $goodsImage->appends($barCode ? ['bar_code'=> $barCode] : [])->render() !!}
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
    @parent
    <script>
        $(function () {
            $('#attr').treetable({expandable: true});
            getCategory(site.api('categories'));
            getAllCategory(site.api('categories'), '{{ isset($search['cate_level_1']) ? $search['cate_level_1'] :0 }}', '{{ isset($search['cate_level_2']) ? $search['cate_level_2'] :0 }}', '{{ isset($search['cate_level_3']) ? $search['cate_level_3'] :0 }}');
            getAttr();
        });
    </script>
@stop