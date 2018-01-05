@extends('index.manage-master')
@include('includes.cropper')
@include('includes.my-goods', ['getGoodsUrl' => url('api/v1/personal/model/goods-page'), 'setAdvertUrl' => url('api/v1/personal/model/recommend-goods')])
@include('includes.templet-model')
@section('subtitle', '个人中心-首页广告')
@section('css')
    @parent
    <link href="{{ asset('css/bootstrap-colorpalette.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
@stop
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/model/model-edit') }}">模版管理</a> >
                    <span class="second-level">  模板设置</span>
                </div>
            </div>

            <div class="row choice-template margin-clear">
                <div class="col-sm-12 ">
                    <div class="row template-item">
                        <div class="col-sm-12 title">
                            <div class="pull-left"> 店招设置</div>
                            <div class="pull-right">
                                <a class="btn check-index" href="{{ url('shop/'.$shop->id)  }}"
                                   target="_blank">查看店铺首页</a>
                            </div>
                        </div>
                        <div class="col-sm-12 item-wrap">
                            <form class="form-horizontal ajax-form"
                                  action="{{ url('api/v1/personal/model/signature') }}"
                                  method="post"
                                  data-help-class="col-sm-push-2 col-sm-10"
                                  data-done-url="{{ url('personal/model/model-edit') }}"
                                  autocomplete="off">

                                <input type="hidden" name="id"
                                       value="{{ !empty($shop->ShopSignature)?$shop->ShopSignature->id:''}}"/>
                                <div class="row">
                                    <div class="col-sm-12 item upload-banner-item">上传店招
                                        <button data-height="120" data-width="1920" data-target="#cropperModal"
                                                data-toggle="modal"
                                                data-loading-text="图片已达到最大数量" type="button"
                                                id="pic-upload" data-name="signature" data-css-width="1230"
                                                data-css-margin-left="-120" class="upload-shopimg">
                                            选择文件
                                        </button>
                                        <div class="shop-img  image-preview" id="shop-img">
                                            <img src="{{ $shop->ShopSignature?$shop->ShopSignature->signature_url:asset('images/signature.jpg') }}">
                                            <input id="text-input"
                                                   style="color:{{ $shop->ShopSignature?$shop->ShopSignature->color:'#000000' }}"
                                                   name="text" type="text" placeholder="可编辑文字"
                                                   value="{{ $shop->ShopSignature?$shop->ShopSignature->text:'' }}"
                                                   autofocus="autofocus" maxlength="30"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 item prompt">
                                        提示：建议上传尺寸1920*120的jpg图片，上面预览的是图片的中间位置，可在店招上编辑文字（最多30个字符），文字显示范围不能超出图片
                                    </div>
                                    <div class="col-sm-12 font-color item">
                                        字体颜色
                                        <a id="selected-color" class="btn btn-mini dropdown-toggle padding-clear"
                                           data-toggle="dropdown" style="background-color: #000"></a>
                                        <ul class="dropdown-menu text-center">
                                            <li>
                                                <div> 字体颜色</div>
                                                <div id="colorpalette"></div>
                                            </li>
                                        </ul>
                                        <input type="hidden" id="color-input" name="color"
                                               value="{{ $shop->ShopSignature?$shop->ShopSignature->color:'#000000' }}"/>
                                    </div>
                                    <div class="col-sm-12 item">
                                        <button type="submit" class="btn btn-success ">提交</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="row template-item">
                        <div class="col-sm-12 title"> 广告轮播图设置</div>
                        <div class="col-sm-12 item-wrap">
                            <form class="form-horizontal ajax-form"
                                  action="{{ url('api/v1/personal/model/shop-advert') }}"
                                  method="post"
                                  data-help-class="col-sm-push-2 col-sm-10"
                                  data-done-url="{{ url('personal/model/model-edit') }}"
                                  autocomplete="off">
                                <div class="row">
                                    <div class="col-sm-12 item prompt">提示：建议上传尺寸1200*200的jpg图片</div>
                                    @if(!empty($shop->shopHomeAdverts))
                                        @foreach($shop->shopHomeAdverts as $key => $shopHomeAdvert)
                                            <div class="col-sm-3 item upload-banner-item">
                                                <div class="option option-upload clearfix">
                                                    <div class=" option-banner image-preview">
                                                        <img src="{{ $shopHomeAdvert->image_url }}"/>
                                                        <a href="javascript:;" class="templet-modal"
                                                           data-target="#templetModal" data-width="1200px"
                                                           data-toggle="modal">点击预览</a>
                                                    </div>
                                                    <span class="pull-left name"> 广告轮播图{{ $key+1 }}</span>
                                                    <input type="hidden" class="cropper-hidden" name="images[image][]"
                                                           value="{{ $shopHomeAdvert->image_url }}"/>
                                                    <button data-height="200" data-width="1200"
                                                            data-target="#cropperModal"
                                                            data-toggle="modal"
                                                            class="pull-right  upload-shopimg" type="button"
                                                            id="pic-upload" data-name="images[image][]">
                                                        选择文件
                                                    </button>

                                                </div>
                                                <div class="option">
                                                    <input type="text" name="images[url][]"
                                                           value="{{ $shopHomeAdvert->url }}"
                                                           placeholder="输入图片链接"/>
                                                </div>
                                                <div class="option">
                                                    <label>
                                                        <input type="checkbox"
                                                               class="banner-checkbox" {{ $shopHomeAdvert->status==1?'checked':'' }} >
                                                        <input class="status" type="hidden" name="images[status][]"
                                                               value="{{ $shopHomeAdvert->status }}"/>
                                                        <input type="hidden" name="images[id][]"
                                                               value="{{ $shopHomeAdvert->id }}"/>
                                                        显示</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if(count($shop->shopHomeAdverts)<5)
                                        @for($i=0;$i<(5-count($shop->shopHomeAdverts));$i++)
                                            <div class="col-sm-3 item upload-banner-item">
                                                <div class="option option-upload clearfix">
                                                    <div class=" option-banner image-preview">
                                                        <img src="{{ count($shop->shopHomeAdverts)==0&&$i==0?asset('images/shop-banner.jpg'):asset('images/default-shop-banner-edit.png') }}"/>
                                                        <a href="javascript:;" class="templet-modal"
                                                           data-target="#templetModal" data-width="1200px"
                                                           data-toggle="modal">点击预览</a>
                                                    </div>
                                                    <span class="pull-left name"> 广告轮播图{{ count($shop->shopHomeAdverts)+$i+1 }}</span>
                                                    <input type="hidden" class="cropper-hidden" name="images[image][]"
                                                           value=""/>
                                                    <button data-height="200" data-width="1200"
                                                            data-target="#cropperModal"
                                                            data-toggle="modal"
                                                            class="pull-right  upload-shopimg" type="button"
                                                            id="pic-upload" data-name="images[image][]">
                                                        选择文件
                                                    </button>

                                                </div>
                                                <div class="option">
                                                    <input type="text" name="images[url][]"
                                                           value="{{ count($shop->shopHomeAdverts)==0&&$i==0?'http://dingbaida.com/goods/1065':'' }}"
                                                           placeholder="输入图片链接"/>
                                                </div>
                                                <div class="option">
                                                    <label>
                                                        <input type="checkbox" class="banner-checkbox">
                                                        <input class="status" type="hidden" name="images[status][]"
                                                               value="0"/>
                                                        显示</label>
                                                </div>
                                            </div>
                                        @endfor
                                    @endif
                                    <div class="col-sm-12 item">
                                        <button type="submit" class="btn btn-success btn-submit">提交</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row template-item">
                        <div class="col-sm-12 title">店铺推荐</div>
                        <div class="col-sm-12 recommend">
                            <button class="btn my-goods" data-target="#myModal" data-toggle="modal">选择文件</button>
                            <span class="prompt ">已选商品<span
                                >{{ count($shop->shopRecommendGoods) }}</span>个</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
@section('js-lib')
    @parent
    <script src="{{ asset('js/bootstrap-colorpalette.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            //颜色选择器
            $('#selected-color').css('background-color', "{{ $shop->ShopSignature && $shop->ShopSignature->color?$shop->ShopSignature->color:'#000000' }}");
            $('#colorpalette').colorPalette()
                .on('selectColor', function (e) {
                    $('#selected-color').css('background-color', e.color);
                    $('#color-input').val(e.color);
                    $('#text-input').css('color', e.color);
                });

            //广告轮播图状态选择
            $('.banner-checkbox').click(function () {
                var obj = $(this);
                if (obj.is(':checked')) {
                    obj.siblings('.status').val(1);
                } else {
                    obj.siblings('.status').val(0);

                }
            });
        });


    </script>
@stop