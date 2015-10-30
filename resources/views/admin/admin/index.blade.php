@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/admin') }}" data-help-class="col-sm-push-2 col-sm-10">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>管理员ID</th>
                <th>管理员账号</th>
                <th>姓名</th>
                <th>所属角色</th>
                <th>状态</th>
                <th class="text-nowrap">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
                <tr>
                    <td><input type="checkbox" class="child" name="ids[]" value="{{$admin->id}}"/> </td>
                    <td>{{$admin->id}}</td>
                    <td>{{$admin->name}}</td>
                    <td>{{$admin->realname}}</td>
                    <td>{{$admin->role->name}}</td>
                    <td>
                        {{ $admin->status ? '启用' : '禁用' }}
                    </td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{ url('admin/admin/'.$admin->id.'/edit') }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <button type="button" class="btn btn-danger ajax" data-method="delete"
                                    data-url="{{ url('admin/admin/'.$admin->id) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
        <div class="btn-group btn-group-xs" role="group">
            <input type="checkbox" id="parent" class="checkbox-inline"/>
            <label for="parent">全选</label>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="delete"
                    data-url="{{ url('admin/admin/batch') }}">
                <i class="fa fa-trash-o"></i> 删除
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-primary ajax" data-method="put" data-data='{"status":1}'
                    data-url="{{ url('admin/admin/switch') }}">
                <i class="fa fa-adjust"></i> 启用
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="put" data-data='{"status":0}'
                    data-url="{{ url('admin/admin/switch') }}">
                <i class="fa fa-trash-o"></i> 禁用
            </button>
        </div>
    </form>
    {!! $admins->render() !!}
@stop
@section('js')
    @parent
    <script type="text/javascript" src="{{ asset('js/pingpp-pc.js') }}"></script>
    <script type="text/javascript">
        $(function () {

            var charge = { "id": "ch_X9W1O440WPW1T8K88GXHGazT", "object": "charge", "created": 1446186797, "livemode": false, "paid": false, "refunded": false, "app": "app_1Gqj58ynP0mHeX1q", "channel": "yeepay_wap", "order_no": "97ba874948d7", "client_ip": "192.168.2.66", "amount": 1, "amount_settle": 0, "currency": "cny", "subject": "Your Subject", "body": "Your Body", "extra": { "product_category": 1, "identity_id": "your identity_id", "identity_type": 1, "terminal_type": 1, "terminal_id": "your terminal_id", "user_ua": "your user_ua", "result_url": "http:\/\/www.yourdomain.com\/result" }, "time_paid": null, "time_expire": 1446273197, "time_settle": null, "transaction_no": null, "refunds": { "object": "list", "url": "\/v1\/charges\/ch_X9W1O440WPW1T8K88GXHGazT\/refunds", "has_more": false, "data": [] }, "amount_refunded": 0, "failure_code": null, "failure_msg": null, "metadata": {}, "credential": { "object": "credential", "yeepay_wap": { "merchantaccount": "YB01000000144", "encryptkey": "KlP+7eLYMCoWM3wsYjWw2CCv1ROygstMCOYL3\/EwnbVr4r5rHNlkNmwBKxwFwNgipYyf2pV\/t9Sor0XYX1lCkWcuF+TExcoqK5mW8FrbCif84NGZz0WzfJ\/oJq9uf60XTIjE1lKrhsYgeBd2dDlovfYW4aoZTmnEChXEtf901T8=", "data": "5PhAJN0NvycVAuA\/KZHTDtWiCJb+XPDPjKb930z0yQVw4FrnIvlyF+LgX4PfZkVyeE5TeuyJI\/LmJUFBV3+v68ZmKun0aKpG\/PsFuuehM6BdQIVQtYAOYC\/+frDum3sxuvtNjWLo1P6UdcRdvFEJGkg46KSTONZzMA5nT5vHnAa0SNaXkQ+t0610hT\/IJRtEV9dLZBCUlZbhESQvp6vj20o+QHW19qVFVH04WvVwwoKE2vmd5hf3Yj\/4tKuWF4sas6vRrebap8dM3Fv\/NLLmGa62dhnmF1bYNWQkbXl22N6\/blo7QMvwTEnVXU2sh9uAzKXuQJp4llDh2nZxgqRzNXNNJrVg5QAG2hHOJ4WhMJU6PDarFlCtE9IR6bu+bjvMzb+nMLKlQptfttwIiFsm\/B68GanBYE3Pov99o6C3azbBTkf\/quySVnCpp3qIfx\/yvQ9CDajv7cUyeijnBGFsvg7quhQXkJdEHtdtQ1Y3RrlhHtyQH2uZq\/O+1tgqHqgEXrK7qbmZOS+gf9cacKBrSdO643FuraLvmi0i\/HkedZeqwubc\/11ZPwZiLHODlycFMoiKh9WyGZf84pIu4nl0RkrnIIVen7l+5lgBNlJLfvzCOSTVb2iZcCpQPcBFa7bxqAfLWHAgCDKmbz0mkfp2pw07L6\/2nlVjgiPC68M5wji0h8URpCtWK+KW8M9h2kGQhpfGe8ShoFCgu0Fep1YZoO+PiYNEk4bhMoIkBpm1DRFkBFYJjEldI7B3l9iku0jeFeDKmfkIbAT471tKiaBWzYS8wtXK9iWY+98ZDg0+nkG+9WWd1\/3Z8uyc0x02l3HOpa9C12LUxzEgTDTMK8dVkPZoK+ypdAgkBFLzRFvEGASx6EdS+f1HKXhZM4Ry\/MN7TNPiJbivRRoFPWDSZj9WBKinRdjGs2zY\/y3uomKTHi6rM9I5hnvaCTnt5pRpIbzvg4cZ5LdX959sTgtTBZS+aA==", "mode": "live" } }, "description": null };

            pingpp.createPayment(charge, function(result, err){
                console.log(result);
            });

            onCheckChange('#parent', '.child');
        })
    </script>
@stop