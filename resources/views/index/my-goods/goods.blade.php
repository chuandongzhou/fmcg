@extends('index.menu-master')
@include('includes.address')
@include('includes.cropper')
@include('includes.tinymce')

@section('subtitle', '商品')

@section('right')
    <div class="col-sm-12 goods-editor">
        <form class="form-horizontal ajax-form" method="{{ $goods->id ? 'put' : 'post' }}"
              action="{{ url('api/v1/my-goods/'.$goods->id) }}"
              data-help-class="col-sm-push-1 col-sm-10" data-done-url="{{ url('my-goods') }}">
            <div class="row editor-panel">
                <div class="col-sm-10 editor-wrap">
                    <div class="form-group editor-item">
                        <label for="name" class="control-label">名称 :</label>
                        <input type="text" name="name" value="{{ $goods->name }}" required>
                    </div>

                    <div class="form-group editor-item">
                        <label class="control-label">价格 :</label>
                        <input name="price_retailer" value="{{ $goods->price_retailer }}" type="text" required>
                        @if (auth()->user()->type == cons('user.type.supplier'))
                            <label class="control-label">价格(批发商) :</label>
                            <input name="price_wholesaler" value="{{ $goods->price_wholesaler }}" type="text" required>
                        @endif
                    </div>

                    <div class="form-group editor-item">
                        <p class="items-item">
                            <label class="control-label">分类 :</label>
                            <select name="cate_level_1">

                            </select>
                            <select name="cate_level_2">

                            </select>
                            <select name="cate_level_3">

                            </select>
                        </p>
                    </div>
                    <div class="form-group editor-item attr">
                        @foreach($attrs as $key=>$attr)
                            <p class="items-item">
                                <label>{{ $attr['name'] }}</label>
                                <select name="attrs[{{ $attr['attr_id'] }}]">
                                    <option value="0">请选择</option>
                                    @foreach($attr['child'] as $child)
                                        <option value="{{ $child['attr_id'] }}" {{ $child['attr_id'] == $attrGoods[$attr['attr_id']]['attr_id'] ? 'selected' : '' }}>{{ $child['name'] }}</option>
                                    @endforeach
                                </select>
                            </p>
                        @endforeach

                    </div>
                    <div class="form-group editor-item">
                        <label class="control-label">最底购买数 :</label>
                        <input class="narrow" value="{{ $goods->min_num_retailer }}" name="min_num_retailer" type="text"
                               required>
                        <span>(整数)</span>
                        @if (auth()->user()->type == cons('user.type.supplier'))
                            <label class="control-label">最底购买数(批发商) :</label>
                            <input class="narrow" value="{{ $goods->min_num_wholesaler }}" name="min_num_wholesaler"
                                   type="text" required>
                            <span>(整数)</span>
                        @endif

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
                    <button type="submit" class="btn btn-primary">保存</button>
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
                        <input type="hidden" name="area[area_name][]" value=""/>
                        <input type="hidden" name="area[address][]" value=""/>
                        {{--区域经纬度--}}
                        <input type="hidden" name="area[blx][]" value=""/>
                        <input type="hidden" name="area[bly][]" value=""/>
                        <input type="hidden" name="area[slx][]" value=""/>
                        <input type="hidden" name="area[sly][]" value=""/>
                    </div>
                    @foreach ($goods->deliveryArea as $area)
                        <div class="col-sm-12 fa-border">{{ $area->address_name }}
                            <span class="fa fa-times-circle pull-right close"></span>
                            <input type="hidden" name="area[id][]" value="{{ $area->id }}"/>
                            <input type="hidden" name="area[province_id][]" value="{{ $area->province_id }}"/>
                            <input type="hidden" name="area[city_id][]" value="{{ $area->city_id }}"/>
                            <input type="hidden" name="area[district_id][]" value="{{ $area->district_id }}"/>
                            <input type="hidden" name="area[street_id][]" value="{{ $area->street_id }}"/>
                            <input type="hidden" name="area[area_name][]" value="{{ $area->area_name }}"/>
                            <input type="hidden" name="area[address][]" value="{{ $area->address }}"/>
                            {{--区域经纬度--}}
                            <input type="hidden" name="area[blx][]" value="{{ $area->coordinate->bl_lng or '' }}"/>
                            <input type="hidden" name="area[bly][]" value="{{ $area->coordinate->bl_lat or '' }}"/>
                            <input type="hidden" name="area[slx][]" value="{{ $area->coordinate->sl_lng or '' }}"/>
                            <input type="hidden" name="area[sly][]" value="{{ $area->coordinate->sl_lat or '' }}"/>
                        </div>
                    @endforeach

                </div>
                <div class="col-sm-12 map">
                    <p><label>地图标识 :</label></p>

                    {{--<p class="address-map">--}}
                        {{--<img src="http://placehold.it/300x250/CDF" alt="" title=""/>--}}
                    {{--</p>--}}
                    <div id="map"></div>
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
        $(document).ready(function(){
            getCoordinateMap({!! $coordinates or '' !!});
        });

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

        var attrDiv = $('.attr');
        $('select[name="cate_level_2"] , select[name="cate_level_3"]').change(function () {
            var categoryId = $(this).val() || $('select[name="cate_level_2"]').val();

            if(categoryId > 0) {
                $.get(site.api('categories/' + categoryId + '/attrs'), {
                    category_id: categoryId,
                    format: true
                }, function (data) {
                    var html = '';
                    for (var index in data) {
                        var options = '<option value="0">请选择</option>';
                        html += '<p class="items-item">';
                        html += '<label>' + data[index]['name'] + '</label>';
                        html += ' <select name="attrs[' + data[index]['attr_id'] + ']" >';
                        for (var i in data[index]['child']) {
                            options += ' <option value="' + data[index]['child'][i]['attr_id'] + '">' + data[index]['child'][i]['name'] + '</option>'
                        }
                        html += options;
                        html += '</select>';
                        html += '</p>';
                    }
                    attrDiv.html(html).css('border', '1px solid #b4b4b4');
                }, 'json')
            }else{
                attrDiv.html('').css('border', '1px solid #fff');
            }
        });
        attrDiv.children().length == 0  ? attrDiv.css('border', '1px solid #fff') : attrDiv.css('border', '1px solid #b4b4b4');
        //促销
        $('input[name="is_promotion"]').change(function () {
            var promotionInfo = $('textarea[name="promotion_info"]');
            $(this).val() == 1 ? promotionInfo.prop('disabled', false) : promotionInfo.prop('disabled', true);
        })
    </script>
@stop