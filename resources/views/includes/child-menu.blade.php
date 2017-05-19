<div class="page-sidebar-wrapper">
    <!--左侧导航栏菜单-->
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false"
            data-auto-scroll="true" data-slide-speed="200">
            @foreach($nodes as $key => $node)
                <li class="nav-item start {{ path_active(array_filter( array_pluck($node['child'], 'url'))) }}">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-smile-o"></i>
                        <span class="title">{{ $node['name'] }}</span>
                        <span class="selected"></span>
                        <span class="arrow"></span>
                    </a>
                    @if(!empty($nodeChild = $node['child']))
                        <ul class="sub-menu">
                            @foreach($nodeChild as $child)
                                <li class="nav-item start {{ path_active($child['url']) }}">
                                    <a href="{{ url($child['url']) }}" class="nav-link">
                                        <span class="title">{{ $child['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>