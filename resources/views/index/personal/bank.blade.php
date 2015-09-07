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
        <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/bank/' . $userBank->id) }}"
              method="{{ $userBank->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10" data-done-url="{{ url('personal/bank') }}">
            <div class="form-group row">
                <label class="col-sm-2 control-label" for="card_number">卡号:</label>

                <div class="col-sm-10 col-md-6">
                    <input class="form-control" id="card_number" name="card_number" placeholder="请输入银行卡号"
                           value="{{ $userBank->card_number }}"
                           type="text">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 control-label" for="contact_info">所属银行:</label>

                <div class="col-sm-4 col-md-4">
                    <select name="card_type" class="form-control">
                        @foreach(cons()->valueLang('bank.type') as $key => $type)
                            <option value="{{ $key }}" {{ $key == $userBank->card_type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 control-label" for="card_holder">开户人:</label>

                <div class="col-sm-10 col-md-6">
                    <input class="form-control" id="card_holder" name="card_holder" placeholder="请输入开户人"
                           value="{{ $userBank->card_holder }}"
                           type="text">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 control-label" for="card_address">开户行所在地:</label>

                <div class="col-sm-10 col-md-6">
                    <input class="form-control" id="card_address" name="card_address" placeholder="请输入开户行所在地"
                           value="{{ $userBank->card_address }}"
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
