@extends('index.manage-master')
@section('subtitle', '库存管理')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">库存管理</span>
                </div>
            </div>
            <div class="row goods-tables margin-clear  search-page">
                <div class="col-sm-12 commodity-class search-sort sort">
                    @if (!empty(array_except($data , ['name' , 'sort', 'page'])))
                        <div class="col-sm-12 a-menu-panel padding-clear">
                            @if (isset($data['category_id']))
                                <div class="search-list-item sort-item">
                                    @if (isset($data['category_id']))
                                        @foreach($categories as $key => $category)
                                            @if(isset($category['selected']))
                                                @if(array_keys($categories)[0]==$key)
                                                    <select class="control select-category">
                                                        <option value="{{ url('inventory') }}">全部分类</option>
                                                        @foreach($category as $key => $item)
                                                            <option value="{{ url('inventory?category_id=' . $item['level'].$item['id'] . (isset($data['nameOrCode']) ? '&nameOrCode=' . $data['nameOrCode'] : '' ) . (!is_null($data['status']) ? '&status=' . $data['status'] : '' )) }}" {{ $category['selected']['id']==$item['id']?'selected':'' }}>
                                                                {{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <div class="sort-list">
                                                        <a class="list-title"
                                                           href="{{ url('inventory?category_id='.$category['selected']['level'].$category['selected']['id'] . (isset($data['nameOrCode']) ? '&nameOrCode=' . $data['nameOrCode'] : ''). (!is_null($data['status']) ? '&status=' . $data['status'] : '' ) )}}"><span
                                                                    class="title-txt">{{  $category['selected']['name']}}</span></a>
                                                    </div>
                                                @endif
                                                <span class="fa fa-angle-right"></span>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="col-sm-12 padding-clear">
                        @if( isset($data['category_id']) && !empty($categories) && !isset($categories[count($categories)-1]['selected']))
                            <div class="search-list-item sort-item sort-item-panel">
                                <span class="pull-left title-name">分类 : </span>
                                <div class="clearfix all-sort-panel">
                                    <div class="pull-left all-sort">
                                        @foreach($categories[count($categories)-1] as $cate )
                                            <a href="{{ url('inventory?category_id='.$cate['level'].$cate['id']. (isset($data['nameOrCode']) ? '&nameOrCode=' . $data['nameOrCode'] : ''). (!is_null($data['status']) ? '&status=' . $data['status'] : '' ))  }}">{{ $cate['name'] }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @elseif(!isset($data['category_id']) && !empty($categories))
                            商品分类 :
                            <select class="control select-category">
                                <option value="{{ url('inventory') }}">全部分类</option>
                                @foreach($categories as $key => $category)
                                    <option value="{{ url('inventory?category_id=' . $category['level'].$category['id'] . (isset($data['nameOrCode']) ? '&nameOrCode=' . $data['nameOrCode'] : '' ). (!is_null($data['status']) ? '&status=' . $data['status'] : '' )) }}">
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                <form action="{{url('inventory')}}" method="get" autocomplete="off">
                    <div class="col-sm-12 controls">
                        <div class="item-panel">
                            <div class="item inventory-name-input">
                                <input class="control" name="nameOrCode" value="{{$data['nameOrCode']}}"
                                       placeholder="商品名称/商品条形码" type="text">
                                @foreach(array_only($data , ['category_id']) as $key=>$val)
                                    <input type="hidden" name="{{ $key }}" value="{{ $val }}"/>
                                @endforeach
                            </div>
                            <div class="control item">
                                <select name="status" class="control">
                                    <option value="">选择上下架</option>
                                    <option @if(!is_null($data['status']) && $data['status'] == cons('goods.status.on')) selected
                                            @endif value="{{cons('goods.status.on')}}">上架
                                    </option>
                                    <option @if(!is_null($data['status']) &&  $data['status'] == cons('goods.status.off')) selected
                                            @endif value="{{cons('goods.status.off')}}">下架
                                    </option>
                                </select>
                            </div>
                            <div class="item ">
                                <button class="btn btn-blue-lighter control search search-by-get" type="submit">搜索
                                </button>
                            </div>
                            <div class="item warehousing-error-btn">
                                <span class="red">预警商品数</span><a href="{{url('inventory?warning=1')}}"
                                                                 class="badge badge-danger">{{$countNeedWarning}}</a>
                            </div>

                        </div>
                    </div>
                </form>
                <div class="col-sm-12  goods-table-panel">
                    <table class="table table-bordered table-title table-width">
                        <thead>
                        <tr>
                            <th>商品名</th>
                            <th>商品条形码</th>
                            <th>价格</th>
                            <th>库存</th>
                            <th>库存预警值</th>
                            <th>分类</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="goods-table">
                        <table class="table table-width">
                            <tbody>
                            @if(isset($goods))
                                @foreach($goods as $item)
                                    <tr>
                                        <td>
                                            <div class="pull-left">
                                                <img class="store-img lazy" data-original="{{ $item->image_url }}"
                                                     src="{{ $item->image_url }}">
                                            </div>
                                            <a class="product-name ellipsis pull-right"
                                               href="{{ url('goods/' . $item->id) }}"
                                               title="{{ $item->name }}"> {{$item->name}}</a>
                                        </td>
                                        <td>
                                            {{$item->bar_code}}
                                        </td>
                                        <td>
                                            @if(auth()->user()->type != cons('user.type.maker'))
                                                <p>{{ $item->price_retailer}}元
                                                    / {{cons()->valueLang('goods.pieces',$item->pieces_retailer)}}</p>
                                            @endif

                                            @if(auth()->user()->type == cons('user.type.supplier') || auth()->user()->type == cons('user.type.maker'))
                                                <p>{{ $item->price_wholesaler }}元
                                                    / {{ cons()->valueLang('goods.pieces' , $item->pieces_wholesaler) }}
                                                    @if(auth()->user()->type != cons('user.type.maker'))
                                                        (批)
                                                    @endif
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            {{--{{$item->need_warning}}--}}
                                            {{$item->surplus_inventory}}
                                        </td>
                                        <td>
                                            <i class="iconfont icon-xiugai warning-edit-icon"></i>
                                            <i data-id="{{$item->id}}"
                                               class="iconfont icon-baocun hidden warning-edit-icon"></i>
                                            <input name="warning_value" class="stock-warning modify hidden" type="text">
                                            <span class="stock-warning @if($item->need_warning) red @endif warning_value undisable">{{$item->warning_value ?? 0}}</span>
                                            <span class="undisable @if($item->need_warning) red @endif warning_piece">{{cons()->valueLang('goods.pieces',$item->warning_piece ?? 0)}}</span>
                                            <select name="warning_piece" class="modify hidden">
                                                @if(!is_null($item->goodsPieces->pieces_level_1))
                                                    <option @if ($item->warning_piece == $item->goodsPieces->pieces_level_1) selected
                                                            @endif value="{{$item->goodsPieces->pieces_level_1}}">{{cons()->valueLang('goods.pieces',$item->goodsPieces->pieces_level_1)}}</option>
                                                @endif
                                                @if(!is_null($item->goodsPieces->pieces_level_2))
                                                    <option @if ($item->warning_piece == $item->goodsPieces->pieces_level_2) selected
                                                            @endif value="{{$item->goodsPieces->pieces_level_2}}">{{cons()->valueLang('goods.pieces',$item->goodsPieces->pieces_level_2)}}</option>
                                                @endif
                                                @if(!is_null($item->goodsPieces->pieces_level_3))
                                                    <option @if ($item->warning_piece == $item->goodsPieces->pieces_level_3) selected
                                                            @endif value="{{$item->goodsPieces->pieces_level_3}}">{{cons()->valueLang('goods.pieces',$item->goodsPieces->pieces_level_3)}}</option>
                                                @endif
                                            </select>
                                        </td>
                                        <td>{{ isset($goodsCate[$item->id]) ? implode('/',$goodsCate[$item->id]) : '' }}</td>
                                        <td class="operating text-center">
                                            <a href="{{url('inventory/out-create'). '/' .$item->id}}"
                                               class="edit">出库</a>
                                            <a href="{{url('inventory/in-create'). '/' .$item->id}}" class="edit">入库</a>
                                            <a href="{{url('inventory/goods-inventory-detail'). '/' .$item->id}}"
                                               class="edit">明细</a>
                                            <a href="javascript:" data-method="put"
                                               data-url="{{ url('api/v1/my-goods/shelve')}}"
                                               data-status="{{ $item->status }}"
                                               data-data='{ "id": "{{ $item->id }}" }'
                                               data-on='上架'
                                               data-off='下架'
                                               data-change-status="true"
                                               class="ajax-no-form orange ">
                                                {{ cons()->valueLang('goods.status' , !$item->status) }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $goods->appends($data)->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        myGoodsFunc();
        $('.select-category').change(function () {
            window.location.href = $(this).val();
        });

        /**
         * 修改预警值
         */
        $('.icon-xiugai').click(function () {
            var self = $(this);
            self.addClass('hidden').siblings('i').removeClass('hidden');

            self.siblings(".undisable").addClass("hidden").siblings(".modify").removeClass('hidden');
            self.siblings('input').val(self.siblings("span.warning_value").html());
        });

        /**
         * 保存预警值
         */
        $('.icon-baocun').click(function () {
            var self = $(this),
                input = self.siblings('input'),
                select = self.siblings('select'),
                war_value = input.val(),
                id = self.data('id'),
                war_piece = $(select).find("option:selected").val();
            self.parents('td').html('<i class="fa fa-spinner fa-pulse"></i>');
            $data = {'warning_value': war_value, 'warning_piece': war_piece};
            $.ajax({
                url: 'api/v1/my-goods/' + id + '/warning',
                type: 'put',
                dataType: 'json',
                data: $data,
                success: function (data) {
                    location.reload();
                    /*self.addClass('hidden').siblings('i').removeClass('hidden');
                     self.siblings(".modify").addClass("hidden").siblings(".undisable").removeClass('hidden');
                     self.siblings('.warning_value').html(war_value);
                     self.siblings('.warning_piece').html($(select).find("option:selected").html());*/
                }
            })
        });
    </script>
@stop
