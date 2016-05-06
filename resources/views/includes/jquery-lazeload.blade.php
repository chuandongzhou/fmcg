@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/lib/jquery/lazyload/jquery.lazyload.min.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $("img.lazy").lazyload({
                effect: "fadeIn"
            });
        });
    </script>
@stop