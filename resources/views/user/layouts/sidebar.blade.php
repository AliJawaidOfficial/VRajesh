@php
    $sideBarList = [
        // [
        //     'name' => 'Dashboard',
        //     'url' => route('user.dashboard'),
        //     'active_url' => 'user.dashboard',
        //     'icon' => asset('assets/images/icons/home.png'),
        // ],
        [
            'name' => 'Connect Accounts',
            'url' => route('user.connect'),
            'active_url' => 'user.connect',
            'icon' => asset('assets/images/icons/connect.png'),
        ],
        [
            'name' => 'Post',
            'active_url' => 'user.post.*',
            'icon' => asset('assets/images/icons/arrow-right.png'),
            'permissions' => [
                'meta_facebook_text_post',
                'meta_facebook_image_post',
                'meta_facebook_video_post',
                'meta_instagram_image_post',
                'meta_instagram_video_post',
                'linkedin_text_post',
                'linkedin_image_post',
                'linkedin_video_post',
                'immediate_post',
                'scheduled_post',
                'draft_post',
            ],
            'submenu' => [
                [
                    'name' => 'Create',
                    'url' => route('user.post.create'),
                    'active_url' => 'user.post.create',
                    'permissions' => [
                        'meta_facebook_text_post',
                        'meta_facebook_image_post',
                        'meta_facebook_video_post',
                        'meta_instagram_image_post',
                        'meta_instagram_video_post',
                        'linkedin_text_post',
                        'linkedin_image_post',
                        'linkedin_video_post',
                    ],
                ],
                [
                    'name' => 'Published',
                    'url' => route('user.post.index'),
                    'active_url' => 'user.post.index',
                    'permissions' => ['immediate_post'],
                ],
                [
                    'name' => 'Draft',
                    'url' => route('user.post.draft'),
                    'active_url' => 'user.post.draft',
                    'permissions' => ['draft_post'],
                ],
                [
                    'name' => 'Scheduled',
                    'url' => route('user.post.scheduled'),
                    'active_url' => 'user.post.scheduled',
                    'permissions' => ['scheduled_post'],
                ],
            ],
        ],
        [
            'name' => 'Linkedin Self',
            'active_url' => 'user.individual-post.*',
            'icon' => asset('assets/images/icons/arrow-right.png'),
            'permissions' => ['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post', 'immediate_post'],
            'submenu' => [
                [
                    'name' => 'Create',
                    'url' => route('user.individual-post.create'),
                    'active_url' => 'user.individual-post.create',
                    'permissions' => [
                        'linkedin_text_post',
                        'linkedin_image_post',
                        'linkedin_video_post',
                        'immediate_post',
                    ],
                ],
                [
                    'name' => 'Published',
                    'url' => route('user.individual-post.index'),
                    'active_url' => 'user.individual-post.index',
                    'permissions' => ['immediate_post'],
                ],
                [
                    'name' => 'Draft',
                    'url' => route('user.individual-post.draft'),
                    'active_url' => 'user.individual-post.draft',
                    'permissions' => ['draft_post'],
                ],
                [
                    'name' => 'Scheduled',
                    'url' => route('user.individual-post.scheduled'),
                    'active_url' => 'user.individual-post.scheduled',
                    'permissions' => ['scheduled_post'],
                ],
            ],
        ],
        // [
        //     'name' => 'Leads',
        //     'active_url' => 'user.linkedin.leads.*',
        //     'icon' => asset('assets/images/icons/arrow-right.png'),
        //     'submenu' => [
        //         [
        //             'name' => 'Sales Navigator',
        //             'url' => route('user.linkedin.leads.sales-navigator.index'),
        //             'active_url' => 'user.linkedin.leads.sales-navigator.*',
        //         ],
        //     ],
        // ],
        // [
        //     'name' => 'Pipelines',
        //     'active_url' => 'user.linkedin.pipeline.*',
        //     'icon' => asset('assets/images/icons/arrow-right.png'),
        //     'submenu' => [
        //         [
        //             'name' => 'B1',
        //             'url' => route('user.linkedin.pipeline.index'),
        //             'active_url' => 'user.linkedin.pipeline.index',
        //         ],
        //         [
        //             'name' => 'B2',
        //             'url' => route('user.linkedin.pipeline.index'),
        //             'active_url' => '',
        //         ],
        //         [
        //             'name' => 'B3',
        //             'url' => route('user.linkedin.pipeline.index'),
        //             'active_url' => '',
        //         ],
        //     ],
        // ],
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
                        @if (isset($item['permissions']))
                            @if (Auth::guard('web')->user()->canAny($item['permissions']))
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
                                                    @if (isset($item['permissions']))
                                                        @if (Auth::user()->canAny($item['permissions']))
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
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @else
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
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        @endif
                    @else
                        @if (isset($item['permissions']))
                            @if (Auth::user()->canAny($item['permissions']))
                                <li
                                    class="menu-item {{ Str::is($item['active_url'], Route::currentRouteName()) ? 'active' : '' }}">
                                    <a href="{{ $item['url'] }}">
                                        <span class="menu-icon">
                                            <img src="{{ $item['icon'] }}" alt=""></span>
                                        <span>{{ $item['name'] }}</span>
                                    </a>
                                </li>
                            @endif
                        @else
                            <li
                                class="menu-item {{ Str::is($item['active_url'], Route::currentRouteName()) ? 'active' : '' }}">
                                <a href="{{ $item['url'] }}">
                                    <span class="menu-icon">
                                        <img src="{{ $item['icon'] }}" alt=""></span>
                                    <span>{{ $item['name'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</div>
