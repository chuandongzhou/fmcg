@extends('auth.master')
@include('includes.cropper')
@include('includes.address', ['model' => 'shop'])

@section('title' , '注册 | 订百达')

@section('body')
    @parent
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-xs-12 register-step-wrap text-center">
                <div class="item item-text step-active">
                    创建账户
                </div>
                <div class="item step-active">--------------</div>
                <div class="item item-text step-active">
                    设置密码
                </div>
                <div class="item">--------------</div>
                <div class="item item-text">
                    填写商户信息
                </div>
                <div class="item">--------------</div>
                <div class="item item-text">
                    注册成功
                </div>
            </div>
            <div class="col-xs-12">
                <div class="row register">
                    <div class="col-xs-8">
                        <div class="title">请为账号<span class="title-username">{{ $user['user_name'] }}</span>设置密码</div>
                        <form class="ajax-form form-horizontal" method="post"
                              action="{{  url('api/v1/auth/set-password')  }}"
                              accept-charset="UTF-8" data-done-url="{{ url('auth/register-add-shop') }}" autocomplete="off">
                            <fieldset>
                                <input type="hidden" name="user_name" value="{{ $user['user_name'] }}" />
                                <input type="hidden" name="type" value="{{ $user['type'] }}" />
                                <input type="hidden" name="backup_mobile" value="{{ $user['backup_mobile'] }}" />
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" >
                                        <span class="red ">*</span>
                                        <span class="prompt">用户密码:</span>
                                    </label>
                                    <div class="col-xs-8 col-md-6">
                                        <input class="form-control"  type="password" name="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">
                                        <span class="red ">*</span>
                                        <span class="prompt">确认密码:</span>
                                    </label>
                                    <div class="col-xs-8 col-md-6">
                                        <input class="form-control"  type="password" name="password_confirmation">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-8 col-xs-offset-3">
                                        <button type="submit" class="btn btn-warning btn-submit">下一步
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop