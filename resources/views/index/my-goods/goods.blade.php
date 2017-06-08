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
    <form class="form-horizontal ajax-form" method="{{ $goods->id ? 'put' : 'post' }}"
          action="{{ url('api/v1/my-goods/'.$goods->id) }}" data-done-url="{{ url('my-goods') }}"
          data-help-class="col-sm-push-1 col-sm-10" data-done-then="referer"
          autocomplete="off">
        <div class="row goods-editor goods-editor-first">
            <div class="col-sm-12">
                <div class="row audit-step-outs">
                    <div class="col-xs-2"></div>
                    <div class="col-xs-4 step first step-active">
                        商品基本信息
                        <span class="triangle-right first"></span>
                        <span class="triangle-right last"></span>
                    </div>
                    <div class="col-xs-4 step last btn-next">
                        商品价格属性
                    </div>
                    <div class="col-xs-2"></div>
                </div>
            </div>
            <div class="col-sm-12 ">
                <input name="goods_id" type="hidden" value="{{ $goods->id }}"/>
                @if(isset($goods->abnormalInfo))
                    <input name="abnormalGoodsId" type="hidden" value="{{$goods->abnormalInfo['goods_id']}}"/>
                    <input name="abnormalOrderId" type="hidden" value="{{$goods->abnormalInfo['order_id']}}"/>
                @endif
                <div class="row editor-panel content-wrap">
                    <div class="col-sm-12 editor-wrap">
                        <div class="form-group editor-item">
                            <label class="control-label col-sm-2"><span class="red">*</span> 分类 :</label>
                            <div class="col-sm-2">
                                <select name="cate_level_1" class="categories form-control"></select>
                            </div>
                            <div class="col-sm-2">
                                <select name="cate_level_2" class="categories form-control"> </select>
                            </div>
                            <div class="col-sm-2">
                                <select name="cate_level_3" class="categories form-control"></select>
                            </div>
                        </div>
                        <div class="form-group editor-item">
                            <label class="control-label col-sm-2"><span class="red">*</span> 商品条形码 :</label>
                            <div class="col-sm-4">
                                <input value="{{ $goods->bar_code }}" class="form-control" name="bar_code"
                                       placeholder="输入包装上商品条形码" type="text">
                            </div>
                        </div>
                        <div class="form-group editor-item">
                            <label for="name" class="control-label col-sm-2"><span class="red">*</span> 商品名称
                                :</label>
                            <div class="col-sm-4">
                                <input type="text" name="name" class="form-control" value="{{ $goods->name }}"
                                       placeholder="请输入商品名称">
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
                        <div class="form-group image-upload editor-item  hide">
                            <label class="col-sm-2 control-label"></label>

                            <div class="col-sm-10">
                                <button data-height="400" data-width="400" data-target="#cropperModal"
                                        data-toggle="modal"
                                        data-loading-text="图片已达到最大数量" class="btn btn-blue-lighter" type="button"
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
                    </div>
                    <div class="col-sm-12 ">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><span class="red">*</span> <b>单位设置</b> <span class="prompt">至少设置一级单位</span>
                                </h3>
                            </div>
                            <div class="panel-container">
                                <div class="row margin-clear goodsPieces">
                                    <div class="col-sm-8">
                                        <div class="form-group editor-item">
                                            <label class="control-label col-sm-2">一级单位 :</label>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="pieces_level_1">
                                                    <option value="">请选择</option>
                                                    @foreach(cons()->valueLang('goods.pieces') as $key =>$pieces)
                                                        <option value="{{ $key }}" {{ $goods->goodsPieces&&$goods->goodsPieces->pieces_level_1==$key?'selected':'' }}>{{ $pieces }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group editor-item">
                                            <label class="control-label col-sm-2">二级单位 :</label>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="pieces_level_2">
                                                    <option value="">请选择</option>
                                                    @foreach(cons()->valueLang('goods.pieces') as $key =>$pieces)
                                                        <option value="{{ $key }}" {{ $goods->goodsPieces&&is_numeric($goods->goodsPieces->pieces_level_2)&&$goods->goodsPieces->pieces_level_2==$key?'selected':'' }}>{{ $pieces }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label class="control-label col-sm-2">进制 :</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="system_1" class="form-control"
                                                       value="{{ $goods->goodsPieces&&$goods->goodsPieces->system_1?$goods->goodsPieces->system_1:'' }}"
                                                       placeholder="请输入数量"/>
                                            </div>
                                            <div class="col-sm-1 padding-clear pieces system_1">
                                                {{ $goods->goodsPieces&&is_numeric($goods->goodsPieces->pieces_level_2)?cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_2):'' }}

                                            </div>
                                        </div>
                                        <div class="form-group editor-item">
                                            <label class="control-label col-sm-2">三级单位 :</label>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="pieces_level_3">
                                                    <option value="">请选择</option>
                                                    @foreach(cons()->valueLang('goods.pieces') as $key =>$pieces)
                                                        <option value="{{ $key }}" {{ $goods->goodsPieces&&is_numeric($goods->goodsPieces->pieces_level_3)&&$goods->goodsPieces->pieces_level_3==$key?'selected':'' }}>{{ $pieces }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label class="control-label col-sm-2">进制 :</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="system_2"
                                                       value="{{ $goods->goodsPieces&&$goods->goodsPieces->system_2?$goods->goodsPieces->system_2:'' }}"
                                                       class="form-control" placeholder="请输入数量"/>
                                            </div>
                                            <div class="col-sm-1 padding-clear pieces system_2">
                                                {{ $goods->goodsPieces&&is_numeric($goods->goodsPieces->pieces_level_3)?cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_3):'' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 example ">
                                        <div>例:</div>
                                        <div>一级单位:箱</div>
                                        <div>二级单位:盒&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  进制:30盒=1箱</div>
                                        <div>三级单位:袋&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;进制:12袋=1盒
                                        </div>
                                        <div>最小单位规格:15g=1袋</div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group editor-item">
                                            <label class="control-label col-sm-2">最小单位规格 :</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="specification" class="form-control"
                                                       placeholder="例如 : 250ml/25g"
                                                       value="{{ $goods->goodsPieces&&$goods->goodsPieces->specification?$goods->goodsPieces->specification:'' }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 text-left save">
                    <button class="btn btn-success btn-next" type="button"> 下一步</button>
                </div>
            </div>
        </div>
        <div class="row goods-editor goods-editor-second hide">
            <div class="col-sm-12">
                <div class="row audit-step-outs">
                    <div class="col-xs-2"></div>
                    <div class="col-xs-4 step first step-active btn-previous">
                        商品基本信息
                        <span class="triangle-right first"></span>
                        <span class="triangle-right last"></span>
                    </div>
                    <div class="col-xs-4 step last step-active">
                        商品价格属性
                    </div>
                    <div class="col-xs-2"></div>
                </div>
            </div>
            <div class="col-sm-12 ">

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
                                            <option value="请选择">请选择</option>
                                            @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_1))
                                                <option class="retailer_pieces_level_1"
                                                        value="{{ $goods->goodsPieces->pieces_level_1 }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_1==$goods->pieces_retailer ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_1) }}</option>
                                            @endif
                                            @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_2))
                                                <option class="retailer_pieces_level_2"
                                                        value="{{ $goods->goodsPieces->pieces_level_2 }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_2==$goods->pieces_retailer ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_2) }}</option>
                                            @endif
                                            @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_3))
                                                <option class="retailer_pieces_level_3"
                                                        value="{{ $goods->goodsPieces->pieces_level_3 }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_3==$goods->pieces_retailer ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_3) }}</option>
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
                                        <span class="pieces_retailer">{{ is_numeric($goods->pieces_retailer)?cons()->valueLang('goods.pieces',$goods->pieces_retailer):'' }}</span>
                                    </div>
                                    <label class="control-label col-sm-2"><span class="red">*</span> 自提价
                                        :</label>

                                    <div class="col-sm-3">
                                        <input type="text" name="price_retailer_pick_up"
                                               value="{{ $goods->price_retailer_pick_up }}" class="form-control"
                                               placeholder="请输自提价"/>
                                    </div>
                                    <div class="col-sm-1 pieces padding-clear">元/<span
                                                class="pieces_retailer">{{is_numeric($goods->pieces_retailer)?cons()->valueLang('goods.pieces',$goods->pieces_retailer):'' }}</span>
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
                                                class="pieces_retailer">{{ is_numeric($goods->pieces_retailer)?cons()->valueLang('goods.pieces',$goods->pieces_retailer):'' }}</span>
                                    </div>
                                    <label class="control-label col-sm-2">规格 :</label>

                                    <div class="col-sm-3 spec spec_retailer">{{ $goods->specification_retailer }}</div>
                                    <input type="hidden" name="specification_retailer"
                                           value="{{ $goods->specification_retailer }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->type == cons('user.type.supplier') ||auth()->user()->type == cons('user.type.maker'))
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
                                                <option value="请选择">请选择</option>
                                                @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_1))
                                                    <option class="wholesaler_pieces_level_1"
                                                            value="{{ $goods->goodsPieces->pieces_level_1 }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_1==$goods->pieces_wholesaler ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_1) }}</option>
                                                @endif
                                                @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_2))
                                                    <option class="wholesaler_pieces_level_2"
                                                            value="{{ $goods->goodsPieces->pieces_level_2 }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_2==$goods->pieces_wholesaler ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_2) }}</option>
                                                @endif
                                                @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_3))
                                                    <option class="wholesaler_pieces_level_3"
                                                            value="{{ $goods->goodsPieces->pieces_level_3 }}" {{ $goods->goodsPieces && $goods->goodsPieces->pieces_level_3==$goods->pieces_wholesaler ? 'selected' : '' }}>{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_3) }}</option>
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
                                                 {{ is_numeric($goods->pieces_wholesaler)?cons()->valueLang('goods.pieces',$goods->pieces_wholesaler):'' }}

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
                                                 {{  is_numeric($goods->pieces_wholesaler)?cons()->valueLang('goods.pieces',$goods->pieces_wholesaler):'' }}

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
                                                {{   is_numeric($goods->pieces_wholesaler)?cons()->valueLang('goods.pieces',$goods->pieces_wholesaler):'' }}

                                            </span>
                                        </div>
                                        <label class="control-label col-sm-2">规格 :</label>

                                        <div class="col-sm-3 spec spec_wholesaler">
                                            {{ $goods->specification_wholesaler }}
                                        </div>
                                        <input type="hidden" name="specification_wholesaler"
                                               value="{{ $goods->specification_wholesaler }}"/>
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
                                <div class="form-group editor-item attr">

                                    @foreach($attrs as $key=>$attr)
                                        <label class="control-label col-sm-2">{{ $attr['name'] }}</label>
                                        <div class="col-sm-2 item">
                                            <select name="attrs[{{ $attr['attr_id'] }}]" class="  form-control">
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
                                            <input type="checkbox" name="is_expire" value="1"
                                                    {{ $goods->is_expire ? 'checked' : '' }}>
                                            即期品
                                        </label>
                                        <label class="control-label">
                                            <input type="checkbox" name="is_change"
                                                   value="1" {{ $goods->is_change ? 'checked' : '' }}>
                                            可换货
                                        </label>
                                        <label class="control-label">
                                            <input name="is_back" value="1"
                                                   {{ $goods->is_back ? 'checked' : '' }} type="checkbox">
                                            可退货
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
                                <div class="form-group  editor-item graphic-wrap">
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
                                                <input type="hidden" name="area[min_money][]"
                                                       value=" "/>
                                            </div>
                                            @foreach($goods->deliveryArea as $area)

                                                <div class="col-sm-10 show-map">{{ $area->address_name }}
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
                                                    <input type="hidden" name="area[min_money][]"
                                                           value="{{ $area->min_money }}"/>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-sm-12 text-left save">
                    @if (!$goods->status)
                        <p><input type="checkbox" name="status" value="1"> 立即上架<span class="prompt">(勾选后保存商品会立即上架,可被购买者查看购买)</span>
                        </p>
                    @endif
                    <button class="btn btn-success" type="submit">提交</button>
                </div>

            </div>
        </div>
    </form>
@stop
@section('js')
    @parent
    <script type="text/javascript" src="{{ asset('js/jquery.validate.js')}}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {

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
            {{--addGoodsFunc('{{ $goods->cate_level_1 }}', '{{ $goods->cate_level_2 }}', '{{ $goods->cate_level_3 }}');--}}
            loadGoodsImages('{{ $goods->bar_code }}');
            //获取标签
            getAttr();

            //下一步
            $(".btn-next").click(function () {
                if (validform().form()) {
                    //通过表单验证,缓存数据
                    $('.goods-editor-first').addClass('hide');
                    $('.goods-editor-second').removeClass('hide');
                }
            });
            //上一步
            $('.btn-previous').click(function () {
                $('.goods-editor-first').removeClass('hide');
                $('.goods-editor-second').addClass('hide');
            });


            //终端商(批发商)选择单位变化时
            selectedChange();

        });

    </script>
@stop