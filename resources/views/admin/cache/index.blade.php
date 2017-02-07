@extends('admin.master')

@section('subtitle','缓存管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/cache/delete') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>缓存名</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>地址库</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "address"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>首页栏目</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "home_column"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>首页广告</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "advert"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>首页公告</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "notice"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>分类列表</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "categories"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>标签库</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "attrs"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>购物车数量</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "cart"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>商品图片</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "goods:image"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>店铺->用户</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "shop-user"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>用户->店铺</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <button type="button" class="btn btn-danger ajax" data-data='{"key" : "user-shop"}'>
                            <i class="fa fa-trash-o"></i> 清除
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
@stop
