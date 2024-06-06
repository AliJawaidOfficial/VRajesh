<header class="top-header d-flex w-100 align-items-end justify-content-end gap-3">


    <div class="d-flex align-items-center alert-wrapper">
        <div class="toggle-button">
            <button class="hamburger" id="toggleSidebar">
                <img src="{{ asset('assets/images/icons/hamberger.png') }}" style="filter: invert(100%)" alt=""></button>
        </div>

        <div class="notifications-wrapper">
            <button class="alert-icon position-relative" type="button" id="notificationButton" data-bs-toggle="dropdown"
                aria-expanded="false">
                <img src={{ asset('assets/images/icons/Notification.png') }} style="filter: invert(100%)"
                    alt="Notifications">
                <span class="badge d-flex align-items-center justify-content-center">05</span>
                <!-- Add the badge here -->
            </button>
            <ul class="dropdown-menu" aria-labelledby="notificationButton">
                <li><a class="dropdown-item" href="#">Notification 1</a></li>
                <li><a class="dropdown-item" href="#">Notification 2</a></li>
                <li><a class="dropdown-item" href="#">Notification 3</a></li>
            </ul>
        </div>
    </div>

    <div class="user-dropdown-wrapper">
        <div class="d-flex align-items-center">
            <div class="user-info d-flex flex-column align-items-end">
                <span class="user-name">{{ Auth::guard('admin')->user()->first_name }} {{ Auth::guard('admin')->user()->last_name }}</span>
                <span class="user-role">Admin</span>
            </div>
            <button class="user-avatar position-relative" type="button" id="profileButton" data-bs-toggle="dropdown"
                aria-expanded="false">
                <span class="user-name-badge d-flex align-items-center justify-content-center">
                    {{ substr(Auth::guard('admin')->user()->first_name, 0, 1) . substr(Auth::guard('admin')->user()->last_name, 0, 1) }}
                </span>
                <span class="online"></span>
            </button>
            <ul class="dropdown-menu profile-user-dropdown-content-wrapper" aria-labelledby="profileButton">
                <li><a href="{{ route('admin.logout') }}" class="dropdown-item">Logout</a></li>
            </ul>

        </div>
    </div>
</header>
