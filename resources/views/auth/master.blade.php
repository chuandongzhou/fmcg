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
    <footer class="panel-footer footer login-footer" >
        <div class="container text-center text-muted">
            <div class="row">
                <div class="col-sm-5  col-sm-push-2 text-left" >
                    <p>Copyright © 成都订百达科技有限公司 </p>
                    <p>蜀ICP备15031748号-1</p></div>
                <div class="col-sm-6 text-left" >
                    <p>联系方式：028-83233316     13980537732</p>
                    <p>联系地址：成都市高新区天华路299号英郡三期6栋1单元905</p>
                </div>
            </div>
        </div>
    </footer>
@stop