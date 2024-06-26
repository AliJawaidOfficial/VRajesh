@php
    $sideBarList = [
        [
            'name' => 'Dashboard',
            'url' => route('admin.dashboard'),
            'active_url' => 'admin.dashboard',
            'icon' => asset('assets/images/icons/home.png'),
        ],
        [
            'name' => 'Users',
            'url' => route('admin.user.index'),
            'active_url' => 'admin.user.*',
            'icon' => asset('assets/images/icons/user.png'),
        ],
        [
            'name' => 'Packages',
            'url' => route('admin.package.index'),
            'active_url' => 'admin.package.*',
            'icon' => asset('assets/images/icons/user.png'),
        ],
    ];
@endphp

<div class="sidebar">
    <div class="logo d-flex align-items-center">

        <img src="{{ asset('assets/images/logo.png') }}" class="main-logo" alt="">
        <span class="cancel-icon">
            <img src="{{ asset('assets/images/icons/cancel.png') }}" alt="">
        </span>
    </div>
    <div class="navigation">
        <nav class="main-menu">
            <ul class="accordion" id="accordionExample">
                @foreach ($sideBarList as $key => $item)
                    @if (isset($item['submenu']))
                        {{-- @if (isset($item['permission'])) --}}
                        {{-- @if (Auth::user()->canAny($item['permission'])) --}}
                        <li class="menu-item accordion-item ">
                            <h2 class="accordion-header" id="heading{{ $key }}">
                                <button
                                    class="accordion-button {{ Str::is($item['active_url'], Route::currentRouteName()) ? '' : 'collapsed' }}"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $key }}" aria-expanded="true"
                                    aria-controls="collapse{{ $key }}">
                                    <span class="menu-icon"><img src="{{ $item['icon'] }}" alt=""></span>
                                    <span>{{ $item['name'] }}</span>
                                </button>
                            </h2>
                            <div id="collapse{{ $key }}"
                                class="accordion-collapse collapse {{ Str::is($item['active_url'], Route::currentRouteName()) ? 'show' : '' }}"
                                aria-labelledby="heading{{ $key }}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="accordion inner-menu" id="secondLevel">
                                        @foreach ($item['submenu'] as $key => $item)
                                            {{-- @if (isset($item['permission'])) --}}
                                            {{-- @if (Auth::user()->can($item['permission'])) --}}
                                            <li
                                                class="menu-item {{ Str::is($item['active_url'], Route::currentRouteName()) ? 'active' : '' }}">
                                                <a href="{{ $item['url'] }}">
                                                    <span class="menu-icon">
                                                        <img src="{{ asset('assets/images/icons/arrow-right.png') }}"
                                                            alt="">
                                                    </span>
                                                    <span>{{ $item['name'] }}</span>
                                                </a>
                                            </li>
                                            {{-- @endif --}}
                                            {{-- @endif --}}
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </li>
                        {{-- @endif --}}
                        {{-- @endif --}}
                    @else
                        {{-- @if (isset($item['permission'])) --}}
                        {{-- @if (Auth::user()->can($item['permission'])) --}}
                        {{-- <li
                                    class="menu-item {{ Str::is($item['active_url'], Route::currentRouteName()) ? 'active' : '' }}">
                                    <a href="{{ $item['url'] }}">
                                        <span class="menu-icon">
                                            <img src="{{ $item['icon'] }}" alt=""></span>
                                        <span>{{ $item['name'] }}</span>
                                    </a>
                                </li> --}}
                        {{-- @endif --}}
                        {{-- @else --}}
                        <li
                            class="menu-item {{ Str::is($item['active_url'], Route::currentRouteName()) ? 'active' : '' }}">
                            <a href="{{ $item['url'] }}">
                                <span class="menu-icon">
                                    <img src="{{ $item['icon'] }}" alt=""></span>
                                <span>{{ $item['name'] }}</span>
                            </a>
                        </li>
                        {{-- @endif --}}
                    @endif
                @endforeach

            </ul>

        </nav>
    </div>
</div>
