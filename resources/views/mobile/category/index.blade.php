@extends('mobile.master')

@section('subtitle', '首页')

@section('body')
    @parent
    <div class="fixed-header fixed-item sort-top-nav">
        <div class="row nav-top">
            <div class="col-xs-2">
                <a class="iconfont icon-fanhui2 go-back" href="javascript:window.history.back()"></a>
            </div>
            <div class="col-xs-10 color-black pd-left-clear">
                分类
            </div>
        </div>
        <div class="sort-panel">
            <ul class="list-panel">
                @foreach($categories as $key => $category)
                    <li class="{{ !$cate && $key == key($categories) ? 'active' : ($cate == $category['id'] ? 'active' : '') }}">
                        <i class="iconfont icon-shipin"></i><a>{{ $category['name'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="container-fluid  m60 categories-panel">
        @foreach($categories as $key => $category)
            <div class="row sort-list-wrap {{ !$cate && $key == key($categories) ? '' : ($cate == $category['id'] ? '' : 'hidden') }}">
                @if(!empty($children = $category['child']))
                    @foreach($children as $child)
                        <div class="col-xs-12 sort-list">
                            <h4 class="title">{{ $child['name'] }}</h4>

                            @if(!empty($items = $child['child']))
                                <div class="list-wrap">
                                    @foreach($items as $item)
                                        <a href="{{ url('goods?category_id=' . $item['level']. $item['id']) }}">{{ $item['name'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $('ul.list-panel li').on('click', function () {
            var obj = $(this), index = obj.index();
            obj.addClass('active').siblings().removeClass('active');
            $('.categories-panel').children('div').eq(index).removeClass('hidden').siblings().addClass('hidden');
        })
    </script>
@stop