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
            $('.datetimepicker').each(function (i, obj) {
                var obj = $(obj), format = obj.data('format') || 'YYYY-MM-DD HH:mm:ss';
                if (obj.data('min-date')) {
                    var date = new Date();
                    var year = date.getFullYear();
                    var month = date.getMonth() + 1;
                    var day = date.getDate() +  (parseInt(obj.data('min-date')) -1) ;
                }

                obj.datetimepicker({
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
                    locale: 'zh-cn',
                    format: format,
                    minDate: obj.data('min-date')? new Date(year + '-' + month + '-' + day) : false,
                    widgetPositioning: {
                        horizontal: 'auto',
                        vertical: 'bottom'
                    }
                });
            });
        });
    </script>
@stop