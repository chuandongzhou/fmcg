@section('js-lib')
    @parent
    <script src="{{ asset('js/lib/jquery/easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/stepBar.js') }}"></script>
    <script type="text/javascript">
        $(function () {

            stepBar.init("stepBar", {
                step: {{$order['step_num']}},
                change: true,
                animation: true
            });

        });
    </script>
@endsection