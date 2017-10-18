<div class="modal fade in" id="salesmanModal" tabindex="-1" role="dialog" aria-labelledby="salesmanModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:450px">
            <div class="modal-header choice-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <div class="modal-title forgot-modal-title" id="myModalLabel">
                    <span>指派业务员</span>
                    <span class="salesman-tips">
                        <i class="iconfont icon-tishi orange"></i>
                    只显示未被指派的业务员!
                    </span>
                </div>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-sm-6">
                        <span class="prompt">供应商 ：</span><span id="name"></span>
                    </div>
                    <div class="col-sm-6">
                        <span class="prompt">平台账号 ：</span> <span id="account"></span>
                    </div>
                </div>
                <div class="row clerk-appoint">
                    <div class="col-sm-12 search-clerk">
                        <input type="text" id="salesmanName" class="control" placeholder="业务员名称"/>
                        <button class="btn btn-blue-lighter salesman-search control">搜索</button>
                    </div>
                    <div class="col-sm-12">
                        <ul class="clerk-list-wrap">

                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer middle-footer ">
                <button class="btn btn-blue-lighter clerk-submit" type="submit">提交并通过</button>
            </div>
        </div>
    </div>
</div>
@section('js')
    <script>
        $(function () {
            var modal = $('div#salesmanModal');
            modal.on('shown.bs.modal', function (parent) {
                var obj = $(parent.relatedTarget),
                        name = obj.data('name'),
                        supplier_id = obj.data('supplier_id'),
                        account = obj.data('account');
                $(this).find('#name').html(name);
                $(this).find('#account').html(account).data('supplier_id', supplier_id);
                salesmanList()
            }).on('hidden.bs.modal', function () {
                $('p.ajax-error').remove();
            });

            //业务员列表
            function salesmanList(salesmanName) {
                salesman_name = salesmanName || '';
                $.ajax({
                    url: site.api('shop/salesman'),
                    method: 'get',
                    data: {'salesman_name': salesman_name}
                }).done(function (data) {
                    html = '';
                    for (var i = 0; i < data.salesman.length; i++) {
                        html += '<li data-id=' + data.salesman[i].id + '> ' + data.salesman[i].name + '<i class="iconfont icon-qiyong pull-right"></i></li>'
                    }
                    $(".clerk-list-wrap").html(html);
                });
            }

            //点击事件
            $(".clerk-list-wrap").on('click', 'li', function () {
                $(this).addClass("active").siblings().removeClass("active");
            });

            $('button.clerk-submit').click(function () {
                var self = $(this);
                var salesman_id = $(".clerk-list-wrap").find('li.active').data('id');
                var supplier_id = modal.find('#account').data('supplier_id');
                if (!salesman_id) {
                    alert('请选择业务员');
                    return
                }
                $.ajax({
                    url: site.api('business/trade-request/pass'),
                    method: 'post',
                    data: {'salesman_id': salesman_id, "supplier_id": supplier_id}
                }).done(function (data) {
                    successMeg(data['message']);
                    location.reload();
                    modal.hide()
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    var json = jqXHR['responseJSON'];
                    if (json) {
                        $(self).addClass('error-color disabled').html(json['message']);
                        setTimeout(function () {
                            $(self).removeClass('error-color disabled').html('提交并通过')
                        }, 2000);
                    }
                })
            });
            $('button.salesman-search').click(function () {
                var salesmanName = $(modal).find('#salesmanName').val();
                salesmanList(salesmanName)
            });

            $("[data-toggle='popover']").popover();
        })
    </script>
@endsection