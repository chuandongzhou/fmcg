@extends('index.menu-master')
@section('subtitle' , '订单下载模版')
@include('includes.shipping-address-map')
@section('top-title')
    <a href="{{ url('order-sell') }}">订单管理</a> &rarr;
    订单打印模版
@stop
@section('right')
    <div class="row">
        <div class="col-sm-12 templet-title">订单打印默认模板: <b
                    class="print-default">{{ cons()->valueLang('order.templete', $defaultTempleteId) }}</b>
            <button class="btn btn-warning display" data-target="#templeteModal" data-toggle="modal"
                    data-src="{{ asset('images/order-templetes/templete_' . $defaultTempleteId . '.png') }}">预览
            </button>
            <a class="btn btn-default" href="javascript:history.back()">返回</a>
        </div>
        <div class="col-sm-12">
            <div>模板选择:</div>
        </div>
        <div class="col-sm-12 templet-list">
            <div class="row">
                @foreach(cons()->valueLang('order.templete') as $templeteId => $templeteName)
                    <div class="col-sm-4 item {{ $templeteId == $defaultTempleteId ? 'checked' : '' }}">
                        <h4 class="templete-name">{{ $templeteName }}</h4>
                        @if($templeteId==cons('order.templete.third'))
                            <img src="{{ asset('images/order-templetes/templete_' . cons('order.templete.first') . '_s.png') }}">
                        @elseif($templeteId==cons('order.templete.fourth'))
                            <img src="{{ asset('images/order-templetes/templete_' .  cons('order.templete.second') . '_s.png') }}">
                        @else
                            <img src="{{ asset('images/order-templetes/templete_' . $templeteId . '_s.png') }}">
                        @endif

                        <div class="buttons">
                            <a class="btn btn-primary ajax no-prompt check-templete" data-method="post"
                               data-done-then="none"
                               data-url="{{ url('api/v1/order/templete/' . $templeteId)  }}">选择</a>
                            @if($templeteId==cons('order.templete.third'))
                                <button class="btn btn-warning" data-target="#templeteModal" data-toggle="modal"
                                        data-src="{{ asset('images/order-templetes/templete_' .cons('order.templete.first') . '.png') }}">
                                    预览
                                </button>
                            @elseif($templeteId==cons('order.templete.fourth'))
                                <button class="btn btn-warning" data-target="#templeteModal" data-toggle="modal"
                                        data-src="{{ asset('images/order-templetes/templete_' .  cons('order.templete.second') . '.png') }}">
                                    预览
                                </button>
                            @else
                                <button class="btn btn-warning" data-target="#templeteModal" data-toggle="modal"
                                        data-src="{{ asset('images/order-templetes/templete_' . $templeteId . '.png') }}">
                                    预览
                                </button>
                            @endif


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
            var templeteModal = $('#templeteModal');
            templeteModal.on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget),
                        src = parent.data('src'),
                        templeteImg = templeteModal.find('.templete-img');
                templeteImg.attr('src', src);
            });
            $('.check-templete').on('always.hct.ajax', function () {
                var obj = $(this),
                        parent = $(this).closest('.item'),
                        templeteName = parent.find('.templete-name').html(),
                        displaySrc = obj.next().data('src');
                parent.addClass('checked').siblings().removeClass('checked');
                $('.print-default').html(templeteName);
                $('.display').data('src', displaySrc);
            })
        })
    </script>
@stop