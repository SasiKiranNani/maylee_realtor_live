    <header id="main-header" class="main-header <?php echo $header_type; ?> w-100">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-xl navbar-dark sticky-header z-10">
            <div
                class="container d-flex align-items-center justify-content-lg-between gap-5 position-relative">
                <a href="{{ route('home') }}" class="navbar-brand align-items-center mb-md-0 text-decoration-none">
                    <img src="{{ asset('Maylee-Logo.png') }}" alt="logo"
                        class="img-fluid logo-white">
                    <img src="{{ asset('Maylee-Logo.png') }}" alt="logo"
                        class="img-fluid logo-color">
                </a>
                <div class="collapse navbar-collapse pt-0 pt-lg-4">
                    <ul class="nav col-12 col-md-auto main-menu" id="main-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('buy') }}">Buy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sell') }}">Sell</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('lease') }}">Lease</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('neighbourhood') }}">Neighbourhood</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#">Others</a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('our-service') }}">Services</a></li>
                                <li><a href="{{ route('frontend.about-us') }}">About Us</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="pull-right d-flex align-items-center mt-0 mt-lg-3 gap-1 gap-md-3">
                    <!-- Call button -->
                    <a href="tel:+16478850114" class="rt-btn btn-pink btn-outline call-btn">(647) 885 0114 <span
                        class="icon"><i class="fa-solid fa-phone trin-trin"></i></span></a>


                     @if (Auth::check())
                    <!-- User Menu -->
                    <div class="rt-user-nav-widget">
                        <div class="toggle-btn">
                            <img
                                src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('frontend/assets/images/user.webp') }}">
                        </div>
                        <div class="user-dropdown">
                            <div class="dropdown-header">
                                <img
                                    src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('frontend/assets/images/user.webp') }}">
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                            <ul class="user-menu-list">
                                <li>
                                    <a href="{{ route('user.profile') }}">
                                        <span class="icon"><i class="fa-solid fa-user"></i></span>
                                        <span class="text">Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('wishlist') }}">
                                        <span class="icon"><i class="fa-solid fa-heart"></i></span>
                                        <span class="text">Wishlist</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                                        <span class="text">Logout</span>
                                    </a>

                                    <form id="logout-form" action="{{ route('user.logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>

                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- User Menu End -->
                @else
                    <!-- Quick chat button -->
                    <a href="#" id="open-signin-signup-popup" class="rt-btn book-btn">
                        Login/Register
                        <span class="icon"><i class="fa-solid fa-user"></i></span>
                    </a>
                @endif


                    <!-- Mobile Menu Start -->
                    <a href="#mobileOffCanvas" class="navbar-toggler border-0">
                        <span class="navbar-toggler-icon"><i class="fa-solid fa-bars"></i></span>
                    </a>
                    <!-- Mobile Menu End -->
                </div>
            </div>
        </nav>
        <!-- Navbar End -->
        <!-- Offcanvas Mobile Menu Start -->
        <div class="offcanvas offcanvas-mobile" tabindex="-1" id="mobileOffCanvas">
            <div class="offcanvas-header">
                <a href="#">
                    <img src="{{ asset('Maylee-Logo.png') }}" alt="logo"
                        class="img-fluid">
                </a>
                <a class="btn-close"><i class="fa-solid fa-x"></i></a>
            </div>
            <div class="offcanvas-body"></div>
        </div>
        <!-- Offcanvas Mobile Menu End -->
    </header>
