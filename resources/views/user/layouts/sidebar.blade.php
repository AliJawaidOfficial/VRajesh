@php
    $sideBarList = [
        [
            'name' => 'Dashboard',
            'url' => route('user.dashboard'),
            'active_url' => 'user.dashboard',
            'icon' => 'https://zetdigi.com/vrajesh/public/' . asset('assets/images/icons/arrow-right.png'),
        ],
        [
            'name' => 'Connect',
            'url' => route('user.connect'),
            'active_url' => 'user.connect',
            'icon' => 'https://zetdigi.com/vrajesh/public/' . asset('assets/images/icons/arrow-right.png'),
        ],
        [
            'name' => 'Facebook Post',
            'active_url' => 'user.facebook.post.*',
            'icon' => 'https://zetdigi.com/vrajesh/public/' . asset('assets/images/icons/arrow-right.png'),
            'submenu' => [
                [
                    'name' => 'Text Post',
                    'url' => route('user.facebook.post.text.create'),
                    'active_url' => 'user.facebook.post.text.create',
                    'permission' => 'product category read',
                ],
                [
                    'name' => 'Image Post',
                    'url' => route('user.facebook.post.image.create'),
                    'active_url' => 'user.facebook.post.image.create',
                    'permission' => 'product category read',
                ],
                [
                    'name' => 'Video Post',
                    'url' => route('user.facebook.post.video.create'),
                    'active_url' => 'user.facebook.post.video.create',
                    'permission' => 'product category read',
                ],
            ],
        ],
        [
            'name' => 'LinkedIn',
            'active_url' => 'user.linkedin.post.*',
            'icon' => 'https://zetdigi.com/vrajesh/public/' . asset('assets/images/icons/arrow-right.png'),
            'submenu' => [
                [
                    'name' => 'Text Post',
                    'url' => route('user.linkedin.post.text.create'),
                    'active_url' => 'user.linkedin.post.text.create',
                ],
                [
                    'name' => 'Video Post',
                    'url' => route('user.linkedin.post.video.create'),
                    'active_url' => 'user.linkedin.post.video.create',
                    'permission' => 'product category read',
                ],
            ],
        ],
    ];
@endphp

<div class="sidebar">
    <div class="logo d-flex align-items-center justify-content-between">Marketing Software <span class="cancel-icon"><img src="./assets/images/icons/cancel.png" alt=""></span></div>
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
                                            <span class="menu-icon"><img src="{{ $item['icon'] }}"
                                                    alt=""></span>
                                            <span>{{ $item['name'] }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $key }}"
                                        class="accordion-collapse collapse {{ Str::is($item['active_url'], Route::currentRouteName()) ? 'show' : '' }}"
                                        aria-labelledby="heading{{ $key }}"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <ul class="accordion inner-menu" id="secondLevel">
                                                @foreach ($item['submenu'] as $key => $item)
                                                    {{-- @if (isset($item['permission'])) --}}
                                                        {{-- @if (Auth::user()->can($item['permission'])) --}}
                                                            <li
                                                                class="menu-item {{ Str::is($item['active_url'], Route::currentRouteName()) ? 'active' : '' }}">
                                                                <a href="{{ $item['url'] }}">
                                                                    <span class="menu-icon"><img
                                                                            src="https://zetdigi.com/vrajesh/public/{{ asset('assets/images/icons/arrow-right.png') }}"
                                                                            alt=""></span>
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
