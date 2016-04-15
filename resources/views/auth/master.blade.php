@extends('master')

@section('js-lib')
    <script src="{{ asset('js/index.js') }}"></script>
@stop

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop


@section('footer')
    <footer class="panel-footer footer login-footer">
        <div class="container text-center text-muted">
            <div class="row">
                <div class="col-sm-2 col-sm-push-1 text-right qr-code">
                    <img src="{{ asset('images/qr-code.png') }}" class="qr-code-img">
                    <a class="qr-code-panel">
                        <img src="{{ asset('images/qr-code.png') }}">

                        <p class="text-right">APP下载</p>
                    </a>
                </div>
                <div class="col-sm-3 col-sm-push-1  text-left">
                    <p>Copyright {!! cons('system.company_name') !!} </p>

                    <p>{!! cons('system.company_record') !!}</p></div>
                <div class="col-sm-5 col-sm-push-1 text-left">
                    <p>{{ cons('system.company_tel') . ' ' . cons('system.company_mobile') }}</p>

                    <p>联系地址：{{ cons('system.company_addr') }}</p>
                </div>
            </div>
        </div>
    </footer>
@stop

@section('js')
    <script type="text/javascript">
        $(".qr-code-panel").hover(function () {
            $(".qr-code-img").css("display", "inline-block");
        }, function () {
            $(".qr-code-img").css("display", "none");
        })
    </script>
@stop