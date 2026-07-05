<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('assets/img/AdminLTELogo.png') }}" alt="SIMKLINIK Logo" class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">SIMKLINIK</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Main navigation">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false" id="navigation">
                @php
                    $menus = \App\Models\Menu::whereNull('parent_id')
                        ->where('is_active', true)
                        ->orderBy('order')
                        ->get();
                @endphp

                @foreach ($menus as $menu)
                    @if (auth()->user()->can($menu->permission_name) || !$menu->permission_name)
                        @if ($menu->children->count() > 0)
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon {{ $menu->icon }}"></i>
                                    <p>
                                        {{ $menu->name }}
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @foreach ($menu->children as $submenu)
                                        @if (auth()->user()->can($submenu->permission_name) || !$submenu->permission_name)
                                            <li class="nav-item">
                                                <a href="{{ $submenu->route_name ? route($submenu->route_name) : '#' }}" class="nav-link {{ request()->routeIs($submenu->route_name . '*') ? 'active' : '' }}">
                                                    <i class="nav-icon bi bi-circle"></i>
                                                    <p>{{ $submenu->name }}</p>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ $menu->route_name ? route($menu->route_name) : '#' }}" class="nav-link {{ request()->routeIs($menu->route_name . '*') ? 'active' : '' }}">
                                    <i class="nav-icon {{ $menu->icon }}"></i>
                                    <p>{{ $menu->name }}</p>
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</aside>

