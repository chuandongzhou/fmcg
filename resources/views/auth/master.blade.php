@extends('master')

@section('js-lib')
    <script src="{{ asset('js/index.js') }}"></script>
@stop

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <style>
        body {
            margin-bottom: 120px
        }</style>
@stop


@section('footer')
    <footer class="panel-footer login-footer guide-footer footer">
        <div class="container text-center text-muted">
            <div class="text-right qr-code">
                <img src="{{ asset('images/qr-code.png') }}">

                <p class="text-center">APP下载</p>
            </div>
            <div class="txt-content">
                <p class="text-left sign">Copyright{!!  cons('system.company_name') . '&nbsp;&nbsp;' . cons('system.company_record') !!}</p>

                <p>{{ cons('system.company_addr') }}&nbsp;&nbsp;联系方式：{{ cons('system.company_tel') . ' ' . cons('system.company_mobile') }}</p>
            </div>
        </div>
    </footer>
@stop