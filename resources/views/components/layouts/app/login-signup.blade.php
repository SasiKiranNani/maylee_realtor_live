  <!-- Sign in and sign up popup -->
    <div id="signin-signup-popup" class="rt-signin-signup-popup">
        <div class="wrapper">
            <div class="rt-btn-close" id="rt-popup-close">
                <i class="fa-solid fa-x"></i>
            </div>
            <div class="left-wrap">
                <img src="assets/images/maylee-realtor-logo-v2.png" alt="">
            </div>
            <div class="right-wrap">
                <!-- Sign In Form Block -->
                <div id="signin-form-block" class="form-block active">
                    <div class="form-header">
                        <h3 class="title">Welcome Back!</h3>
                        <p class="desc">Sign in to access your wishlist, compare properties, and explore more about
                            properties.</p>
                    </div>
                    <div class="form-body">
                        <form action="{{ route('user.login') }}" method="POST" class="signin-form" id="loginForm">
                            @csrf
                            <div class="form-group">
                                <label for="userName">
                                    Username Or Email Address <span class="required">*</span>
                                </label>
                                <input type="text" name="userName" id="userName">
                            </div>
                            <div class="form-group">
                                <label for="userPwd">
                                    Password <span class="required">*</span>
                                </label>
                                <input type="password" name="userPwd" id="userPwd">
                            </div>
                            <div class="d-block mb-4 remeber-block rt-checkbox-wrap">
                                <label for="loginRemember" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" id="loginRemember">
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">Remember Me</span>
                                </label>
                            </div>
                            <div class="form-button">
                                <input type="submit" value="Sign In">
                            </div>
                        </form>
                    </div>
                    <div class="form-footer">
                        <div class="link-block">
                            <p>Forgot your password <a href="{{ route('user.password.email') }}"
                                    id="resetpwd-form-link">Click here</a> to recover it
                            </p>
                        </div>
                        <div class="link-block">
                            <p>New here? <a href="{{ route('user.register') }}" id="signup-form-link">Sign Up</a></p>
                        </div>
                    </div>
                </div>
                <!-- Sign In Form Block End -->
                <!-- Sign Up Form Block -->
                <div id="signup-form-block" class="form-block">
                    <div class="form-header">
                        <h3 class="title">Create Your Free Account</h3>
                        <p class="desc">Sign up to save favorite properties, compare options, and explore more details
                            about properties.</p>
                    </div>
                    <div class="form-body">
                        <form action="{{ route('user.register') }}" method="POST" class="signin-form"
                            id="registerForm">
                            @csrf
                            <div class="form-group">
                                <label for="userFullName">
                                    Fullname <span class="required">*</span>
                                </label>
                                <input type="text" name="userFullName" id="userFullName">
                            </div>
                            <div class="form-group">
                                <label for="userEmail">
                                    Email <span class="required">*</span>
                                </label>
                                <input type="Email" name="userEmail" id="userEmail">
                            </div>
                            <div class="form-group">
                                <label for="userPassword">
                                    Password <span class="required">*</span>
                                </label>
                                <input type="password" name="userPassword" id="userPassword">
                            </div>
                            <div class="form-group">
                                <label for="userConfPassword">
                                    Confirm Password <span class="required">*</span>
                                </label>
                                <input type="password" name="userPassword_confirmation"
                                    id="userPassword_confirmation">
                            </div>
                            <div class="d-block mb-4 consent-block rt-checkbox-wrap">
                                <label for="loginRemember" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" id="loginRemember">
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">Creating an account means you accept our <a
                                            href="#" target="_blank">Terms</a> & <a href="#"
                                            target="_blank">Privacy Policy</a>.</span>
                                </label>
                            </div>
                            <div class="form-button">
                                <input type="submit" value="Sign Up">
                            </div>
                        </form>
                    </div>
                    <div class="form-footer">
                        <div class="link-block">
                            <p>Already have an account? <a href="{{ route('user.login') }}" id="signin-form-link">Sign
                                    In</a></p>
                        </div>
                    </div>
                </div>
                <!-- Sign Up Form Block End -->
                <!-- Reset Password Form Block -->
                <div id="resetpwd-form-block" class="form-block">
                    <div class="form-header">
                        <h3 class="title">Forgot Your Password?</h3>
                        <p class="desc">No worries — reset it in just a few steps.</p>
                    </div>
                    <div class="form-body">
                        <form action="{{ route('user.password.email') }}" method="POST" class="reset-password-form"
                            id="forgotForm">
                            @csrf
                            <div class="form-group">
                                <label for="userEmail">
                                    Email <span class="required">*</span>
                                </label>
                                <input type="Email" name="userEmail" id="userEmail">
                            </div>
                            <div class="form-button">
                                <input type="submit" value="Send Reset Link">
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Reset Password Form Block End -->
            </div>
        </div>
    </div>
    <!-- Sign in and sign up popup End -->
