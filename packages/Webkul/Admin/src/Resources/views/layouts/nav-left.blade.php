<div id="navbar-left" class="navbar-left" onmouseleave="closeNav()">
    <ul class="menubar">
        @foreach ($menu->items as $menuItem)
            <li class="menu-item {{ $menu->getActive($menuItem) }}">
                    <span class="icon {{ $menuItem['icon-class'] }} menu-item-icon" onclick="onClickMenuIcon(event.target)"></span>
                    <span class="menu-item-name" onclick="onClickMenuName(event.target)">{{ trans($menuItem['name']) }}</span>
                    <div class="dropdown-container">
                        @foreach($menuItem['children'] as $item)
                            <a href="{{ $item['url'] }}" class="{{ $menu->getActive($item) }}">
                                {{ trans($item['name']) }}
                                @if ($menu->getActive($item))
                                    <i class="angle-right-icon"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                    
                </a>
            </li>
        @endforeach
    </ul>
</div>