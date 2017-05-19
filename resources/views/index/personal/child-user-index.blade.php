@extends('index.manage-master')
@section('subtitle', '个人中心-子账号')
@include('includes.child-user')
@include('includes.child-user-node')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/info') }}">个人中心</a> >
                    <span class="second-level"> 子账号</span>
                </div>
            </div>
            <div class="row coupon">
                <div class="col-sm-12 table-responsive">
                    <div class="add-coupon">
                        <a class="add btn btn-blue-lighter update-modal" href="javascript:" data-toggle="modal"
                           data-target="#childUserModal">
                            <label>
                                <span class="fa fa-plus"></span>
                            </label>添加帐号
                        </a>
                    </div>
                    <table class="table table-bordered table-center public-table">
                        <thead>
                        <tr>
                            <th>姓名</th>
                            <th>帐号名</th>
                            <th>联系方式</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($childUsers as $user)
                            <tr>
                                <td>
                                    {{ $user->name }}
                                </td>
                                <td>
                                    {{ $user->account }}
                                </td>
                                <td>
                                    {{ $user->phone }}
                                </td>
                                <td>
                                    已<span class="status-name">{{ cons()->valueLang('status' ,$user->status) }}</span>
                                </td>
                                <td>
                                    <div role="group" class="btn-group btn-group-xs">
                                        <a data-toggle="modal"
                                           data-target="#childUserModal" data-id="{{ $user->id }}"
                                           data-name="{{ $user->name }}" data-phone="{{ $user->phone }}"
                                           data-account="{{ $user->account }}"
                                           class="edit update-modal">
                                            <i class="iconfont icon-xiugai"></i> 编辑
                                        </a>
                                        <a href="javascript:" data-method="put"
                                           data-url="{{ url('api/v1/personal/child-user/status/' . $user->id)}}"
                                           data-status="{{ $user->status }}"
                                           data-data='{ "id": "{{ $user->id }}" }'
                                           data-on='<i class="iconfont icon-qiyong"></i> 启用'
                                           data-off='<i class="iconfont icon-jinyong"></i> 禁用'
                                           data-change-status="true"
                                           class="ajax-no-form color-blue">
                                            {!!  $user->status ? '<i class="iconfont icon-jinyong"></i> 禁用' : '<i class="iconfont icon-qiyong"></i> 启用' !!}
                                        </a>
                                        <a data-toggle="modal"
                                           data-target="#childUserNodeModal" data-id="{{ $user->id }}"
                                           class="edit-node">
                                            <i class="iconfont icon-xiugai"></i> 分配权限
                                        </a>
                                        <a data-url="{{ url('api/v1/personal/child-user/'. $user->id) }}"
                                           data-method="delete" class="red delete-no-form ajax" href="javascript:"
                                           data-danger="真的要删除吗？"
                                           type="button">
                                            <i class="iconfont icon-shanchu"></i> 删除
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="right">
                {!! $childUsers->render() !!}
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        ajaxNoForm();
    </script>
@stop

