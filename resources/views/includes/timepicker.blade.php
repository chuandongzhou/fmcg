@section('css')
    @parent
    <link href="{{ asset('js/lib/jquery/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
          rel="stylesheet">
@stop

@section('js-lib')
    @parent
    <script src="{{ asset('js/lib/jquery/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/lib/jquery/moment/locales.min.js') }}"></script>
    <script src="{{ asset('js/lib/jquery/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $('.datetimepicker').datetimepicker({
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                },
                format: 'YYYY-MM-DD HH:mm:ss',
            });
        })
    </script>
@stop