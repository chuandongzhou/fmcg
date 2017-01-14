@section('body')
    <div class="modal fade" id="customerMapModal" tabindex="-1" role="dialog" aria-labelledby="customerMapModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <div class="modal-title dis-modal-title" id="myModalLabel">成交客户分布图</div>
                </div>
                <div class="modal-body">
                    <ul class="modal-list-left">
                        <li>{{ $startDay }} &nbsp;&nbsp; 至&nbsp;&nbsp; {{ $endDay }}</li>
                        <li>推广员 : <span class="promoter-name"></span>&nbsp;&nbsp;&nbsp;&nbsp;推广码 : <span class="spreading-code"></span></li>
                        <li>成交金额 : <span class="finish-amount"></span></li>
                        <li><i></i> 成交客户数 : <span class="user-count"></span></li>
                    </ul>
                    <div id="myChart" class="chart" style="height:520px"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal -->
    </div>
    @parent
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/echarts-all-3.js"></script>
    <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/map/js/china.js"></script>
@stop
@section('js')
    <script type="text/javascript">
        $('#customerMapModal').on('shown.bs.modal', function (e) {
            function randomData() {
                return Math.round(Math.random() * 1000);
            }

            var chart = echarts.init(document.getElementById('myChart')),
                parent = $(e.relatedTarget),
                name = parent.data('name'),
                spreadingCode = parent.data('spreadingCode'),
                finishAmount = parent.data('finishAmount'),
                userCount = parent.data('userCount');

            $('.promoter-name').html(parent.data('name'));
            $('.spreading-code').html(parent.data('spreadingCode'));
            $('.finish-amount').html(parent.data('finishAmount'));
            $('.user-count').html(parent.data('userCount'));


            chart.setOption({
                tooltip: {},
                visualMap: {
                    min: 0,
                    max: 1500,
                    left: 'left',
                    top: 'bottom',
                    text: ['高', '低'],
                    calculable: true
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: ['']
                },
                selectedMode: 'single',
                series: [
                    {
                        name: '成交客户数',
                        type: 'map',
                        mapType: 'china',
                        label: {
                            normal: {
                                show: true
                            },
                            emphasis: {
                                show: true
                            }
                        },
                        data: parent.data('shops')
                    }
                ]
            });
        })
    </script>
@stop