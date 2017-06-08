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
            $('.datetimepicker').each(makeDate());
        });
        function makeDate() {
            return function (i, obj) {
                var obj = $(obj), format = obj.data('format') || 'YYYY-MM-DD HH:mm:ss', minDate = obj.data('min-date'), maxDate = obj.data('max-date');
                if (minDate || maxDate) {
                    var date = new Date(), minDateContent, maxDateContent;
                    var year = date.getFullYear();
                    var month = date.getMonth() + 1;
                    var day = date.getDate();
                }
                if (minDate && typeof minDate != 'boolean') {
                    minDateContent = new Date(obj.data('min-date'));
                } else if (minDate && typeof minDate == 'boolean') {
                    minDateContent = new Date(year + '-' + month + '-' + day);
                }
                if (maxDate && typeof maxDate != 'boolean') {
                    maxDateContent = new Date(maxDate);
                } else if (maxDate && typeof maxDate == 'boolean') {
                    console.log(year + '-' + month + '-' + day);
                    maxDateContent = new Date(year + '-' + month + '-' + day);
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
                    minDate: minDate ? minDateContent : false,
                    useCurrent: maxDate ? false : true,
                    maxDate: maxDate ? maxDateContent : false,
                    widgetPositioning: {
                        horizontal: 'auto',
                        vertical: 'bottom'
                    }
                });
            }
        }
    </script>
@stop