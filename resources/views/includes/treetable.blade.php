@section('css')
    @parent
    <link href="{{ asset('js/lib/jquery/treetable/css/jquery.treetable.css') }}" rel="stylesheet">
    <link href="{{ asset('js/lib/jquery/treetable/css/jquery.treetable.theme.default.css') }}" rel="stylesheet">
@stop

@section('js-lib')
    @parent
    <script src="{{ asset('js/lib/jquery/treetable/jquery.treetable.js') }}"></script>
@stop