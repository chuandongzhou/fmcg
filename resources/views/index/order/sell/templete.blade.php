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
            <button class="btn btn-blue-lighter display" data-target="#templeteModal" data-toggle="modal"
                    data-src="{{ asset('images/order-templetes/templete_' . $defaultTempleteId . '.png') }}">预览
            </button>
            <a class="btn go-back" href="javascript:history.back()">返回</a>
            <span class="prompt prompt-last">您可以在下列中选择订单打印模板，点击模板图片可预览大图</span>
        </div>
        <div class="col-sm-12 templet-list">
            <div class="row">

                @foreach(cons()->valueLang('order.templete') as $templeteId => $templeteName)
                <div class="col-sm-4 item">
                    <div class="mask-panel">
                        @if($templeteId==cons('order.templete.third'))
                            <img src="{{ asset('images/order-templetes/templete_' . cons('order.templete.first') . '_s.png') }}">
                        @elseif($templeteId==cons('order.templete.fourth'))
                            <img src="{{ asset('images/order-templetes/templete_' .  cons('order.templete.second') . '_s.png') }}">
                        @else
                            <img src="{{ asset('images/order-templetes/templete_' . $templeteId . '_s.png') }}">
                        @endif
                            <a href="javascript:;" class="templet-modal" data-target="#templetModal" data-toggle="modal">点击预览</a>
                    </div>
                    <div class="choice-item">
                        <label><input data-url="{{ url('api/v1/order/templete/' . $templeteId)  }}" class="select-templet {{ $templeteId }}" type="radio" name="templet" {{ $templeteId == $defaultTempleteId ? 'checked disabled="disabled"' : '' }} />{{ $templeteName }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="modal fade" id="templeteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width:980px;">
            <div class="modal-content" style="width:980px;height:555px;margin:auto">
                <div class="modal-body text-center" style="padding:0;">
                    <img src="" class="templete-img">
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            //模板选择成功事件
            var templeteModal = $('#templeteModal');
            templeteModal.on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget),
                        src = parent.data('src'),
                        templeteImg = templeteModal.find('.templete-img');
                templeteImg.attr('src', src);
            });
            //选择模板
            $('.select-templet').click(function(){
                $('.select-templet').each(function(){
                    $(this).removeAttr('disabled');
                });
                $(this).attr("disabled",true);
                var url = $("input[type=radio]:checked").data('url');
                $.ajax({
                    url: url,
                    method: 'post'
                }).done(function () {
                    $('.success-meg-content').html('模板修改成功');
                    showSuccessMeg();

                }).fail(function(){
                    $('.success-meg-content').html('模板修改失败');
                    $(".popup").css({"opacity":"1","top":"20px"});
                    setTimeout(function(){
                        $(".popup").css({"opacity": "0","top":"-150px"});
                    },3000);
                    $('.select-templet').each(function(){
                        $(this).removeClass('checked').removeAttr('disabled');
                    });
                    $('.{{ $defaultTempleteId }}').addClass('checked').attr("disabled",true);

                });
            });

        })
    </script>
@stop