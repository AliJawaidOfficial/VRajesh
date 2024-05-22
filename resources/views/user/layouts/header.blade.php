<header class="top-header d-flex w-100 justify-content-between">


    <div class="d-flex align-items-center alert-wrapper">
        <div class="toggle-button">
            <button class="hamburger" id="toggleSidebar"><img src="./assets/images/icons/hamberger.png"
                    alt=""></button>
        </div>

        <div class="notifications-wrapper">
            <button class="alert-icon position-relative" type="button" id="notificationButton" data-bs-toggle="dropdown"
                aria-expanded="false">
                <img src="./assets/images/icons/Notification.png" alt="Notifications">
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
                <span class="user-name">John Doe</span>
                <span class="user-role">Admin</span>
            </div>

            <button class="user-avatar position-relative" type="button" id="profileButton" data-bs-toggle="dropdown"
                aria-expanded="false">

                <!-- if Image exist show image -->
                <!-- <img src="./assets/images/icons/user-profile-icon.png" alt="User Avatar"> -->

                <!-- else show user name badge -->
                <span class="user-name-badge d-flex align-items-center justify-content-center">JD</span>


                <span class="online"></span>

            </button>

            <ul class="dropdown-menu profile-user-dropdown-content-wrapper" aria-labelledby="profileButton">
                <li><a class="dropdown-item" href="#">Users</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#">Logout</a></li>
            </ul>

        </div>
    </div>
</header>
