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
                <p class="text-left sign">Copyright2015成都订百达科技有限公司 蜀ICP备15031748号-1</p>

                <p>成都市高新区天华路299号英郡三期6栋1单元905&nbsp;&nbsp;联系方式：13980537732（马先生）</p>
            </div>
        </div>
    </footer>
@stop