@extends('index.menu-master')
@section('subtitle' , '订单下载模版')
@include('includes.shipping-address-map')
@include('includes.templet-model')

@section('top-title')
    <a href="{{ url('order-sell') }}">订单管理</a> >
    <span class="second-level">订单打印模版</span>
@stop
@section('right')
    @include('includes.success-meg')
    <div class="row print-templet margin-clear">
        <div class="col-sm-12 templet-title">
            <span class="prompt">已选订单打印默认模板:</span>
            <b class="print-default">{{ cons()->valueLang('order.templete', $defaultTempleteId) }}</b>
            <a href="javascript:" class="btn btn-blue-lighter" data-target="#templetModal" data-toggle="modal"
               data-src="{{ asset('images/order-templetes/templete_' . $defaultTempleteId . '.png') }}">预览
            </a>
            <a class="btn go-back" href="javascript:history.back()">返回</a>
            <span class="prompt prompt-last">您可以在下列中选择订单打印模板，点击模板图片可预览大图</span>
        </div>
        <div class="col-sm-12 templet-list">
            <div class="row">

                @foreach(cons()->valueLang('order.templete') as $templeteId => $templeteName)
                    <div class="col-sm-4 item">
                        <div class="mask-panel">
                            <img src="{{ asset('images/order-templetes/templete_' . $templeteId . '_s.png') }}">
                            <a href="javascript:" class="templet-modal" data-target="#templetModal" data-toggle="modal"
                               data-src="{{ asset('images/order-templetes/templete_' . $templeteId . '.png') }}">点击预览</a>
                        </div>
                        <div class="choice-item">
                            <label><input data-url="{{ url('api/v1/order/templete/' . $templeteId)  }}"
                                          class="select-templet {{ $templeteId }}" type="radio"
                                          name="templet" {{ $templeteId == $defaultTempleteId ? 'checked disabled="disabled"' : '' }} />{{ $templeteName }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $(".popup").css({"opacity": "0", "top": "-150px"});
            //选择模板
            $('.select-templet').change(function () {
                $('body').append('<div class="loading"> <img src="'+site.url("images/new-loading.gif")+'" /> </div>');
                var obj = $(this);
                $('.select-templet').each(function () {
                    $(this).prop('disabled', false);
                });
                obj.prop("disabled", true);
                $.ajax({
                    url: obj.data('url'),
                    method: 'post'
                }).done(function () {
                    successMeg('模板修改成功');
                }).fail(function () {
                    $('body').find('.loading').remove();
                    $('.success-meg-content').html('模板修改失败');
                    $(".popup").css({"opacity": "1", "top": "20px"});
                    setTimeout(function () {
                        $(".popup").css({"opacity": "0", "top": "-150px"});
                    }, 3000);
                    $('.select-templet').each(function () {
                        $(this).removeClass('checked').removeAttr('disabled');
                    });
                    $('.{{ $defaultTempleteId }}').addClass('checked').attr("disabled", true);
                }).always(function () {
                    setTimeout(function () {
                        location.reload();
                    }, 3000);

                });
            });
        })
    </script>
@stop