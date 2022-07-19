

<header class="main-header">
    <div class="inside-header">
        <div class="d-flex align-items-center logo-box justify-content-between">
            <!-- Logo -->
            <a href="{{URL::to("/")}}" class="logo">
                <!-- logo-->
                <div class="logo-lg">
                    <span class="light-logo" style="color:#fff; font-size:35px; font-weight: bolder;">
                        {{ config("app.name") }}
                    </span>
                </div>
            </a>
        </div>
        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top pl-10">
            <!-- Sidebar toggle button-->
            <div class="app-menu">
                <!-- Nothing ToDo -->
            </div>

            <div class="navbar-custom-menu r-side">
                <ul class="nav navbar-nav">
                    <li class="btn-group nav-item d-lg-inline-flex d-none">
                        <a href="javascript:void(0)" data-provide="fullscreen" class="waves-effect waves-light nav-link rounded full-screen" title="Full Screen">
                            <i class="icon-Expand-arrows"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                    </li>

                    <!-- User Account-->
                    <li class="dropdown user user-menu">
                        <a href="#" class="waves-effect waves-light dropdown-toggle" data-toggle="dropdown" title="User">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                        <ul class="dropdown-menu animated flipInX">
                            <li class="user-body">
                                <a class="dropdown-item" href="{{ URL::to('logout') }}"><i class="ti-lock text-muted mr-2"></i> Logout</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</header>

<nav class="main-nav" role="navigation">

    <!-- Mobile menu toggle button (hamburger/x icon) -->
    <input id="main-menu-state" type="checkbox" />
    <label class="main-menu-btn" for="main-menu-state">
        <span class="main-menu-btn-icon"></span> Toggle main menu visibility
    </label>
    <!-- Sample menu definition -->
    <ul id="main-menu" class="sm sm-blue">


        <li class="{{ $current_module=="dashboard" ? "current" : "" }}">
            <a href="{{ URL::to("dashboard/view") }}">
                <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                Dashboard
            </a>
        </li>
        <li class="{{ $current_module=="categories" ? "current" : "" }}">
            <a href="{{ URL::to("categories/list") }}">
                <i class="icon-Box"><span class="path1"></span><span class="path2"></span></i>
                Categories
            </a>
        </li>
        <li class="{{ $current_module=="media" ? "current" : "" }}">
            <a href="{{ URL::to("media/list") }}">
                <i class="icon-Box"><span class="path1"></span><span class="path2"></span></i>
                Media
            </a>
        </li>
    </ul>
</nav>
