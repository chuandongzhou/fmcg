@extends('index.menu-master')
@section('subtitle', '个人中心-提现账号')

@section('right')
    <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/delivery-man/' . $deliveryMan->id) }}"
          method="{{ $deliveryMan->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('personal/delivery-man') }}">
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
                <a href="javascript:history.go(-1)" class="btn btn-cancel">取消</a>
            </div>
        </div>
    </form>
    @parent
@stop
