<header class="top-header d-flex w-100 justify-content-between">
    <div class="d-flex align-items-center alert-wrapper">
        <div class="toggle-button">
            <button class="hamburger" id="toggleSidebar"><img src="{{ asset('assets/images/icons/hamberger.png') }}"
                    alt=""></button>
        </div>
        {{-- <div class="notifications-wrapper">
            <button class="alert-icon position-relative" type="button" id="notificationButton" data-bs-toggle="dropdown"
                aria-expanded="false">
                <img src="{{ asset('assets/images/icons/Notification.png') }}" alt="Notifications">
                <span class="badge d-flex align-items-center justify-content-center">{{ count(getNotifications()) }}</span>
                <!-- Add the badge here -->
            </button>
            <ul class="dropdown-menu" aria-labelledby="notificationButton">
                @foreach (getNotifications() as $notification)
                    <li>
                        <a href="{{ URL::to($notification->link) }}" class="dropdown-item">
                            <div class="d-flex flex-column gap-1">
                                <p class="m-0 fw-bold">{{ $notification->title }}</p>
                                <small class="m-0">{{ $notification->discription }}</small>
                                <small class="m-0">{{ timeAgo($notification->created_at) }}</small>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div> --}}
    </div>
    <div class="user-dropdown-wrapper">
        <div class="d-flex align-items-center">
            @if (Session::get('linkedin_access_token'))
                <a class="btn btn-dark" href="{{ route('logout') }}">Logout</a>
            @endif
            {{-- <div class="user-info d-flex flex-column align-items-end">
                <span class="user-name">{{ Auth::user()->first_name }}
                    {{ Auth::user()->last_name }}</span>
                <span class="user-role">{{ Auth::user()->position }}</span>
            </div>=
            <button class="user-avatar position-relative" type="button" id="profileButton" data-bs-toggle="dropdown"
                aria-expanded="false">
                {{-- <span
                    class="user-name-badge d-flex align-items-center justify-content-center">{{ substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1) }}</span>
                <span class="online"></span>
            </button>
            <ul class="dropdown-menu profile-user-dropdown-content-wrapper" aria-labelledby="profileButton">
                {{-- <li><a class="dropdown-item" href="#">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
            </ul> --}}
        </div>
    </div>
</header>
