<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
<a href="{{ route('pos.index') }}" target="_blank" class="btn btn-primary">
                <i class="menu-icon tf-icons ti ti-cash"></i>
                <div>POS</div>
            </a>

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <li class="nav-item me-2 me-xl-0">
                <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                    <i id="modeIcon" class="ti ti-md"></i>
                </a>
            </li>

            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                    data-bs-toggle="dropdown">

                    <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>

                </a>
                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item" href="profile">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">My Account</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{route('settings.index')}}">
                            <i class="ti ti-settings me-2 ti-sm"></i>
                            <span class="align-middle">Settings</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form action="{{route('logout')}}" method="post">
                            @csrf
                            <button class="dropdown-item" type="submit"><i class="ti ti-logout me-2 ti-sm"></i>
                                <span class="align-middle">Log Out</span></button>
                        </form>

                    </li>
                </ul>
            </li>


        </ul>
    </div>

    <div class="navbar-search-wrapper search-input-wrapper d-none">
        <input type="text" class="form-control search-input container-xxl border-0"
            placeholder="Search..." aria-label="Search..." />
        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
    </div>
    
</nav>

  