@extends('index.menu-master')
@include('includes.address')
@include('includes.cropper')
@include('includes.tinymce',['full' => true])

@section('subtitle', '商品')

@section('right')
    <div class="col-sm-12 goods-editor">
        <form class="form-horizontal ajax-form" method="{{ $goods->id ? 'put' : 'post' }}"
              action="{{ url('api/v1/my-goods/'.$goods->id) }}"
              data-help-class="col-sm-push-1 col-sm-10" data-done-url="{{ url('my-goods') }}" autocomplete="off">
            <div class="row editor-panel content-wrap">
                <div class="col-sm-10 editor-wrap">
                    <div class="form-group editor-item">
                        <label for="name" class="control-label">商品名称 :</label>
                        <input type="text" name="name" value="{{ $goods->name }}" required>
                    </div>
                    <div class="form-group editor-item">
                        <p class="items-item right-item">
                            <label class="control-label">商品条形码 :</label>
                            <input value="{{ $goods->bar_code }}" name="bar_code" placeholder="输入包装上商品条形码" type="text"
                                   required>
                        </p>
                    </div>
                    <div class="form-group goods-imgs">
                        @foreach($goods->images as $image)
                            <div class="thumbnail col-xs-3">
                                <button aria-label="Close" class="close" type="button">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <img alt="" src="{{ $image->image_url }}">
                                <input type="hidden" value="{{ $image->id }}" name="images[]">

                            </div>
                        @endforeach
                    </div>
                    <div class="form-group editor-item">
                        <p class="items-item">
                            <label class="control-label">分类 :</label>
                            <select name="cate_level_1" class="categories"></select>
                            <select name="cate_level_2" class="categories"> </select>
                            <select name="cate_level_3" class="categories"></select>
                        </p>
                    </div>
                    <div class="form-group  attr editor-item">
                        @foreach($attrs as $key=>$attr)
                            <p class="items-item">
                                <label>{{ $attr['name'] }}</label>
                                <select name="attrs[{{ $attr['attr_id'] }}]" class="attrs">
                                    <option value="0">请选择</option>
                                    @foreach($attr['child'] as $child)
                                        <option value="{{ $child['attr_id'] }}" {{ isset($attrGoods[$attr['attr_id']]) && $child['attr_id'] == $attrGoods[$attr['attr_id']]['attr_id'] ? 'selected' : '' }}>{{ $child['name'] }}</option>
                                    @endforeach
                                </select>
                            </p>
                        @endforeach
                    </div>

                    <div class="form-group editor-item">
                        <p class="items-item">
                            <label class="control-label">价格 :</label>
                            <input name="price_retailer" value="{{ $goods->price_retailer }}" type="text" required>

                            <label class="control-label">单位 :</label>
                            <select name="pieces_retailer" class="pieces" data-change-class="pieces-retailer">
                                @foreach(cons()->valueLang('goods.pieces') as $key=> $piece)
                                    <option value="{{ $key }}" {{ $key == $goods->pieces_retailer ? 'selected' : '' }}>{{ $piece }}</option>
                                @endforeach
                            </select>
                        </p>
                    </div>
                    <div class="form-group editor-item">
                        <label class="control-label">最低购买数 :</label>
                        <input class="narrow" value="{{ $goods->min_num_retailer }}" name="min_num_retailer"
                               type="text"
                               required>
                            <span>
                                (<span class="pieces-retailer">{{ $goods->pieces_retailer ? cons()->valueLang('goods.pieces',$goods->pieces_retailer) : head(cons()->valueLang('goods.pieces')) }}</span>)
                            </span>
                    </div>

                    <div class="form-group editor-item">
                        <label class="control-label">规格 :</label>
                        <input name="specification_retailer" value="{{ $goods->specification_retailer }}" type="text"
                               required>
                    </div>

                    @if (auth()->user()->type == cons('user.type.supplier'))
                        <div class="form-group editor-item">
                            <p class="items-item">
                                <label class="control-label">价格(批发) :</label>
                                <input name="price_wholesaler" value="{{ $goods->price_wholesaler }}" type="text"
                                       required>

                                <label class="control-label">单位(批发) :</label>
                                <select name="pieces_wholesaler" class="pieces" data-change-class="pieces-wholesaler">
                                    @foreach(cons()->valueLang('goods.pieces') as $key=> $piece)
                                        <option value="{{ $key }}" {{ $key == $goods->pieces_wholesaler ? 'selected' : '' }}>{{ $piece }}</option>
                                    @endforeach
                                </select>
                            </p>
                        </div>
                        <div class="form-group editor-item">
                            <label class="control-label">最低购买数(批发) :</label>
                            <input class="narrow" value="{{ $goods->min_num_wholesaler }}" name="min_num_wholesaler"
                                   type="text"
                                   required>
                            <span>
                                (<span class="pieces-wholesaler">{{ $goods->pieces_wholesaler ? cons()->valueLang('goods.pieces',$goods->pieces_wholesaler) : head(cons()->valueLang('goods.pieces')) }}</span>)
                            </span>
                        </div>

                        <div class="form-group editor-item">
                            <label class="control-label">规格(批发) :</label>
                            <input name="specification_wholesaler" value="{{ $goods->specification_wholesaler }}"
                                   type="text"
                                   required>
                        </div>
                    @endif
                    <div class="form-group editor-item">
                        <p class="check-item">
                            <label class="control-label"><input name="is_new" value="1"
                                                                {{ $goods->is_new ? 'checked' : '' }}
                                                                type="checkbox"> 新品</label>
                            <label class="control-label"><input name="is_out" value="1"
                                                                {{ $goods->is_out ? 'checked' : '' }}
                                                                type="checkbox"> 缺货</label>
                            <label class="control-label"><input name="is_expire" value="1"
                                                                {{ $goods->is_expire ? 'checked' : '' }}
                                                                type="checkbox"> 即期品</label>
                        </p>

                        <p class="check-item">
                            <label class="control-label"><input name="is_back" value="1"
                                                                {{ $goods->is_back ? 'checked' : '' }} type="checkbox">
                                可换货</label>
                            <label class="control-label"><input name="is_back" value="1"
                                                                {{ $goods->is_change ? 'checked' : '' }} type="checkbox">
                                可退货</label>
                            <label class="control-label"><input name="is_promotion" value="1"
                                                                {{ $goods->is_promotion ? 'checked' : '' }} type="checkbox">
                                促销</label>
                        </p>
                    </div>

                    <div class="form-group editor-item promotions-msg {{ $goods->is_promotion ? '' : 'hide' }}">
                        <label class="control-label">促销信息 : </label>
                        <input type="text" name="promotion_info" value="{{ $goods->promotion_info }}"
                               {{ $goods->is_promotion ? '' : 'disabled' }} id="promotion_info">
                    </div>
                </div>


                <div class="col-sm-12 graphic-wrap">
                    <p><label>商品图文介绍 :</label></p>

                    <p class="graphic-txt">
                        <textarea class="introduce tinymce-editor" name="introduce">{{ $goods->introduce }}</textarea>
                    </p>
                </div>
                <div class="col-sm-12 add-address">
                    <label>商品配送区域 : </label>
                    <button class="btn btn-primary" id="add-address" type="button" data-target="#addressModal"
                            data-toggle="modal" data-loading-text="地址达到最大数量">添加地址
                    </button>
                </div>
                <div class="col-sm-8 address-list">
                    {{--<div class="hidden">--}}
                    {{--<input type="hidden" name="area[id][]" value=""/>--}}
                    {{--<input type="hidden" name="area[province_id][]" value=""/>--}}
                    {{--<input type="hidden" name="area[city_id][]" value=""/>--}}
                    {{--<input type="hidden" name="area[district_id][]" value=""/>--}}
                    {{--<input type="hidden" name="area[street_id][]" value=""/>--}}
                    {{--<input type="hidden" name="area[area_name][]" value=""/>--}}
                    {{--<input type="hidden" name="area[address][]" value=""/>--}}
                    {{--区域经纬度--}}
                    {{--<input type="hidden" name="area[blx][]" value=""/>--}}
                    {{--<input type="hidden" name="area[bly][]" value=""/>--}}
                    {{--<input type="hidden" name="area[slx][]" value=""/>--}}
                    {{--<input type="hidden" name="area[sly][]" value=""/>--}}
                    {{--</div>--}}
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

                    <div id="map"></div>
                </div>
            </div>
            <div class="col-sm-12 text-center save padding-clear">
                @if (!$goods->id)
                    <label><input type="checkbox" name="status" value="1">立即上架<span class="prompt">(勾选后保存商品会立即上架,可被购买者查看购买)</span></label>
                @endif
                <p class="save-btn">
                    <button class="btn btn-bg btn-primary" type="submit"> 保存</button>
                </p>
            </div>
        </form>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
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
        //获取分类
        getAttr();
        {{--addGoodsFunc('{{ $goods->cate_level_1 }}', '{{ $goods->cate_level_2 }}', '{{ $goods->cate_level_3 }}');--}}
        loadGoodsImages('{{ $goods->bar_code }}');
        $('.pieces').change(function () {
            var obj = $(this), changeClass = obj.data('changeClass'), pieces = obj.find("option:selected").text();
            $('.' + changeClass).html(pieces);
        })
        $('input[name="is_promotion"]').change(function () {
            var promotionInfo = $('input[name="promotion_info"]');
            $(this).is(':checked') ? promotionInfo.prop('disabled', false).parents('.promotions-msg').removeClass('hide') : promotionInfo.prop('disabled', true).parents('.promotions-msg').addClass('hide');
        });
    </script>
@stop