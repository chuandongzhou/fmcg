@section('css')
    @parent
    <link href="{{ asset('js/lib/jquery/jstree/themes/default/style.min.css') }}" rel="stylesheet">
@stop

@section('js-lib')
    @parent
    <script src="{{ asset('js/lib/jquery/jstree/jstree.min.js') }}"></script>
@stop