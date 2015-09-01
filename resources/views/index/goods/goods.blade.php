@extends('index.manage-left')
@include('includes.cropper')
@include('includes.address')
@include('includes.tinymce')

@section('subtitle', '商品')

@section('right')
    <div class="col-sm-10  goods-editor">
        <form class="form-horizontal ajax-form" method="{{ $goods->id ? 'put' : 'post' }}"
              action="{{ url('api/v1/goods/'.$goods->id) }}"
              data-help-class="col-sm-push-1 col-sm-10" data-done-url="{{ url('goods') }}">
            <div class="row editor-panel">
                <div class="col-sm-10 editor-wrap">
                    <div class="form-group editor-item">
                        <label for="name" class="control-label">名称 :</label>
                        <input type="text" name="name" value="{{ $goods->name }}" required>
                    </div>

                    <div class="form-group editor-item">
                        <label class="control-label">价格 :</label>
                        <input name="price" value="{{ $goods->price }}" type="text" required>
                    </div>

                    <div class="form-group editor-item">
                        <p class="items-item">
                            <label class="control-label">分类 :</label>
                            <select name="cate_level_1" class="narrow">

                            </select>
                            <select name="cate_level_2" class="narrow">

                            </select>
                            <select name="cate_level_3" class="narrow">

                            </select>
                        </p>
                    </div>
                    <div class="form-group editor-item">
                        <label class="control-label">标签 :</label>

                        <p class="items-item brand-msg">
                            @foreach($attrs as $key=>$attr)
                                <label>{{ $attr['name'] }}</label>
                                <select name="attrs[{{ $attr['id'] }}]" class="narrow">
                                    <option value="0">请选择</option>
                                    @foreach($attr['child'] as $child)
                                        <option value="{{ $child['id'] }}" {{ $child['id'] == $goods->attr[$key]->id ? 'selected' : '' }}>{{ $child['name'] }}</option>
                                    @endforeach
                                </select>
                            @endforeach
                        </p>
                    </div>

                    <div class="form-group editor-item">
                        <label class="control-label">最底购买数 :</label>
                        <input class="narrow" value="{{ $goods->min_num }}" name="min_num" type="text" required>
                        <span>(整数)</span>
                    </div>

                    <div class="form-group editor-item">
                        <p class="items-item">
                            <label class="control-label">是否新货 :</label>
                            <label class="checks"><input name="is_new" value="1" checked type="radio">是</label>
                            <label class="checks"><input name="is_new" value="0"
                                                         {{ $goods->is_new ? '' : 'checked' }} type="radio">否</label>
                        </p>

                        <p class="items-item right-item">
                            <label class="control-label">是否缺货 :</label>
                            <label class="checks"><input name="is_out" value="1" checked type="radio">是</label>
                            <label class="checks"><input name="is_out" value="0"
                                                         {{ $goods->is_out ? '' : 'checked' }} type="radio">否</label>
                        </p>
                    </div>

                    <div class="form-group editor-item">
                        <p class="items-item">
                            <label class="control-label">退换货　:</label>
                            <label class="checks"><input name="is_back" value="1"
                                                         {{ $goods->is_back ? 'checked' : '' }} type="checkbox">可退货</label>
                            <label class="checks"><input name="is_change" value="1"
                                                         {{ $goods->is_change ? 'checked' : '' }} type="checkbox">可换货</label>
                        </p>

                        <p class="items-item right-item">
                            <label class="control-label">是否促销 :</label>
                            <label class="checks"><input name="is_promotion" value="1" checked type="radio">是</label>
                            <label class="checks"><input name="is_promotion" value="0"
                                                         {{ $goods->is_promotion ? '' : 'checked' }} type="radio">否</label>
                        </p>
                    </div>
                    <div class="form-group editor-item">
                        <p class="items-item">
                            <label class="control-label">是否即将过期 :</label>
                            <label class="checks"><input name="is_expire" value="1"
                                                         {{ $goods->is_expire ? 'checked' : '' }} type="radio">是</label>
                            <label class="checks"><input name="is_expire" checked value="0"
                                                         {{ $goods->is_expire ? '' : 'checked' }} type="radio">否</label>
                        </p>
                    </div>

                    <div class="form-group editor-item promotions-msg">
                        <label class="control-label">促销信息 :</label>
                        <textarea name="promotion_info"
                                  {{ $goods->is_promotion ? '' : 'disabled' }} id="promotion_info">{{ $goods->promotion_info }}</textarea>
                    </div>
                </div>
                <div class="col-sm-2 right-save">
                    <input type="submit" class="btn btn-primary" value="保存">
                </div>

            </div>
            <div class="row content-wrap">
                <div class="col-sm-12 add-address">
                    <label>商品配送区域 : </label>
                    <button class="btn btn-primary" id="add-address" type="button" data-target="#addressModal"
                            data-toggle="modal" data-loading-text="地址达到最大数量">添加地址
                    </button>
                    (最多5条配送区域)
                </div>
                <div class="col-sm-8 address-list">
                    <div class="hidden">
                        <input type="hidden" name="area[id][]" value=""/>
                        <input type="hidden" name="area[province_id][]" value=""/>
                        <input type="hidden" name="area[city_id][]" value=""/>
                        <input type="hidden" name="area[district_id][]" value=""/>
                        <input type="hidden" name="area[street_id][]" value=""/>
                        <input type="hidden" name="area[detail_address][]" value=""/>
                    </div>
                    @foreach ($goods->deliveryArea as $area)
                        <div class="col-sm-12 fa-border">{{ $area->detail_address }}
                            <span class="fa fa-times-circle pull-right close"></span>
                            <input type="hidden" name="area[id][]" value="{{ $area->id }}"/>
                            <input type="hidden" name="area[province_id][]" value="{{ $area->province_id }}"/>
                            <input type="hidden" name="area[city_id][]" value="{{ $area->city_id }}"/>
                            <input type="hidden" name="area[district_id][]" value="{{ $area->district_id }}"/>
                            <input type="hidden" name="area[street_id][]" value="{{ $area->street_id }}"/>
                            <input type="hidden" name="area[detail_address][]" value="{{ $area->detail_address }}"/>
                        </div>
                    @endforeach

                </div>
                <div class="col-sm-12 map">
                    <p><label>地图标识 :</label></p>

                    <p class="address-map">
                        <img src="http://placehold.it/300x250/CDF" alt="" title=""/>
                    </p>
                </div>
                <div class="col-sm-12 map">
                    <label>商品图片 :</label>
                    <button id="pic-upload" type="button" class="btn btn-primary" data-loading-text="图片已达到最大数量"
                            data-toggle="modal" data-target="#cropperModal" data-width="200" data-height="200">
                        请选择图片文件(200x200)
                    </button>

                    <div class="col-sm-10">
                        <div class="row pictures">
                            <div class="hidden">
                                <input type="hidden" name="images[id][]" value="">
                                <input type="hidden" name="images[path][]" value="">
                                <input type="text" class="form-control input-sm" name="images[name][]" value="">
                            </div>
                            @foreach($goods->images as $image)
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
                <div class="col-sm-12 graphic-wrap">
                    <p><label>商品图文介绍 :</label></p>

                    <p class="graphic-txt">
                        <textarea class="introduce tinymce-editor" name="introduce">{{ $goods->introduce }}</textarea>
                    </p>
                </div>
            </div>
        </form>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">

        //上传图片处理
        picFunc();
        //获取下级分类
        getCategory(site.api('categories'));
        //页面加载时获取所有分类
        getAllCategory(
                site.api('categories'),
                '{{ $goods->cate_level_1 }}',
                '{{ $goods->cate_level_2 }}',
                '{{ $goods->cate_level_3 }}'
        );

        //获取标签
        $('select[name="cate_level_1"]').change(function () {
            $('p.brand-msg').html('');
        });

        $('select[name="cate_level_2"] , select[name="cate_level_3"]').change(function () {
            var categoryId = $(this).val() || $('select[name="cate_level_2"]').val();

            $.get(site.api('categories/' + categoryId + '/attrs'), {
                category_id: categoryId,
                format: true
            }, function (data) {
                var html = '';
                for (var index in data) {
                    var options = '<option value="0">请选择</option>';
                    html += '<label>' + data[index]['name'] + '</label>';
                    html += ' <select name="attrs[' + data[index]['id'] + ']" class="narrow">';
                    for (var i in data[index]['child']) {
                        options += ' <option value="' + data[index]['child'][i]['id'] + '">' + data[index]['child'][i]['name'] + '</option>'
                    }
                    html += options;
                    html += '</select>'
                }
                $('p.brand-msg').html(html);
            }, 'json')
        })
        //促销
        $('input[name="is_promotion"]').change(function () {
            var promotionInfo = $('textarea[name="promotion_info"]');
            $(this).val() == 1 ? promotionInfo.prop('disabled', false) : promotionInfo.prop('disabled', true);
        })
    </script>
@stop