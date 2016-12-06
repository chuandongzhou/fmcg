@extends('index.menu-master')

@include('includes.cropper')
@include('includes.tinymce',['full' => true])

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
                <div class="col-xs-4 step last">
                    商品基本信息
                </div>
                <div class="col-xs-2"></div>
            </div>
        </div>
        <div class="col-sm-12 ">
            <form class="form-horizontal form-goods" method="post" action="{{ url('my-goods/update-next') }}"
                  data-help-class="col-sm-7" data-done-then="referer" autocomplete="off">
                {{ csrf_field() }}
                <input name="goods_id" type="hidden" value="{{ $goods->id }}"/>
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
                            <label for="name" class="control-label col-sm-2"><span class="red">*</span> 商品名称 :</label>
                            <div class="col-sm-4">
                                <input type="text" name="name" class="form-control" value="{{ $goods->name }}"
                                       placeholder="请输入商品名称">
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
                                <div class="form-group editor-item">
                                    <label class="control-label col-sm-2">一级单位 :</label>
                                    <div class="col-sm-2">
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
                                    <div class="col-sm-2">
                                        <select class="form-control" name="pieces_level_2">
                                            <option value="">请选择</option>
                                            @foreach(cons()->valueLang('goods.pieces') as $key =>$pieces)
                                                <option value="{{ $key }}" {{ $goods->goodsPieces&&is_numeric($goods->goodsPieces->pieces_level_2)&&$goods->goodsPieces->pieces_level_2==$key?'selected':'' }}>{{ $pieces }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-sm-1">进制 :</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="system_1" class="form-control"
                                               value="{{ $goods->goodsPieces&&$goods->goodsPieces->system_1?$goods->goodsPieces->system_1:'' }}"
                                               placeholder="请输入数量"/>
                                    </div>
                                    <div class="col-sm-1 padding-clear pieces system_1"></div>
                                </div>
                                <div class="form-group editor-item">
                                    <label class="control-label col-sm-2">三级单位 :</label>
                                    <div class="col-sm-2">
                                        <select class="form-control" name="pieces_level_3">
                                            <option value="">请选择</option>
                                            @foreach(cons()->valueLang('goods.pieces') as $key =>$pieces)
                                                <option value="{{ $key }}" {{ $goods->goodsPieces&&is_numeric($goods->goodsPieces->pieces_level_3)&&$goods->goodsPieces->pieces_level_3==$key?'selected':'' }}>{{ $pieces }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-sm-1">进制 :</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="system_2"
                                               value="{{ $goods->goodsPieces&&$goods->goodsPieces->system_2?$goods->goodsPieces->system_2:'' }}"
                                               class="form-control" placeholder="请输入数量"/>
                                    </div>
                                    <div class="col-sm-1 padding-clear pieces system_2"></div>
                                </div>
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
                <div class="col-sm-12 text-left save">
                    <button class="btn btn-success" type="submit"> 下一步</button>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
                    @if(count($errors) > 0)
            var errorMeg = JSON.parse('{!! json_encode($errors->toArray()) !!}');
            var form = $('.form-goods');
            form.formValidate(errorMeg);
            @endif
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

            $('select[name="pieces_level_2"]').change(function () {
                $('.system_1').html($(this).find("option:selected").text());
            });
            $('select[name="pieces_level_3"]').change(function () {
                $('.system_2').html($(this).find("option:selected").text());
            });
        });

    </script>
@stop