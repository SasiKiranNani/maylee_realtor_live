<footer class="rt-footer position-relative">
    <div class="container">
        <div class="footer-top position-relative">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="rt-footer-about-widget">
                        <img src="{{ asset('Maylee-Logo.png') }}"
                            class="footer-logo mb-1">
                        <p>Connecting you to your next home. We make selling, buying, and leasing property simple and
                            stress-free.</p>
                        <div class="rt-social-block">
                            <h3 class="title">Where I’m Active</h3>
                            <ul class="social-widget default-style rt-pink mt-3">
                                <li id="facebook"><a href="" target="_blank"><i
                                            class="fa-brands fa-facebook-f"></i></a></li>
                                <li id="linkedin"><a href="" target="_blank"><i
                                            class="fa-brands fa-linkedin-in"></i></a></li>
                                <li id="instagram"><a href="" target="_blank"><i
                                            class="fa-brands fa-instagram"></i></a></li>
                                <li id="whatsapp"><a href="" target="_blank"><i
                                            class="fa-brands fa-whatsapp"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-12">
                    <div class="rt-footer-menu-widget rt-footer-widget">
                        <div class="widget-title mb-3">
                            <h3>Services</h3>
                        </div>
                        <ul class="menu-list services-menu">
                            <li class="menu-item">
                                <a href="{{ route('buy') }}">Buying</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('sell') }}">Selling</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('lease') }}">Lease</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('neighbourhood') }}">Neighbourhood</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('our-service') }}">Services</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-12">
                    <div class="rt-footer-menu-widget rt-footer-widget">
                        <div class="widget-title mb-3">
                            <h3>Quick Links</h3>
                        </div>
                        <ul class="menu-list quick-menu">
                            <li class="menu-item">
                                <a href="{{ route('frontend.about-us') }}">About Us</a>
                            </li>
                            <!--<li class="menu-item">-->
                            <!--    <a href="#">Contact Us</a>-->
                            <!--</li>-->
                            <li class="menu-item">
                                <a href="{{ route('privacyPolicy') }}">Privacy Policy</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('termsAndConditions') }}">Terms & Conditions</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="rt-footer-subscribe-widget rt-footer-widget">
                        <div class="widget-title">
                            <h3>Never Miss a Home</h3>
                            <p>Subscribe for exclusive updates on new properties, price drops, and local market trends.
                            </p>
                        </div>
                        <div class="rt-subscribe-form-widget">
                            <form action="{{ route('subscribe') }}" method="POST">
                                @csrf
                                <div class="form-group position-relative">
                                    <input type="email" name="email" id="email" placeholder="Enter Email" required>
                                    <button type="submit">
                                        <i class="fa-brands fa-telegram"></i>
                                    </button>
                                </div>
                                <p class="note d-none">We value your trust. We promise to keep your information secure
                                    and will never sell or share it.</p>
                                <div class="form-group consent-block">
                                    <input type="checkbox" name="consent" id="consent" required>
                                    <label for="consent">By subscribing, I agree to receive emails from Realtor. We
                                        respect your privacy and will never share your data with any third
                                        party.</label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom position-relative">
            <div class="rt-disclaimer-wrap d-block pb-4">
                <p class="text-center align-items-center" style="color: var(--rt-accent-color);">
                    @Website powered by Lenora Enterprises in partnership with P&P Infotech.
                </p>
                <p class="mb-0">Copyright 2030 All rights reserved. Toronto Real Estate Board (TREB) assumes no
                    responsibility for the accuracy of any information shown. The information provided herein must only
                    be used by consumers that have a bona fide interest in the purchase, sale or lease of real estate
                    and may not be used for any commercial purpose or any other purpose.</p>
            </div>
            <div class="footer-copyright d-block w-100">
                <p class="mb-0"><i class="fa-regular fa-copyright"></i> <span id="current-year"></span> <span
                        class="heighlight">Maylee Inc</span>. All Rights Reserved</p>
            </div>
        </div>
    </div>
</footer>
