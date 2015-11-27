@extends('master')

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

                <p>成都市高新区天府大道中段1388号美年广场A座1248号&nbsp;&nbsp;13829262065(霍女士)</p>
            </div>
        </div>
    </footer>
@stop