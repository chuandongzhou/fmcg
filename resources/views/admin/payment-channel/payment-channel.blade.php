@extends('admin.master')

@include('includes.uploader')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $paymentChannel->id ? 'put' : 'post' }}"
          action="{{ url('admin/payment-channel/' . $paymentChannel->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('admin/payment-channel') }}" autocomplete="off">

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">渠道名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入渠道名"
                       value="{{ $paymentChannel->name }}">
            </div>
        </div>


        <div class="form-group">
            <label for="contact" class="col-sm-2 control-label">渠道识别码</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="identification_code" name="identification_code"
                       placeholder="请输入渠道识别码"
                       value="{{ $paymentChannel->identification_code }}">
            </div>
        </div>

        <div class="form-group">
            <label for="contact" class="col-sm-2 control-label">渠道类型</label>

            <div class="col-sm-4">
                <select class="form-control" name="type">
                    <option value="">请选择渠道类型</option>
                    @foreach(cons()->valueLang('payment_channel.type') as $code=> $name)
                        <option value="{{ $code }}" {{ $code ==$paymentChannel->type ? 'selected' : ''  }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="upload-file" class="col-sm-2 control-label">展示图片</label>

            <div class="col-sm-4">
                <span data-name="icon" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('V1') }}"
                                       name="file">
                            </span>

                <div class="image-preview w160">
                    <img src="{{ $paymentChannel->icon }}" class="img-thumbnail">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">{{ $paymentChannel->id ? '保存':'添加' }}</button>
            </div>
        </div>
    </form>
@stop