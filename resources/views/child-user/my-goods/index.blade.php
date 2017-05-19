@extends('child-user.manage-master')

@section('subtitle', '我的商品')
@include('includes.jquery-lazeload')

@section('container')
    @include('includes.child-menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('child-user/my-goods') }}">商品管理</a> >
                    <span class="second-level">我的商品</span>
                </div>
            </div>
            <div class="row goods-tables margin-clear search-page">
                <div class="col-sm-12 commodity-class search-sort sort">

                    @if (!empty(array_except($get , ['name' , 'sort', 'page'])))
                        <div class="col-sm-12 a-menu-panel padding-clear">
                            @if (isset($get['category_id']))
                                <div class="search-list-item sort-item">
                                    @if (isset($get['category_id']))
                                        @foreach($categories as $key => $category)
                                            @if(isset($category['selected']))
                                                @if(array_keys($categories)[0]==$key)
                                                    <select class="control select-category">
                                                        <option value="{{ url('child-user/my-goods') }}">全部分类</option>
                                                        @foreach($category as $key => $item)
                                                            <option value="{{ url('child-user/my-goods?category_id=' . $item['level'].$item['id'] . (isset($get['name']) ? '&name=' . $get['name'] : '' )) }}" {{ $category['selected']['id']==$item['id']?'selected':'' }}>
                                                                {{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <div class="sort-list">
                                                        <a class="list-title"
                                                           href="{{ url('child-user/my-goods?category_id='.$category['selected']['level'].$category['selected']['id']) }}"><span
                                                                    class="title-txt">{{  $category['selected']['name']}}</span></a>
                                                    </div>
                                                @endif
                                                <span class="fa fa-angle-right"></span>
                                            @endif
                                        @endforeach

                                    @endif
                                    @foreach($searched as $attrId => $name)
                                        <div class="sort-list">
                                            <a class="last-category"
                                               href="{{ url('child-user/my-goods?' . http_build_query(array_except($get , ['attr_' . $attrId]))) }}">
                                                {{ $name }}
                                                <i class="fa fa-times"></i></a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="col-sm-12 padding-clear">
                        @if( isset($get['category_id']) && !empty($categories) && !isset($categories[count($categories)-1]['selected']))
                            <div class="search-list-item sort-item sort-item-panel">
                                <span class="pull-left title-name">分类 : </span>
                                <div class="clearfix all-sort-panel">
                                    <div class="pull-left all-sort">
                                        @foreach($categories[count($categories)-1] as $cate )
                                            <a href="{{ url('child-user/my-goods?category_id='.$cate['level'].$cate['id']) }}">{{ $cate['name'] }}</a>
                                        @endforeach
                                    </div>
                                    <a class="more pull-right" href="#"><span>更多</span> <i class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                        @elseif(!isset($get['category_id']) && !empty($categories))
                            商品分类 :
                            <select class="control select-category">
                                <option value="{{ url('child-user/my-goods') }}">全部分类</option>
                                @foreach($categories as $key => $category)
                                    <option value="{{ url('child-user/my-goods?category_id=' . $category['level'].$category['id'] . (isset($get['name']) ? '&name=' . $get['name'] : '' )) }}">
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        @foreach($attrs as $attr)
                            @if(isset($attr['child']))
                                <div class="search-list-item sort-item sort-item-panel">
                                    <span class="pull-left title-name">{{ $attr['name'] }} : </span>

                                    <div class="clearfix all-sort-panel">
                                        <div class="pull-left all-sort">
                                            @foreach($attr['child'] as $child)
                                                <a href="{{ url('child-user/my-goods?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}"
                                                   class="btn  control">
                                                    {{ $child['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                        <a class="more pull-right" href="javascript:"><span>更多</span> <i
                                                    class="fa fa-angle-down"></i></a>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(!empty($moreAttr))
                            @foreach($moreAttr as $attr)
                                @if(isset($attr['child']))
                                    <div class="search-list-item sort-item sort-item-panel">
                                        <span class="pull-left title-name">{{ $attr['name'] }} : </span>

                                        <div class="clearfix all-sort-panel">
                                            <div class="pull-left all-sort">
                                                @foreach($attr['child'] as $child)
                                                    <a href="{{ url('child-user/my-goods?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}">{{ $child['name'] }}</a>
                                                @endforeach
                                            </div>
                                            <a class="more pull-right" href="#"><span>更多</span> <i
                                                        class="fa fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                <form action="{{ url('child-user/my-goods') }}" method="get" autocomplete="off">
                    <div class="col-sm-12 controls">
                        <div class="item-panel">
                            <div class="item control-name">
                                <input class="control" name="name" value="{{ isset($get['name']) ? $get['name'] : '' }}"
                                       placeholder="请输入商品名称" type="text">
                            </div>
                            <div class="item control-status">
                                <select class="status control" name="status">
                                    <option value="">请选择上下架</option>
                                    @foreach(cons()->valueLang('goods.status') as $status => $statusName)
                                        <option value="{{ $status }}" {{ isset($get['status']) && $get['status'] == $status ? 'selected' : '' }}>
                                            已{{ $statusName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="item control-status">
                                <select class="control" name="is_gift">
                                    <option value="">请选择赠品情况</option>
                                    <option value="1" {{ array_get($get, 'is_gift') == 1 ? 'selected' : '' }}>是</option>
                                    <option value="0" {{ array_get($get, 'is_gift') === '0' ? 'selected' : '' }}>否
                                    </option>
                                </select>
                            </div>
                            <div class="item control-delivery">
                                <select class="address-province control" name="province_id"
                                        data-id="{{ $get['province_id'] or 0}}">
                                </select>
                                <select class="address-city control" name="city_id"
                                        data-id="{{ $get['city_id'] or 0}}">
                                </select>
                                <select class="address-district control" name="district_id"
                                        data-id="{{ $get['district_id'] or 0}}">
                                </select>
                                <select class="address-street hidden useless-control control" name="street_id"
                                        data-id="{{ $get['street_id'] or  0}}">
                                </select>
                                @foreach(array_except($get , ['province_id' , 'city_id' ,'district_id', 'street_id' ,'is_gift', 'name', 'page', 'status']) as $key=>$val)
                                    <input type="hidden" name="{{ $key }}" value="{{ $val }}"/>
                                @endforeach
                            </div>
                            <div class="item ">
                                <button class="btn btn-blue-lighter control search search-by-get" type="submit">搜索
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <form class="form-horizontal ajax-form" method="put"
                      action="{{ url('api/v1/child-user/my-goods/batch-shelve') }}"
                      data-help-class="col-sm-push-1 col-sm-10" data-done-url="{{ url('child-user/my-goods') }}"
                      autocomplete="off">
                    <div class="col-sm-12  goods-table-panel">
                        <table class="table table-bordered table-title table-width">
                            <thead>
                            <tr>
                                <th>商品名</th>
                                <th>价格</th>
                                <th>最低购买数</th>
                                <th>分类</th>
                                <th>更新时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                        </table>
                        <div class="goods-table">
                            <table class="table    table-width">
                                <tbody>
                                @foreach($goods  as $item)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="child" name="ids[]" value="{{ $item->id }}">
                                            <img class="store-img lazy" data-original="{{ $item->image_url }}">
                                            <a class="product-name ellipsis"
                                               href="{{ url('child-user/my-goods/' . $item->id) }}"
                                               title="{{ $item->name }}"> {{ $item->name }}</a>
                                        </td>
                                        <td>
                                            <p>{{ $item->price_retailer}}元</p>
                                            @if($type == cons('user.type.supplier' || $type == cons('user.type.maker')))
                                                <p>{{ $item->price_wholesaler }}元 (批)</p>
                                            @endif
                                        </td>
                                        <td>
                                            <p> {{ $item->min_num }}</p>
                                            @if($type == cons('user.type.supplier' ||  $type == cons('user.type.maker')))
                                                <p>{{ $item->min_num_wholesaler }} (批)</p>
                                            @endif
                                        </td>
                                        <td>{{ isset($goodsCateName[$item->id]) ? implode('/',$goodsCateName[$item->id]) : '' }}</td>
                                        <td>{{ $item->updated_at }}</td>
                                        <td>
                                            已<span class="status-name">{{ cons()->valueLang('goods.status' ,$item->status) }}</span>
                                        </td>
                                        <td class="operating text-center">
                                            @if(!$item->is_mortgage_goods)
                                                <a href="javascript:" data-id="{{ $item->id }}" data-method="post"
                                                   data-url="{{ url('api/v1/child-user/my-goods/' . $item->id . '/mortgage') }}"
                                                   class="no-form mortgage color-blue" title="设为抵费商品">抵费</a>
                                            @endif

                                            <a href="javascript:" data-method="put"
                                               data-url="{{ url('api/v1/child-user/my-goods/gift')}}"
                                               data-status="{{ $item->is_gift }}"
                                               data-data='{ "id": "{{ $item->id }}" }'
                                               data-on='设为赠品'
                                               data-off='取消赠品'
                                               class="ajax-no-form color-blue">
                                                {{ $item->is_gift ? '取消赠品' : '设为赠品' }}
                                            </a>
                                            <a href="{{ url('child-user/my-goods/' . $item->id . '/edit') }}" class="edit">编辑</a>
                                            <a href="javascript:" data-method="put"
                                               data-url="{{ url('api/v1/child-user/my-goods/shelve')}}"
                                               data-status="{{ $item->status }}"
                                               data-data='{ "id": "{{ $item->id }}" }'
                                               data-on='上架'
                                               data-off='下架'
                                               data-change-status="true"
                                               class="ajax-no-form orange ">
                                                {{ cons()->valueLang('goods.status' , !$item->status) }}
                                            </a>
                                            <a class="red delete-no-form {{ $item->status ? 'hidden' : '' }} delete"
                                               data-method="delete"
                                               data-url="{{ url('api/v1/child-user/my-goods/' . $item->id) }}" href="javascript:">删除</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12 operating">
                        <label class="all-check"><input type="checkbox" id="parent"> 全选</label>

                        <button class="btn btn-blue-lighter " type="submit" data-data='{"status":"1"}'>批量上架</button>
                        <button class="btn btn-blue-lighter " type="submit" data-data='{"status":"0"}'>批量下架</button>
                    </div>
                    <div class="col-sm-12 text-right">
                        {!! $goods->appends($data)->render() !!}
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        displayList();
        formSubmitByGet();
        myGoodsFunc();
        onCheckChange('#parent', '.child');
        $('.select-category').change(function () {
            window.location.href = $(this).val();
        });
    </script>
@stop
