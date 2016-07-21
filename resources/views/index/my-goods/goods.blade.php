@extends('index.menu-master')
@include('includes.address')
@include('includes.cropper')
@include('includes.tinymce',['full' => true])

@section('subtitle', '商品')
@section('top-title')
    <a href="{{ url('my-goods') }}">商品管理</a> &rarr;
    <a href="{{ url('my-goods') }}">我的商品</a> &rarr;
    {{ $goods->id ? '编辑' : '新增' }}商品
@stop

@section('right')
    <div class="row">
     <div class="col-sm-12 goods-editor">
        <form class="form-horizontal ajax-form" method="{{ $goods->id ? 'put' : 'post' }}"
              action="{{ url('api/v1/my-goods/'.$goods->id) }}"
              data-help-class="col-sm-push-1 col-sm-10" data-done-then="referer"
              autocomplete="off">
            <div class="row editor-panel content-wrap">
                <div class="col-sm-12 editor-wrap">
                    <div class="form-group editor-item">
                        <label for="name" class="control-label col-sm-2">商品名称 :</label>

                        <div class="col-sm-6">
                            <input type="text" name="name" class="form-control" value="{{ $goods->name }}"
                                   placeholder="请输入商品名称">
                        </div>

                    </div>
                    <div class="form-group editor-item">
                        <label class="control-label col-sm-2">商品条形码 :</label>

                        <div class="col-sm-6">
                            <input value="{{ $goods->bar_code }}" class="form-control" name="bar_code"
                                   placeholder="输入包装上商品条形码" type="text">
                        </div>
                    </div>
                    <div class="form-group editor-item">
                        <label class="control-label col-sm-2"> </label>

                        <div class="col-sm-10 goods-imgs">
                            @foreach($goods->images as $image)
                                <div class="thumbnail col-xs-3">
                                    <img alt="" src="{{ $image->image_url }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group image-upload editor-item {{ $goods->images->isEmpty() ? '' : 'hide' }}">
                        <label class="col-sm-2 control-label"></label>

                        <div class="col-sm-10">
                            <button data-height="400" data-width="400" data-target="#cropperModal" data-toggle="modal"
                                    data-loading-text="图片已达到最大数量" class="btn btn-primary btn-sm" type="button"
                                    id="pic-upload">
                                请选择图片文件(裁剪)
                            </button>

                            <div class="progress collapse">
                                <div class="progress-bar progress-bar-striped active"></div>
                            </div>

                            <div class="row pictures">

                            </div>
                        </div>
                    </div>


                    <div class="form-group editor-item">
                        <label class="control-label col-sm-2">分类 :</label>

                        <div class="col-sm-6">
                            <select name="cate_level_1" class="categories inline-control"></select>
                            <select name="cate_level_2" class="categories inline-control"> </select>
                            <select name="cate_level_3" class="categories inline-control"></select>
                        </div>
                    </div>
                    <div class="form-group editor-item">
                        <label class="col-sm-2"></label>

                        <div class="col-sm-10 attr">
                            @foreach($attrs as $key=>$attr)
                                <p class="items-item">
                                    <label>{{ $attr['name'] }}</label>
                                    <select name="attrs[{{ $attr['attr_id'] }}]" class="attrs  inline-control">
                                        <option value="0">请选择</option>
                                        @if(isset($attr['child']))
                                            @foreach($attr['child'] as $child)
                                                <option value="{{ $child['attr_id'] }}" {{ isset($attrGoods[$attr['attr_id']]) && $child['attr_id'] == $attrGoods[$attr['attr_id']]['attr_id'] ? 'selected' : '' }}>{{ $child['name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </p>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group editor-item">
                        <label class="control-label col-sm-2">价格 :</label>

                        <div class="col-sm-4">
                            <input name="price_retailer" value="{{ $goods->price_retailer }}" placeholder="请输入价格"
                                   type="text" class="form-control">
                        </div>
                        <label class="control-label col-sm-2">单位 :</label>

                        <div class="col-sm-2">
                            <select name="pieces_retailer" class="pieces form-control"
                                    data-change-class="pieces-retailer">
                                @foreach(cons()->valueLang('goods.pieces') as $key=> $piece)
                                    <option value="{{ $key }}" {{ $key == $goods->pieces_retailer ? 'selected' : '' }}>{{ $piece }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group editor-item">
                        <label class="control-label col-sm-2">最低购买数 :</label>

                        <div class="col-sm-6">
                            <input class="form-control" value="{{ $goods->min_num_retailer }}" name="min_num_retailer"
                                   type="text" placeholder="如1、2">
                        </div>
                        <div class="col-sm-2 pieces">
                            (<span class="pieces-retailer">{{ $goods->pieces_retailer ? cons()->valueLang('goods.pieces',$goods->pieces_retailer) : head(cons()->valueLang('goods.pieces')) }}</span>)
                        </div>
                    </div>
                    <div class="form-group editor-item">
                        <label class="control-label col-sm-2">规格 :</label>

                        <div class="col-sm-6">
                            <input name="specification_retailer" value="{{ $goods->specification_retailer }}"
                                   type="text"
                                   placeholder="如250ml*24、250ml" class="form-control">
                        </div>
                    </div>
                    @if (auth()->user()->type == cons('user.type.supplier'))
                        <div class="form-group editor-item">
                            <label class="control-label col-sm-2">价格(批发) :</label>

                            <div class="col-sm-4">
                                <input name="price_wholesaler" class="form-control"
                                       value="{{ $goods->price_wholesaler }}" type="text"
                                       placeholder="请输入批发价格">
                            </div>
                            <label class="control-label col-sm-2">单位(批发) :</label>

                            <div class="col-sm-2">
                                <select name="pieces_wholesaler" class="pieces form-control"
                                        data-change-class="pieces-wholesaler">
                                    @foreach(cons()->valueLang('goods.pieces') as $key=> $piece)
                                        <option value="{{ $key }}" {{ $key == $goods->pieces_wholesaler ? 'selected' : '' }}>{{ $piece }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group editor-item">
                            <label class="control-label col-sm-2">最低购买数(批发) :</label>

                            <div class="col-sm-6">
                                <input class="form-control" value="{{ $goods->min_num_wholesaler }}"
                                       name="min_num_wholesaler"
                                       type="text" placeholder="如1、2">
                            </div>
                            <div class="col-sm-2 pieces">
                                (<span class="pieces-wholesaler">{{ $goods->pieces_wholesaler ? cons()->valueLang('goods.pieces',$goods->pieces_wholesaler) : head(cons()->valueLang('goods.pieces')) }}</span>)
                            </div>

                        </div>
                        <div class="form-group editor-item">
                            <label class="control-label col-sm-2">规格(批发) :</label>

                            <div class="col-sm-6">
                                <input name="specification_wholesaler" value="{{ $goods->specification_wholesaler }}"
                                       type="text" placeholder="如250ml*24、250ml" class="form-control">
                            </div>
                        </div>
                    @endif
                    <div class="form-group editor-item">
                        <label class="control-label col-sm-2">保质期 :</label>

                        <div class="col-sm-6">
                            <input name="shelf_life" value="{{ $goods->shelf_life }}"
                                   type="text" placeholder="如 24个月" class="form-control">
                        </div>
                    </div>
                    <div class="form-group editor-item">
                        <label class="col-sm-2"></label>

                        <div class="check-item col-sm-10">
                            <label class="control-label">
                                <input name="is_new" value="1" {{ $goods->is_new ? 'checked' : '' }}
                                type="checkbox"> 新品</label>
                            <label class="control-label"><input name="is_out" value="1"
                                                                {{ $goods->is_out ? 'checked' : '' }}
                                                                type="checkbox"> 缺货</label>
                            <label class="control-label"><input name="is_expire" value="1"
                                                                {{ $goods->is_expire ? 'checked' : '' }}
                                                                type="checkbox"> 即期品</label>
                        </div>
                        <label class="col-sm-2"></label>

                        <div class="check-item col-sm-10">
                            <label class="control-label">
                                <input name="is_change" value="1" {{ $goods->is_change ? 'checked' : '' }} type="checkbox">
                                可换货
                            </label>
                            <label class="control-label">
                                <input name="is_back" value="1"
                                       {{ $goods->is_back ? 'checked' : '' }} type="checkbox">
                                可退货
                            </label>
                            <label class="control-label"><input name="is_promotion" value="1"
                                                                {{ $goods->is_promotion ? 'checked' : '' }} type="checkbox">
                                促销</label>
                        </div>
                    </div>
                    <div class="form-group editor-item promotions-msg {{ $goods->is_promotion ? '' : 'hide' }}">
                        <label class="control-label col-sm-2">促销信息 : </label>
                        <input type="text" name="promotion_info" value="{{ $goods->promotion_info }}"
                               {{ $goods->is_promotion ? '' : 'disabled' }} id="promotion_info">
                    </div>
                    <div class="form-group col-sm-12 graphic-wrap editor-item">
                        <label class="control-label col-sm-2">商品图文介绍 :</label>

                        <div class="col-sm-9 padding-clear">
                        <textarea class="introduce tinymce-editor form-control"
                                  name="introduce">{{ $goods->introduce }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="username">配送区域:</label>

                        <div class="col-sm-10 col-md-8 padding-clear">
                            <div class="col-sm-12">
                                <a id="add-address" class="btn btn-primary" href="javascript:"
                                   data-target="#addressModal"
                                   data-toggle="modal" data-loading-text="地址达到最大数量">添加配送区域</a>
                            </div>
                            <div class="address-list col-lg-12">
                                <div class="hidden">
                                    <input type="hidden" name="area[id][]" value=""/>
                                    <input type="hidden" name="area[province_id][]" value=""/>
                                    <input type="hidden" name="area[city_id][]" value=""/>
                                    <input type="hidden" name="area[district_id][]" value=""/>
                                    <input type="hidden" name="area[street_id][]" value=""/>
                                    <input type="hidden" name="area[area_name][]" value=""/>
                                    <input type="hidden" name="area[address][]" value=""/>
                                    {{--区域经纬度--}}
                                    {{--<input type="hidden" name="area[blx][]" value=""/>--}}
                                    {{--<input type="hidden" name="area[bly][]" value=""/>--}}
                                    {{--<input type="hidden" name="area[slx][]" value=""/>--}}
                                    {{--<input type="hidden" name="area[sly][]" value=""/>--}}
                                </div>
                                @foreach ($goods->deliveryArea as $area)
                                    <div class="col-sm-10 fa-border show-map">{{ $area->address_name }}
                                        <span class="fa fa-times-circle pull-right close-icon"></span>
                                        <input type="hidden" name="area[id][]" value="{{ $area->id }}"/>
                                        <input type="hidden" name="area[province_id][]"
                                               value="{{ $area->province_id }}"/>
                                        <input type="hidden" name="area[city_id][]" value="{{ $area->city_id }}"/>
                                        <input type="hidden" name="area[district_id][]"
                                               value="{{ $area->district_id }}"/>
                                        <input type="hidden" name="area[street_id][]" value="{{ $area->street_id }}"/>
                                        <input type="hidden" name="area[area_name][]" value="{{ $area->area_name }}"/>
                                        <input type="hidden" name="area[address][]" value="{{ $area->address }}"/>
                                        {{--区域经纬度--}}
                                        {{--<input type="hidden" name="area[blx][]"--}}
                                        {{--value="{{ $area->coordinate->bl_lng or '' }}"/>--}}
                                        {{--<input type="hidden" name="area[bly][]"--}}
                                        {{--value="{{ $area->coordinate->bl_lat or '' }}"/>--}}
                                        {{--<input type="hidden" name="area[slx][]"--}}
                                        {{--value="{{ $area->coordinate->sl_lng or '' }}"/>--}}
                                        {{--<input type="hidden" name="area[sly][]"--}}
                                        {{--value="{{ $area->coordinate->sl_lat or '' }}"/>--}}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{--<div class="col-sm-12 map">--}}
                    {{--<p><label>地图标识 :</label></p>--}}

                    {{--<div id="map"></div>--}}
                    {{--</div>--}}
                </div>
            </div>
            <div class="col-sm-12 text-center save padding-clear">
                @if (!$goods->id)
                    <label><input type="checkbox" name="status" value="1"> 立即上架<span class="prompt">(勾选后保存商品会立即上架,可被购买者查看购买)</span></label>
                @endif
                <p class="save-btn">
                    <button class="btn btn-bg btn-primary" type="submit"> 保存</button>
                </p>
            </div>
        </form>
    </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        {{--$(document).ready(function () {--}}
        {{--getCoordinateMap({!! $coordinates or '' !!});--}}
        {{--});--}}
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