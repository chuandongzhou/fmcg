@extends('index.menu-master')
@section('subtitle', '个人中心-配送人员')
@section('top-title', '个人中心->配送人员')
@section('right')
    <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/delivery-man/' . $deliveryMan->id) }}"
          method="{{ $deliveryMan->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('personal/delivery-man') }}" autocomplete="off">


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
            <label class="col-sm-2 control-label" for="user_name">POS机登录账号:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="user_name" name="user_name" placeholder="请输入POS机登录账号"
                       value="{{ $deliveryMan->user_name }}"
                       type="text">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="password">POS机登录密码:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="password" name="password" placeholder="请输入POS机登录密码"
                       value=""
                       type="password">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="password_confirmation">POS机密码确认:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="password_confirmation" name="password_confirmation" placeholder="请重复输入POS机登录密码"
                       value=""
                       type="password">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 control-label" for="name">POS机编号:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="pos_sign" name="pos_sign" placeholder="请输入POS机编号"
                       value="{{ $deliveryMan->pos_sign }}"
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
