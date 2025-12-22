<?php
$header_type = 'rt-transparent';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::check() ? Auth::id() : '' }}">
    <title>Maylee Realtor</title>
    <link rel="shortcut icon" href="{{ asset('frontend/assets/images/favicon.webp') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Poppins:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Sirivennela&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/aos.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.2.10/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">

    {!! ToastMagic::styles() !!}

    @yield('styles')
</head>

<body>

    @include('layouts.frontend.header')


    @yield('contents')


    @include('layouts.frontend.footer')

    @include('layouts.frontend.login-signup')
    <!-- Assessment Popup -->
    <div id="rt-inperson-assessment-popup" class="rt-inperson-assessment-popup">
        <div class="popup-wrapper">
            <div class="rt-btn-close" id="rt-popup-close">
                <i class="fa-solid fa-x"></i>
            </div>
            <div class="popup-heading">
                <h3>Request Inperson Assessment</h3>
            </div>
            <div class="popup-body">
                <form action="">
                    <div class="rt-form-field d-block w-100">
                        <input type="text" name="assess-reqt-name" id="assess-reqt-name" placeholder="Enter Name">
                        <div class="error-msg"></div>
                    </div>
                    <div class="rt-form-field d-block w-100">
                        <input type="email" name="assess-reqt-email" id="assess-reqt-email" placeholder="Enter Email">
                        <div class="error-msg"></div>
                    </div>
                    <div class="rt-form-field d-block w-100">
                        <input type="text" name="assess-reqt-phone" id="assess-reqt-phone"
                            placeholder="Enter Phone No">
                        <div class="error-msg"></div>
                    </div>
                    <div class="rt-form-field d-block w-100">
                        <input type="date" name="assess-reqt-date" id="assess-reqt-date" placeholder="Select Date">
                        <div class="error-msg"></div>
                    </div>
                    <div class="rt-form-field d-block w-100">
                        <select name="assess-reqt-time" id="assess-reqt-time" class="rt-select">
                            <option value="">Select Time</option>
                            <option value="">12:30PM</option>
                            <option value="">12:45PM</option>
                            <option value="">1:00PM</option>
                            <option value="">1:15PM</option>
                            <option value="">1:30PM</option>
                            <option value="">1:45PM</option>
                        </select>
                        <div class="error-msg"></div>
                    </div>
                    <div class="rt-form-field d-block w-100">
                        <textarea name="assess-reqt-msg" id="assess-reqt-msg" placeholder="Enter Message"></textarea>
                    </div>
                    <div class="rt-form-field d-block w-100">
                        <label for="reqt-consent" class="rt-checkbox">
                            <input class="rt_checkbox_input" type="checkbox" id="reqt-consent">
                            <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94"
                                    rx="3"></rect>
                                <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                    stroke-width="4" d="M4 10l5 5 9-9"></path>
                            </svg>
                            <span class="rt_checkbox_label">I consent to be contacted regarding this property.</span>
                        </label>
                        <div class="error-msg"></div>
                    </div>
                    <div class="rt-form-field d-block w-100">
                        <input type="button" value="Schedule">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assessment Popup -->
    
    <section class="social">
        <ul>
            <li id="facebook">
                <a href="#">
                   <i class="fa-brands fa-facebook-f"></i>Facebook
                </a>
            </li>
        </ul>
         <ul>
            <li id="linkedin">
                <a href="#">
                    <i class="fa-brands fa-linkedin"></i>Linkedin
                </a>
             </li>
        </ul>
        <ul>
            <li id="instagram">
               <a href="#">
                  <i class="fa-brands fa-instagram"></i>Instagram
                </a>
            </li>
        </ul>
        <ul>
            <li id="whatsapp">
               <a href="#">
                  <i class="fa-brands fa-whatsapp"></i>Whatsapp
                </a>
            </li>
        </ul>
    </section>
    
    
    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/aos.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11.2.10/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="{{ asset('frontend/assets/js/app.js') }}"></script>

    {!! ToastMagic::scripts() !!}

    @yield('script')
    
</body>

</html>
