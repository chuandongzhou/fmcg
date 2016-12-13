@extends('admin.master')

@section('right-container')

    @if($type == 'category' || $type == 'left-category')
        <form class="form-horizontal" action="{{ url('admin/advert-category') }}" method="get" autocomplete="off">
            <div class="form-group">
                <label class="col-sm-2 control-label">区域：</label>

                <div class="col-sm-2">
                    <select name="province_id" data-id="{{ $data['province_id'] or null }}"
                            class="address-province form-control"></select>
                </div>
                <div class="col-sm-2">
                    <select name="city_id" data-id="{{ $data['city_id'] or null }}" class="address-city form-control">
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type="submit" class="btn btn-default  search-by-get" value="查询"/>
                </div>
                <div class="col-sm-2">
                    <select name="district_id" class="address-district form-control hide useless-control">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="street_id" class="address-street form-control hide useless-control"></select>
                </div>

            </div>
        </form>
    @endif
    <table class="table table-striped">
        <thead>
        <tr>
            <th>广告类型</th>
            <th>名称</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>状态</th>
            <th class="text-nowrap">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($adverts as $advert)
            <tr>
                <td>{{ cons()->valueLang('advert.type', $advert->type) }}</td>
                <td>{{ $advert->name }}</td>
                <td>{{ $advert->start_at }}</td>
                <td>{{ $advert->end_at }}</td>
                <td>{{ $advert->status_name }}</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <a class="btn btn-primary" href="{{ url('admin/advert-' .$type. '/' .$advert->id. '/edit') }}">
                            <i class="fa fa-edit"></i> 编辑
                        </a>
                        <button type="button" class="btn btn-danger ajax" data-method="delete"
                                data-url="{{ url('admin/advert-' .$type. '/' . $advert->id ) }}">
                            <i class="fa fa-trash-o"></i> 删除
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! $adverts->appends(['type' => $type])->render() !!}
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop