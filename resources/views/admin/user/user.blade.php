@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $user->id ? 'put' : 'post' }}"
          action="{{ url('admin/user/' . $user->id) }}" data-help-class="col-sm-push-2 col-sm-10">

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">用户名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="username" name="user_name" placeholder="请输入用户名"
                       value="{{ $user->user_name }}">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">密码</label>

            <div class="col-sm-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码">
            </div>
        </div>

        <div class="form-group">
            <label for="password-confirmation" class="col-sm-2 control-label">重复密码</label>

            <div class="col-sm-4">
                <input type="password" class="form-control" id="password-confirmation" name="password_confirmation"
                       placeholder="请重复输入密码">
            </div>
        </div>

        <div class="form-group">
            <label for="nickname" class="col-sm-2 control-label">{{ cons()->valueLang('user.type', $typeId) }}
                姓名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="nickname" name="nickname" placeholder="请输入姓名"
                       value="{{ $user->nickname }}">
            </div>
        </div>

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">地址</label>

            <div class="col-sm-2">
                <select class="form-control" name="province_id">
                    <option value="0">省</option>
                    <option value="1">四川</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select class="form-control" name="city_id">
                    <option value="0">市</option>
                    <option value="11">成都</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select class="form-control" name="district_id">
                    <option value="0">区</option>
                    <option value="111">高新区</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select class="form-control" name="street_id">
                    <option value="0">街道</option>
                    <option value="1111">天府五街</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="col-sm-2 control-label">详细地址</label>

            <div class="col-sm-8">
                <input type="text" class="form-control" id="address" name="address" placeholder="请输入详细地址"
                       value="{{ $user->address }}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">添加</button>
            </div>
        </div>
    </form>
@stop