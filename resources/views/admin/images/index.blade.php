@extends('admin.master')
@include('includes.treetable')
@section('subtitle' , '图片管理')

@section('right-container')
    <form class="form-horizontal" action="{{ url('admin/images') }}" method="get"
          data-help-class="col-sm-push-2 col-sm-10" data-done-url="{{ url('admin/images') }}">
        <div id="container">
            <div class="form-group">
                <div class="row col-lg-12">
                    <div class="col-sm-2">
                        <select name="cate_level_1" class="address-province form-control">

                        </select>
                    </div>
                    <div class="col-sm-2" id="level2">
                        <select name="cate_level_2" class="address-city form-control">

                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="cate_level_3" class="address-district form-control">
                            <option selected="selected" value="0">请选择</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit">搜索</button></span>
                    </div>
                </div>
            </div>

            <div class="form-group ">
                <div class="row col-lg-12 ">
                    <div class="col-sm-12 attr">
                        @foreach($attrResult as $attr)
                            <p class="items-item"><label>{{ $attr['name'] }}</label>
                                <select name="attrs[{{ $attr['attr_id'] }}]" class="attrs">
                                    <option value="0">请选择</option>
                                    @if (isset($attr['child']))
                                        @foreach($attr['child'] as $key => $val)
                                            <option value="{{ $key }}" {{ $key == $attrs[$attr['attr_id']] ? 'selected' : '' }} >{{ $val['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select></p>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <div class="row goods-pictures">
                        @foreach($goodsImage as $id => $image)
                            <div class="col-xs-6 col-sm-4 col-md-3">
                                <div class="thumbnail">
                                    <span class="cate-name">
                                        {{ $categories[$image['cate_level_1']] .($image['cate_level_2'] ? '>' . $categories[$image['cate_level_2']] : '' )  .($image['cate_level_3'] ? '>' . $categories[$image['cate_level_3']] : '' ) }}
                                    </span>
                                    <a aria-label="Close" class="close btn ajax" type="button"
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
                    {!! $goodsImage->render() !!}
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