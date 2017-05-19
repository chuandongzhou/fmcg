@extends('master')
@section('css')
    @parent
    <style type="text/css">
        .setting-authority-title {
            border-bottom: 1px solid #f2f2f2;
            padding: 5px 10px;
        }

        .setting-authority-container {
            height: 450px;
            overflow-y: scroll;
        }

        .setting-authority-container .list-wrap {
            padding: 10px 0 5px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .setting-authority-container .menu-title {
            color: #17C4BB;
        }

        .setting-authority-container .menu-wrap {
            padding-top: 5px;
        }

        .setting-authority-container .menu-wrap.two {
            margin-left: 15px;
        }

        .setting-authority-container .menu-wrap.three {
            margin-left: 20px;
        }

        .setting-authority-container .menu-wrap.three label {
            display: inline-block;
            font-weight: normal;
        }

        .setting-authority-container .menu-wrap.three label + label {
            margin-left: 10px;
        }

        .setting-authority-container .menu-wrap input[type='checkbox'] {
            vertical-align: top;
        }

        .setting-authority-container .triangle {
            width: 0;
            height: 0;
            display: inline-block;
        }

        .setting-authority-container .triangle.triangle-down {
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-top: 10px solid #17C4BB;
        }

        .setting-authority-container .triangle.triangle-right {
            border-top: 7px solid transparent;
            border-left: 10px solid #17C4BB;
            border-bottom: 7px solid transparent;
        }

        .setting-authority-footer .btn-save {
            margin: 10px 20px 0px 0px;
        }
    </style>
@stop
@section('body')
    <form action="{{ url('api/v1/personal/child-user/bind-node/' . $childUser->id) }}" method="post"
          class="form-horizontal ajax-form" data-no-loading="true">
        <div class="setting-authority-title">用户名:<span class="child-user-name">{{ $childUser->name }}</span></div>
        <div class="setting-authority-container">
            @foreach($indexNodes as $node)
                <div class="setting-authority-item">
                    <div class="list-wrap one">
                        <input type="checkbox" name="nodes[]"
                               value="{{ $node['id'] }}" {{ in_array($node['id'], $userNodes) ? 'checked' : '' }}>
                        <a class="menu-title">
                            @if($nodeChild = $node['child'])<i
                                    class="triangle triangle-down "></i>@endif{{ $node['name'] }}
                        </a>
                        @foreach($nodeChild as $child)
                            <div class="menu-wrap two">
                                <input type="checkbox" name="nodes[]"
                                       value="{{ $child['id'] }}" {{ in_array($child['id'], $userNodes) ? 'checked' : '' }}>
                                <a class="menu-title">
                                    @if($grandChildren = array_get($child, 'child'))<i
                                            class="triangle triangle-down "></i>@endif{{ $child['name'] }}
                                </a>
                                @if($grandChildren)
                                    <div class="menu-wrap three">
                                        @foreach($grandChildren as $grandChild)
                                            <label>
                                                <input type="checkbox" name="nodes[]"
                                                       value="{{ $grandChild['id'] }}" {{ in_array($grandChild['id'], $userNodes) ? 'checked' : '' }}>
                                                {{ $grandChild['name'] }}
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>
        <div class="text-right setting-authority-footer">
            <button type="submit" class="btn btn-success no-prompt btn-save">保存</button>
        </div>
    </form>
@stop

@section('js')
    <script type="text/javascript">
        $(function () {
            //权限选择
            $("body").on('click', '.setting-authority-item .menu-title', function () {
                var self = $(this), triangle = self.children(".triangle");
                if (triangle.hasClass("triangle-down")) {
                    triangle.addClass("triangle-right").removeClass("triangle-down");
                    self.siblings(".menu-wrap").slideUp();

                } else {
                    triangle.addClass("triangle-down").removeClass("triangle-right");
                    self.siblings(".menu-wrap").slideDown();
                }
            });
            //点击选择框
            $('.one input[type="checkbox"], .two input[type="checkbox"], .three>input[type="checkbox"]').on('change', function () {
                var obj = $(this), isChecked = obj.is(':checked');
                obj.parent('div').find('input[type="checkbox"]').prop('checked', isChecked);
                if (isChecked) {
                    obj.closest('div').siblings('input[type="checkbox"]').prop('checked', isChecked).closest('div').siblings('input[type="checkbox"]').prop('checked', isChecked);
                }
            })
        })

    </script>
@stop