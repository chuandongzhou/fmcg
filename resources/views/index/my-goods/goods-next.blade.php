@extends('index.menu-master')

@include('includes.cropper')
@include('includes.tinymce',['full' => true])
@include('includes.shop-address')
@section('subtitle', '商品')
@section('top-title')
    <a href="{{ url('my-goods') }}">商品管理</a> >
    <a href="{{ url('my-goods') }}">我的商品</a> >
    <span class="second-level">{{ $goods->id ? '编辑' : '新增' }}商品</span>
@stop

@section('right')
    <div class="row goods-editor">
        <div class="col-sm-12">
            <div class="row audit-step-outs">
                <div class="col-xs-2"></div>
                <div class="col-xs-4 step first step-active">
                    商品基本信息
                    <span class="triangle-right first"></span>
                    <span class="triangle-right last"></span>
                </div>
                <div class="col-xs-4 step last step-active">
                    商品基本信息
                </div>
                <div class="col-xs-2"></div>
            </div>
        </div>
        <div class="col-sm-12 ">
            <form class="form-horizontal ajax-form" method="{{ $goods->id ? 'put' : 'post' }}"
                  action="{{ url('api/v1/my-goods/'.$goods->id) }}"
                  data-help-class="col-sm-push-1 col-sm-10" data-done-then="referer"
                  autocomplete="off">
                <input type="hidden" name="cate_level_1" value="{{ $data['cate_level_1'] }}"/>
                <input type="hidden" name="cate_level_2" value="{{ $data['cate_level_2'] }}"/>
                <input type="hidden" name="cate_level_3" value="{{ $data['cate_level_3']}}"/>
                <input type="hidden" name="bar_code" value="{{ $data['bar_code'] }}"/>
                <input type="hidden" name="name" value="{{ $data['name'] }}"/>
                <input type="hidden" name="pieces_level_1" value="{{ $data['pieces_level_1'] }}"/>
                <input type="hidden" name="pieces_level_2" value="{{ $data['pieces_level_2'] }}"/>
                <input type="hidden" name="pieces_level_3" value="{{ $data['pieces_level_3'] }}"/>
                <input type="hidden" name="system_1" value="{{ $data['system_1'] }}"/>
                <input type="hidden" name="system_2" value="{{ $data['system_2'] }}"/>
                <input type="hidden" name="specification" value="{{ $data['specification'] }}"/>
                @if(isset($data['image']))
                    <input type="hidden" name="image" value="{{ $data['image'] }}"/>
                @endif
                @if(isset($data['images']))
                    @for($i=0;$i<count($data['images']['id']);$i++)
                        <input type="hidden" name="images[id][]" value="{{ $data['images']['id'][$i] }}"/>
                        <input type="hidden" name="images[path][]" value="{{ $data['images']['path'][$i] }}"/>
                        <input type="hidden" name="images[org_name][]" value="{{ $data['images']['org_name'][$i] }}"/>
                        <input type="hidden" name="images[name][]" value="{{ $data['images']['name'][$i] }}"/>

                    @endfor
                @endif
                <div class="row editor-panel content-wrap">
                    <div class="col-sm-12 ">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><b>终端商购买价格</b></h3>
                            </div>
                            <div class="panel-container">
                                <div class="form-group editor-item">
                                    <label class="control-label col-sm-2"><span class="red">*</span> 单位 :
                                    </label>

                                    <div class="col-sm-2">
                                        <select class="form-control" name="pieces_retailer">
                                            <option value="">请选择</option>
                                            <option value="{{ $data['pieces_level_1']}}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_1==$goods->pieces_retailer ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$data['pieces_level_1']) }}</option>
                                            @if($data['pieces_level_2'] != '')
                                                <option value="{{ $data['pieces_level_2'] }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_2== $goods->pieces_retailer ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$data['pieces_level_2']) }}</option>
                                            @endif
                                            @if($data['pieces_level_3']!='')
                                                <option value="{{ $data['pieces_level_3'] }}" {{ $goods->goodsPieces&&$goods->goodsPieces->pieces_level_3== $goods->pieces_retailer ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$data['pieces_level_3']) }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group editor-item">
                                    <label class="control-label col-sm-2"><span class="red">*</span> 价格
                                        :</label>

                                    <div class="col-sm-3">
                                        <input name="price_retailer" value="{{ $goods->price_retailer }}" type="text"
                                               class="form-control" placeholder="请输入价格"/>
                                    </div>
                                    <div class="col-sm-1 pieces padding-clear">元/
                                        <span class="pieces_retailer">
                                            @if($goods->goodsPieces && $goods->goodsPieces->pieces_level_1==$goods->pieces_retailer)
                                                {{ cons()->valueLang('goods.pieces',$data['pieces_level_1']) }}

                                            @elseif($data['pieces_level_2'] != '' &&$goods->goodsPieces&& $goods->goodsPieces->pieces_level_2==$goods->pieces_retailer)
                                                {{cons()->valueLang('goods.pieces',$data['pieces_level_2'])}}
                                            @elseif($data['pieces_level_3'] != '' &&$goods->goodsPieces&& $goods->goodsPieces->pieces_level_3==$goods->pieces_retailer)
                                                {{ cons()->valueLang('goods.pieces',$data['pieces_level_3']) }}
                                            @endif

                                        </span>
                                    </div>
                                    <label class="control-label col-sm-2"><span class="red">*</span> 自提价
                                        :</label>

                                    <div class="col-sm-3">
                                        <input type="text" name="price_retailer_pick_up"
                                               value="{{ $goods->price_retailer_pick_up }}" class="form-control"
                                               placeholder="请输自提价"/>
                                    </div>
                                    <div class="col-sm-1 pieces padding-clear">元/<span
                                                class="pieces_retailer">
                                            @if($goods->goodsPieces&&$goods->goodsPieces->pieces_level_1==$goods->pieces_retailer)
                                                {{ cons()->valueLang('goods.pieces',$data['pieces_level_1']) }}

                                            @elseif($data['pieces_level_2'] != '' &&$goods->goodsPieces&& $goods->goodsPieces->pieces_level_2==$goods->pieces_retailer)
                                                {{cons()->valueLang('goods.pieces',$data['pieces_level_2'])}}
                                            @elseif($data['pieces_level_3'] != '' &&$goods->goodsPieces&& $goods->goodsPieces->pieces_level_3==$goods->pieces_retailer)
                                                {{ cons()->valueLang('goods.pieces',$data['pieces_level_3']) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group editor-item">
                                    <label class="control-label col-sm-2"><span class="red">*</span> 最低购买数
                                        :</label>

                                    <div class="col-sm-3">
                                        <input type="text" value="{{ $goods->min_num_retailer }}"
                                               name="min_num_retailer" class="form-control" placeholder="如 3 "/>
                                    </div>
                                    <div class="col-sm-1 pieces padding-clear"><span
                                                class="pieces_retailer">
                                            @if($goods->goodsPieces&&$goods->goodsPieces->pieces_level_1==$goods->pieces_retailer)
                                                {{ cons()->valueLang('goods.pieces',$data['pieces_level_1']) }}

                                            @elseif($data['pieces_level_2'] != '' &&$goods->goodsPieces&& $goods->goodsPieces->pieces_level_2==$goods->pieces_retailer)
                                                {{cons()->valueLang('goods.pieces',$data['pieces_level_2'])}}
                                            @elseif($data['pieces_level_3'] != '' &&$goods->goodsPieces&& $goods->goodsPieces->pieces_level_3==$goods->pieces_retailer)
                                                {{ cons()->valueLang('goods.pieces',$data['pieces_level_3']) }}
                                            @endif
                                        </span>
                                    </div>
                                    <label class="control-label col-sm-2">规格 :</label>

                                    <div class="col-sm-3  spec">
                                        {{ $data['specification'] }}
                                        <input type="hidden" name="specification_retailer" value="{{ $goods->specification_retailer }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->type == cons('user.type.supplier'))
                        <div class="col-sm-12 ">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><b>批发商购买价格</b></h3>
                                </div>
                                <div class="panel-container">
                                    <div class="form-group editor-item">
                                        <label class="control-label col-sm-2"><span class="red">*</span> 单位 :
                                        </label>

                                        <div class="col-sm-2">
                                            <select class="form-control" name="pieces_wholesaler">
                                                <option value="">请选择</option>
                                                <option value="{{ $data['pieces_level_1'] }}" {{  $goods->goodsPieces && $goods->goodsPieces->pieces_level_1==$goods->pieces_wholesaler ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$data['pieces_level_1']) }}</option>
                                                @if($data['pieces_level_2']!='')
                                                    <option value="{{ $data['pieces_level_2'] }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_2== $goods->pieces_wholesaler ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$data['pieces_level_2']) }}</option>
                                                @endif
                                                @if($data['pieces_level_3']!='')
                                                    <option value="{{ $data['pieces_level_3'] }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_3== $goods->pieces_wholesaler? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$data['pieces_level_3']) }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group editor-item">
                                        <label class="control-label col-sm-2"><span class="red">*</span> 价格
                                            :</label>

                                        <div class="col-sm-3">
                                            <input type="text" name="price_wholesaler" class="form-control"
                                                   value="{{ $goods->price_wholesaler }}" placeholder="请输入价格"/>
                                        </div>
                                        <div class="col-sm-1 pieces padding-clear">元/<span
                                                    class="pieces_wholesaler">
                                                @if($goods->goodsPieces && $goods->goodsPieces->pieces_level_1==$goods->pieces_wholesaler)
                                                    {{ cons()->valueLang('goods.pieces',$data['pieces_level_1']) }}

                                                @elseif($data['pieces_level_2'] != '' && $goods->goodsPieces && $goods->goodsPieces->pieces_level_2==$goods->pieces_wholesaler)
                                                    {{cons()->valueLang('goods.pieces',$data['pieces_level_2'])}}
                                                @elseif($data['pieces_level_3'] != '' && $goods->goodsPieces && $goods->goodsPieces->pieces_level_3==$goods->pieces_wholesaler)
                                                    {{ cons()->valueLang('goods.pieces',$data['pieces_level_3']) }}
                                                @endif
                                            </span>
                                        </div>
                                        <label class="control-label col-sm-2"><span class="red">*</span> 自提价
                                            :</label>

                                        <div class="col-sm-3">
                                            <input type="text" name="price_wholesaler_pick_up" class="form-control"
                                                   value="{{ $goods->price_wholesaler_pick_up }}" placeholder="请输自提价"/>
                                        </div>
                                        <div class="col-sm-1 pieces padding-clear">元/<span
                                                    class="pieces_wholesaler">
                                                 @if($goods->goodsPieces && $goods->goodsPieces->pieces_level_1==$goods->pieces_wholesaler)
                                                    {{ cons()->valueLang('goods.pieces',$data['pieces_level_1']) }}

                                                @elseif($data['pieces_level_2'] != '' && $goods->goodsPieces && $goods->goodsPieces->pieces_level_2==$goods->pieces_wholesaler)
                                                    {{cons()->valueLang('goods.pieces',$data['pieces_level_2'])}}
                                                @elseif($data['pieces_level_3'] != '' && $goods->goodsPieces && $goods->goodsPieces->pieces_level_3==$goods->pieces_wholesaler)
                                                    {{ cons()->valueLang('goods.pieces',$data['pieces_level_3']) }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group editor-item">
                                        <label class="control-label col-sm-2"><span class="red">*</span> 最低购买数
                                            :</label>

                                        <div class="col-sm-3">
                                            <input type="text" value="{{ $goods->min_num_wholesaler }}"
                                                   name="min_num_wholesaler" class="form-control" placeholder="如 3 "/>
                                        </div>
                                        <div class="col-sm-1 pieces padding-clear"><span
                                                    class="pieces_wholesaler">
                                                @if($goods->goodsPieces && $goods->goodsPieces->pieces_level_1==$goods->pieces_wholesaler)
                                                    {{ cons()->valueLang('goods.pieces',$data['pieces_level_1']) }}

                                                @elseif($data['pieces_level_2'] != '' && $goods->goodsPieces && $goods->goodsPieces->pieces_level_2==$goods->pieces_wholesaler)
                                                    {{cons()->valueLang('goods.pieces',$data['pieces_level_2'])}}
                                                @elseif($data['pieces_level_3'] != '' && $goods->goodsPieces && $goods->goodsPieces->pieces_level_3==$goods->pieces_wholesaler)
                                                    {{ cons()->valueLang('goods.pieces',$data['pieces_level_3']) }}
                                                @endif
                                            </span>
                                        </div>
                                        <label class="control-label col-sm-2">规格 :</label>

                                        <div class="col-sm-3  spec">
                                            {{ $data['specification'] }}
                                            <input type="hidden" name="specification_wholesaler" value="{{ $goods->specification_wholesaler }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-12 ">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><b>商品属性</b></h3>
                            </div>
                            <div class="panel-container">
                                <div class="form-group editor-item">

                                    @foreach($attrs as $key=>$attr)
                                        <label class="control-label col-sm-2 item">{{ $attr['name'] }}</label>
                                        <div class="col-sm-2 item">
                                            <select name="attrs[{{ $attr['attr_id'] }}]" class="attrs  form-control">
                                                <option value="0">请选择</option>
                                                @if(isset($attr['child']))
                                                    @foreach($attr['child'] as $child)
                                                        <option value="{{ $child['attr_id'] }}" {{ isset($attrGoods[$attr['attr_id']]) && $child['attr_id'] == $attrGoods[$attr['attr_id']]['attr_id'] ? 'selected' : '' }}>{{ $child['name'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group editor-item">
                                    <label class="control-label col-sm-2 ">保质期 : </label>
                                    <div class="col-sm-4 item">
                                        <input type="text" name="shelf_life" value="{{ $goods->shelf_life }}"
                                               class="form-control" placeholder="如12个月"/>
                                    </div>
                                </div>
                                <div class="form-group editor-item">
                                    <label class="control-label col-sm-2 ">标签 : </label>
                                    <div class="col-sm-10 label-wrap">
                                        <label class="control-label">
                                            <input type="checkbox" name="is_new"
                                                   value="1" {{ $goods->is_new ? 'checked' : '' }}>
                                            新品
                                        </label>
                                        <label class="control-label">
                                            <input type="checkbox" name="is_out" value="1"
                                                    {{ $goods->is_out ? 'checked' : '' }}>
                                            缺货
                                        </label>
                                        <label class="control-label">
                                            <input type="checkbox" name="is_expire" alue="1"
                                                    {{ $goods->is_expire ? 'checked' : '' }}>
                                            即期品
                                        </label>
                                        <label class="control-label">
                                            <input type="checkbox" name="is_change"
                                                   value="1" {{ $goods->is_change ? 'checked' : '' }}>
                                            可换货
                                        </label>
                                        <label class="control-label">
                                            <input type="checkbox" name="is_promotion" value="1"
                                                    {{ $goods->is_promotion ? 'checked' : '' }} >
                                            促销
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group editor-item promotions-msg {{ $goods->is_promotion ? '' : 'hide' }}">
                                    <label class="control-label col-sm-2">促销信息
                                        : </label>
                                    <div class="col-sm-9 item">
                                        <input class="form-control" type="text" name="promotion_info"
                                               value="{{ $goods->promotion_info }}"
                                               {{ $goods->is_promotion ? '' : 'disabled' }} id="promotion_info"/>
                                    </div>
                                </div>
                                <div class="form-group  graphic-wrap editor-item">
                                    <label class="control-label col-sm-2">商品图文介绍 :</label>

                                    <div class="col-sm-9 padding-clear">

                                        <textarea class="introduce tinymce-editor form-control" name="introduce">
                                            {{ $goods->introduce }}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="username">配送区域:</label>

                                    <div class="col-sm-10 col-md-8 padding-clear">
                                        <div class="col-sm-12">
                                            @if(!empty($goods->shopDeliveryArea))
                                                <a id="add-address" class="btn btn-blue-lighter" href="javascript:"
                                                   data-target="#shopAddressModal" data-toggle="modal"
                                                   data-loading-text="地址达到最大数量">选择配送区域</a>
                                            @else
                                                <span class="prompt">还未添加配送区域</span>
                                                <a href="{{ url('personal/delivery-area/create') }}">去添加</a>
                                            @endif


                                        </div>
                                        <div class="address-list col-lg-12">
                                            <div class="hidden">
                                                <input type="hidden" name="area[id][]" value="">
                                                <input type="hidden" name="area[province_id][]" value="">
                                                <input type="hidden" name="area[city_id][]" value="">
                                                <input type="hidden" name="area[district_id][]" value="">
                                                <input type="hidden" name="area[street_id][]" value="">
                                                <input type="hidden" name="area[area_name][]" value="">
                                                <input type="hidden" name="area[address][]" value="">
                                            </div>
                                            @foreach($goods->deliveryArea as $area)

                                                <div class="col-sm-10 show-map">{{ $area->address_name.'('.$area->min_money.')' }}
                                                    <span class="fa fa-times pull-right close-icon"></span>
                                                    <input type="hidden" name="area[id][]" value="{{ $area->id }}"/>
                                                    <input type="hidden" name="area[province_id][]"
                                                           value="{{ $area->province_id }}"/>
                                                    <input type="hidden" name="area[city_id][]"
                                                           value="{{ $area->city_id }}"/>
                                                    <input type="hidden" name="area[district_id][]"
                                                           value="{{ $area->district_id }}"/>
                                                    <input type="hidden" name="area[street_id][]"
                                                           value="{{ $area->street_id }}"/>
                                                    <input type="hidden" name="area[area_name][]"
                                                           value="{{ $area->area_name }}"/>
                                                    <input type="hidden" name="area[address][]"
                                                           value="{{ $area->address }}"/>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-sm-12 text-left save padding-clear">
                    @if (!$goods->status)
                        <p><input type="checkbox" name="status" value="1"> 立即上架<span class="prompt">(勾选后保存商品会立即上架,可被购买者查看购买)</span>
                        </p>
                    @endif
                    <button class="btn btn-success" type="submit">提交</button>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {

            //促销信息的显示与隐藏
            $('input[name="is_promotion"]').change(function () {
                var promotionInfo = $('input[name="promotion_info"]');
                $(this).is(':checked') ? promotionInfo.prop('disabled', false).parents('.promotions-msg').removeClass('hide') : promotionInfo.prop('disabled', true).parents('.promotions-msg').addClass('hide');
            });
            //选择单位
            $('select[name="pieces_retailer"]').change(function () {
                var html = $(this).find("option:selected").text() == "请选择" ? '' : $(this).find("option:selected").text();
                $('.pieces_retailer').html(html);
                var value = $(this).find("option:selected").val();
                if(value == '{{ $data['pieces_level_1'] }}'){
                    $('input[name="specification_retailer"]').val('{{ $data['specification'] }}');

                }else if(value == '{{ $data['pieces_level_2'] }}'){
                    $('input[name="specification_retailer"]').val('{{ $data['system_1'] }}'+'*'+'{{ $data['specification'] }}');

                }else if(value == '{{ $data['pieces_level_3'] }}'){
                    $('input[name="specification_retailer"]').val('{{  $data['system_1']* $data['system_2'] }}'+'*'+'{{$data['specification'] }}');

                }
            });
            $('select[name="pieces_wholesaler"]').change(function () {
                var html = $(this).find("option:selected").text() == "请选择" ? '' : $(this).find("option:selected").text();
                $('.pieces_wholesaler').html(html);
                var value = $(this).find("option:selected").val();
                if(value == '{{ $data['pieces_level_1'] }}'){
                    $('input[name="specification_wholesaler"]').val('{{ $data['specification'] }}');

                }else if(value == '{{ $data['pieces_level_2'] }}'){
                    $('input[name="specification_wholesaler"]').val('{{ $data['system_1'] }}'+'*'+'{{ $data['specification'] }}');

                }else if(value == '{{ $data['pieces_level_3'] }}'){
                    $('input[name="specification_wholesaler"]').val('{{  $data['system_1']* $data['system_2'] }}'+'*'+'{{$data['specification'] }}');

                }
            });
        });

    </script>
@stop