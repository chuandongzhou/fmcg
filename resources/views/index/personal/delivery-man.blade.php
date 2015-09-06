@extends('index.manage-left')
@section('subtitle', '个人中心-提现账号')

@section('right')
    <div class="col-sm-10 personal-center personal-center-tab2">

        <div class="row">
            <div class="col-sm-12 switching">
                <a href="#" class="btn ">商家信息</a>
                <a href="#" class="btn active">体现账号</a>
                <a href="#" class="btn">人员管理</a>
                <a href="#" class="btn">配送人员</a>
                <a href="#" class="btn">修改密码</a>
                <a href="#" class="btn">账号余额</a>
            </div>
        </div>
        <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/delivery-man/' . $deliveryMan->id) }}"
              method="{{ $deliveryMan->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10" data-done-url="{{ url('personal/delivery-man') }}">
            <div class="form-group row">
                <label class="col-sm-2 control-label" for="name">姓名:</label>

                <div class="col-sm-10 col-md-6">
                    <input class="form-control" id="name" name="name" placeholder="请输入姓名"
                           value="{{ $deliveryMan->name }}"
                           type="text">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label" for="phone">手机号码:</label>

                <div class="col-sm-10 col-md-6">
                    <input class="form-control" id="phone" name="phone" placeholder="请输入手机号码"
                           value="{{ $deliveryMan->phone }}"
                           type="text">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-push-2 col-sm-10">
                    <button class="btn btn-primary" type="submit">提交</button>
                </div>
            </div>
        </form>
    </div>
    @parent
@stop
