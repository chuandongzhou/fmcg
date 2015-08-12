@extends('admin.master')

@section('subtitle', '首页')

@section('right-container')
    <div class="row">
        <div class="col-sm-8">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">系统信息</div>
                <table class="table">
                    <tbody>
                    @foreach($serverInfo as $info)
                        <tr>
                            <td>{{ $info['name'] }}</td>
                            <td>{{ $info['value'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">目录权限</div>
                <table class="table">
                    <tbody>
                    @foreach($flodersInfo as $name => $info)
                        <tr>
                            <td>{{ $name }}</td>
                            <td>
                                @if (empty($info))
                                    <span class="label label-danger">目录不存在</span>
                                @else
                                    @foreach($info as $type => $value)
                                        <span class="label label-{{ $value ? 'success' : 'danger' }}">{{ $value ? '可' : '不可' }}{{ ['read' => '读', 'write' => '写'][$type] }}</span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">PHP扩展</div>
                <table class="table">
                    <tbody>
                    @foreach($extensionsInfo as $info)
                        <tr>
                            <td>{{ $info['name'] }}</td>
                            <td>
                                <span class="label label-{{ $info['is_exists'] ? 'success' : 'danger' }}">{{ $info['is_exists'] ? '正常' : '关闭' }}</span>
                            </td>
                            <td><span class="label label-info">{{ $info['type'] }}</span></td>
                            <td>{{ $info['detail'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop