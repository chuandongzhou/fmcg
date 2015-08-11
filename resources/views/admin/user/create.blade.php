@extends('index.master')

@section('subtitle', '首页')

@section('container')
    @include('admin.left-nav')
    <div class="right-content">
        <form method="post"
              action="{{ url(($group == config('constant.user_group.wholesalers')) ? 'admin/wholesalers' : 'admin/retailer') }}">
            <table class="table">
                <tr>
                    <td width="20%">账号：</td>
                    <td><input class="text w200 necessary" type="text" name="user_name"/></td>
                </tr>
                <tr>
                    <td>密码：</td>
                    <td><input class="text w200 necessary" type="password" name="password"/></td>
                </tr>
                <tr>
                    <td>确认密码：</td>
                    <td><input class="text w200 necessary" type="password" name="password_confirmation"/></td>
                </tr>
                <tr>
                    <td>{{ $group == config('constant.user_group.wholesalers') ? '经销商' : '终端商'  }}姓名：</td>
                    <td><input class="text w200 necessary" type="text" name="nickname"/></td>
                </tr>
                <tr>
                    <td>地址：</td>
                    <td>
                        <select name="province_id">
                            <option value="0">省</option>
                            <option value="1">四川</option>
                        </select>
                        <select name="city_id">
                            <option value="0">市</option>
                            <option value="11">成都</option>
                        </select>
                        <select name="district_id">
                            <option value="0">区</option>
                            <option value="111">高新区</option>
                        </select>
                        <select name="street_id">
                            <option value="0">街道</option>
                            <option value="1111">天府五街</option>
                        </select>
                        <input type="text" name="address">
                    </td>
                </tr>
                <tr>
                    {{csrf_field()}}
                    <input type="hidden" value="{{  $group  }}" name="group"/>
                    <td colspan="2">
                        <button type="submit" class="btn btn-bg btn-default">添加</button>
                    </td>
                </tr>
            </table>


        </form>
    </div>
@stop