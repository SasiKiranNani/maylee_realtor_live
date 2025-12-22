@extends('layouts.frontend.index')

@section('contents')
    <div class="rt-sell-page d-block w-100 position-relative">
        <div class="rt-page-hero-section position-relative mb-5">
            <div class="container" style="z-index: 1;">
                <h1 class="main-title">What's my home worth?</h1>
                <p class="description">Find your home's value in today's market.</p>    <!-- Search Bar Section -->
                <div class="rt-property-filter-section-sell">
                    <div class="container">
                        <div class="rt-property-filters">
                            <form action="{{ url()->current() }}" method="GET" id="sell-search-form">
                                <div class="row justify-content-center align-items-end gy-3">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="city-search">Search City</label>
                                            <input type="text" id="city-search" name="search"
                                                value="{{ request('search', 'Mississauga') }}" class="input-search px-3"
                                                placeholder="Search city (e.g., Ajax, Ontario, Canada)" autocomplete="off">
                                            <div id="city-suggestions" class="city-suggestions-list" style="display: none;">
                                                <!-- City suggestions will be populated here -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            class="button-group h-100 d-flex flex-wrap justify-content-center align-items-center gap-4 gap-md-4">
                                            <button type="submit" class="btn-search">
                                                <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                                <span class="text">Search</span>
                                            </button>
                                            <a href="{{ url()->current() }}" class="btn-save-search">
                                                <span class="icon"><i class="fa-solid fa-refresh"></i></span>
                                                <span class="text">Reset</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recommended Properties -->
        <section class="rt-recommended-properties sec-pad overflow-hidden bg-white">
            <div class="container">
                <div class="rt-section-title-wrap d-block w-100">
                    <h2 class="main-title">Recently Sold Properties</h2>
                </div>
                <div class="swiper rt-property-carousel rt-carousel top-right-nav pb-2">
                    <div class="swiper-wrapper">
                        @foreach ($soldProperties as $property)
                            <div class="swiper-slide">
                                <div class="rt-property-item sell rt-box-shadow-1">
                                    <div class="rt-property-header">
                                        <figure class="rt-propert-image">
                                            <img src="{{ $property['image'] }}" alt="{{ $property['address'] }}"
                                                onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">
                                        </figure>
                                        <span class="property-type-lable">sold</span>
                                        <!--      <span class="property-wishlist" data-listing-key="{{ $property['ListingKey'] ?? '' }}"-->
                                        <!--    data-transaction-type="{{ $property['TransactionType'] ?? 'For Sale' }}">-->
                                        <!--    <i class="fa-regular fa-heart"></i>-->
                                        <!--</span>-->
                                    </div>
                                    <div class="rt-property-body">
                                        <h2 class="property-price">{{ $property['price'] }}</h2>
                                        <p class="property-location">{{ $property['FullAddress'] ?? $property['address'] }}</p>
                                        <div class="property-meta">
                                            <ul class="aminity-list">
                                                <li class="aminity-item">
                                                    <img src="{{ asset('frontend/assets/images/icons/bed.svg') }}">
                                                    <span>{{ $property['bedrooms'] }} Bedrooms</span>
                                                </li>
                                                <li class="aminity-item">
                                                    <img src="{{ asset('frontend/assets/images/icons/bathroom.svg') }}">
                                                    <span>{{ $property['bathrooms'] }} Bathrooms</span>
                                                </li>
                                                <li class="aminity-item">
                                                    <img
                                                        src="{{ asset('frontend/assets/images/icons/measure-ruler.svg') }}">
                                                    <span>{{ $property['area'] }}</span>
                                                </li>
                                                <li class="aminity-item">
                                                    <img src="{{ asset('frontend/assets/images/icons/clock.svg') }}">
                                                    <span>{{ $property['daysOnMarket'] }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="property-footer">
                                        <a href="{{ route('sold.details', $property['ListingKey']) }}" class="btn-property-explore">Explore Property<span
                                                class="icon"><i class="fa-solid fa-angles-up"></i></span></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Navigation & Pagination -->
                    <div class="rt-carousel-nav swiper-button-prev">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <div class="rt-carousel-nav swiper-button-next">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </section>
        <!-- Sold Properties End -->
        <section class="position-relative sec-pad bg-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xs-12 col-sm-12 col-md-6 pb-3 pb-md-0">
                        <img src="{{ asset('frontend/assets/images/2149198844.webp') }}" class="rounded-3">
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <h2 class="text-capitalize rt-primary-color mb-4"><b class="text-dark">What's your home worth?</b></h2>
                        <h5 class="text-uppercase fw-bold">Free Property Valuation</h5>
                        <p>Planning to sell your home? Knowing its current market value is crucial to set the right asking
                            price and attract serious buyers.</p>
                        <p class="fw-bold">Receive a detailed, free analysis of comparable properties close to your
                            location.</p>
                        <p>In today’s competitive market, pricing your home correctly is key to a successful sale. With my
                            expertise in the Comparative Market Analysis, Income Approach, and Cost Approach method, I can
                            help determine the best price to attract buyers and maximize your return. Curious about what
                            your home is worth now? Simply complete the form below, and I’ll provide you a FREE
                            comprehensive Home Evaluation Report.</p>
                        <a href="#estimation-form" class="rt-btn btn-pink btn-outline mt-5">Get Home Estimate <span
                                class="icon"><i class="fa-solid fa-house"></i></span></a>
                    </div>
                </div>
            </div>
        </section>
        <!-- About Section -->
        <section class="d-block w-100 position-relative sec-pad overflow-hidden bg-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 col-sm-12 pb-5 pb-md-0">
                        <div class="about-me-block d-block w-100">
                            <h1 class="name">Want to Speak to <span class="fw-bolder">Ms. May Lee</span>?</h1>
                            <div class="short-bio my-5">
                                <p>I have access to comprehensive sold listings, market statistics, and valuable insights
                                    that enable a highly accurate property valuation. Connect with me today to leverage this
                                    expertise.</p>
                            </div>
                            <a href="#" id="open-assessment-popup" class="rt-btn btn-pink btn-outline">FREE inperson
                                assessment <span class="icon"><i class="fa-solid fa-house"></i></span></a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="rt-agent-details-widget rt-bg-1 p-4">
                            <div class="profile-pic mb-2">
                                <img src="{{ asset('assets/maylee-small.jpg') }}">
                            </div>
                            <div class="profile-info">
                                <h3>May Lee</h3>
                                <p>Sales Representative At Right At Home Realty</p>
                                <ul class="contact-info">
                                    <li>
                                        <a href="tel:+19056957888">
                                            <span class="icon">
                                                <i class="fa-solid fa-phone"></i>
                                            </span>
                                            <span class="text">(905) 695 7888</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="mailto:may.lee@mayleerealtor.com">
                                            <span class="icon">
                                                <i class="fa-solid fa-envelope-open-text"></i>
                                            </span>
                                            <span class="text"> may.lee@mayleerealtor.com</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- About Section End -->
        <!-- Home Estimation form section -->
        <section id="estimation-form" class="d-block w-100 position-relative sec-pad">
            <div class="container">
                <div class="rt-section-title-wrap text-center d-block w-100 mb-5">
                    <h2 class="main-title mb-3">Get Your Free Home Value Estimate</h2>
                    <p>Thinking of selling your home? Discover what your property could sell for in today’s market. Simply
                        fill out the form below, and our real estate experts will provide you with a personalized,
                        no-obligation home value estimate to help you make informed decisions.</p>
                </div>
                <div class="rt-sell-home-estimation-form">
                    <form action="{{ route('sell.store') }}" method="POST" id="sell-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="rt-form-field">
                                    <label for="sell_property_address">address of the property you want to sell.<span class="text-danger">*</span></label>
                                    <input type="text" name="sell_property_address" id="sell_property_address"
                                        placeholder="Please enter the address of the property you want to sell." required>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <label for="sell_property_type">What Type Of Property?<span class="text-danger">*</span></label>
                                    <select name="sell_property_type" id="sell_property_type" class="rt-select">
                                        <option value="">Select a property type</option>
                                        <option value="Detached Home">Detached Home</option>
                                        <option value="Semi Detached Home">Semi Detached Home</option>
                                        <option value="Bungalow">Bungalow</option>
                                        <option value="Row Townhouse">Row Townhouse</option>
                                        <option value="Condos">Condos</option>
                                        <option value="Link Home">Link Home</option>
                                        <option value="Stacked">Stacked</option>
                                        <option value="Duplex">Duplex</option>
                                        <option value="Triplex">Triplex</option>
                                        <option value="Fourflex">Fourflex</option>
                                        <option value="Loft">Loft</option>
                                    </select>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <label for="sell_property_sqft">How many square feet is your home?<span class="text-danger">*</span></label>
                                    <input type="number" name="sell_property_sqft" id="sell_property_sqft"
                                        placeholder="Enter an estimate" required>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="rt-form-field">
                                    <label for="">How many bedrooms and bathrooms does your home have?<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <input type="number" name="sell_property_bedrooms" id="sell_property_bedrooms"
                                        placeholder="No. of Bedrooms" required>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <input type="number" name="sell_property_bathrooms" id="sell_property_bathrooms"
                                        placeholder="No. of Bathrooms" required>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <label for="sell_property_condition">Can you tell us about the condition of your
                                        home?</label>
                                    <select name="sell_property_condition" id="sell_property_condition"
                                        class="rt-select">
                                        <option value="">Select a condition of your property</option>
                                        <option value="Excellent (Move-in Ready)">Excellent (Move-in Ready)</option>
                                        <option value="Good (Minor Repairs Needed)">Good (Minor Repairs Needed)</option>
                                        <option value="Fair (Some Renovations Required)">Fair (Some Renovations Required)
                                        </option>
                                        <option value="Poor (Major Repairs Needed)">Poor (Major Repairs Needed)</option>
                                        <option value="New Construction">New Construction</option>
                                        <option value="Under Renovation">Under Renovation</option>
                                    </select>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <label for="sell_property_relocating">Are you relocating locally or out of the
                                        area?</label>
                                    <select name="sell_property_relocating" id="sell_property_relocating"
                                        class="rt-select">
                                        <option value="">Select your answer</option>
                                        <option value="Locally">Locally</option>
                                        <option value="Out of the Area">Out of the Area</option>
                                        <option value="Not Sure Yet">Not Sure Yet</option>
                                    </select>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <label for="house_construct_year">In which year was the house constructed?</label>
                                    <input type="number" name="house_construct_year" id="house_construct_year"
                                        placeholder="Enter Year">
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <label for="house_construct_year">Are you interested in any other services?</label>
                                    <select name="sell_property_service" id="sell_property_service" class="rt-select">
                                        <option value="">Select Service</option>
                                        <option value="Appraisal">Appraisal</option>
                                        <option value="Staging">Staging</option>
                                        <option value="Renovation">Renovation</option>
                                        <option value="Cleaning">Cleaning</option>
                                        <option value="Repairs & Maintenance Coordination">Repairs & Maintenance
                                            Coordination</option>
                                        <option value="Other Services">Other Services</option>
                                    </select>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="rt-form-field">
                                    <label for="sell_property_mortgage_balance">Do you currently have a mortgage on the
                                        property? If yes, approximate balance.(optional)</label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <input type="number" name="sell_property_mortgage_balance"
                                        id="sell_property_mortgage_balance" placeholder="Enter balance">
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="rt-form-field">
                                    <label for="">Let’s get your property uprise by Professionals — share your
                                        contact.<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="rt-form-field">
                                    <input type="text" name="sell_property_user_name" id="sell_property_user_name"
                                        placeholder="Your full name" required>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <input type="email" name="sell_property_user_email" id="sell_property_user_email"
                                        placeholder="Your email address" required>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 mb-3">
                                <div class="rt-form-field">
                                    <input type="text" name="sell_property_user_phone" id="sell_property_user_phone"
                                        placeholder="Your phone number" required>
                                    <div class="error-msg"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="rt-form-field">
                                    <label for="">Please upload upto 20 images</label>
                                </div>
                                <!-- Clickable upload area -->
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                                        <div class="rt-files-upload-field mb-3" id="uploadArea">
                                            <input class="file-input" type="file" name="files[]" id="fileInput" multiple
                                                accept="image/*" hidden>
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>Browse File to Upload</p>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 d-flex justify-content-center align-items-center">
                                        <button type="submit" class="rt-btn-submit">
                                    <span class="text">Submit</span>
                                    <span class="icon">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                </button>
                                    </div>
                                </div>
                                <div class="rt-files-upload-preview row" id="imagePreviewContainer">

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <!-- Home Estimation form section End -->
        <!-- Why choose Us -->
        <section class="d-block w-100 position-relative sec-pad bg-white why">
            <div class="container">
                <div class="rt-section-title-wrap text-center d-block w-100 mb-5">
                    <h2 class="main-title">Why Choose Us?</h2>
                    <p>A Professional Realtor to Sell or Buy Your Home in Canada?</p>
                </div>
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/peace-of-mind-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Peace of Mind</h3>
                                <p>I provide expert guidance and handle every detail—from market analysis and pricing to
                                    marketing, showings, legal documents, and negotiations—ensuring you sell your home with
                                    complete peace of mind.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box style-2">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/trust-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Trust</h3>
                                <p>With extensive GTA experience, I manage every detail of your home sale—from coordinating
                                    photos, videos, and contractors to scheduling showings—ensuring a smooth, stress-free
                                    process.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/advice-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Thoughtful Advice</h3>
                                <p>I am a professional realtor offers unbiased, thoughtful guidance, helping you make clear
                                    decisions without emotional bias, ensuring smooth transactions and the best outcome
                                    throughout your home selling journey.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box style-2">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/negotiation-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Negotiation</h3>
                                <p>Skilled negotiation ensures you get the best deal. I advocate for your interests,
                                    leverage market insights, and navigate offers strategically to secure optimal terms and
                                    a successful sale.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/marketing-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Marketing</h3>
                                <p>Whether you’re selling a single-family home, condo, duplex, or cottage, I quickly connect
                                    your property with a network of qualified buyers using innovative marketing strategies
                                    and deep market expertise to accelerate your sale.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="rt-icon-content-box style-2">
                            <div class="icon">
                                <img src="{{ asset('frontend/assets/images/icons/security-icon.webp') }}">
                            </div>
                            <div class="content">
                                <h3>Security</h3>
                                <p>Ontario realtors follow strict TRREB rules under the Real Estate Brokerage Act, hold
                                    licenses, complete accredited training, maintain liability insurance, and update skills
                                    regularly to ensure secure, professional transactions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Why Choose Us End -->
        <!-- Steps to sell home -->
        <section class="d-block w-100 postion-relative sec-pad overflow-hidden basic">
            <div class="container">
                <div class="rt-section-title-wrap text-center d-block w-100 mb-5">
                    <h2 class="main-title">10 Basic Steps to Sell Your Home</h2>
                    <p>I guide you through seven easy, clear steps—from preparing your property and pricing it right to
                        marketing, showings, negotiating, and closing—ensuring a smooth, successful sale every time.</p>
                </div>
                <div class="row row-gap-4">
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">1</div>
                            <h3>Prepare for Selling Your Home & Understand Potential Costs</h3>
                            <p>Decide to sell and be aware of all possible expenses, helping you plan and avoid surprises
                                throughout your home selling journey.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">2</div>
                            <h3>Book Your Personalized Consultation</h3>
                            <p>Schedule a one-on-one meeting to discuss your goals and create a tailored selling plan just
                                for you.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">3</div>
                            <h3>Set the Right Price for Your Home</h3>
                            <p>I help you price your home competitively using market insights to attract serious buyers and
                                maximize returns.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">4</div>
                            <h3>Get Your Home Ready for Sale</h3>
                            <p>Prepare your home effectively to make a strong first impression and boost its market appeal.
                            </p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">5</div>
                            <h3>Reach Qualified Buyers Quickly</h3>
                            <p>I market your property strategically, sharing details with a network of interested and
                                qualified buyers to speed up your sale.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">6</div>
                            <h3>Organize Convenient Showings</h3>
                            <p>I coordinate flexible and efficient showings to showcase your home to potential buyers
                                without hassle.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">7</div>
                            <h3>Start Negotiations & Review Offers</h3>
                            <p>I negotiate on your behalf to secure the best possible deal while keeping your priorities
                                front and center.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">8</div>
                            <h3>Manage the Under Contract Process</h3>
                            <p>Once under contract, I guide you through every step ensuring a smooth transaction until
                                closing.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">9</div>
                            <h3>Navigate the Conditional Phase with Confidence</h3>
                            <p>Understand and fulfill all conditions smoothly, protecting your interests during this
                                important period.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="rt-steps-item">
                            <div class="steps-number">10</div>
                            <h3>Successfully Close Your Home Sale</h3>
                            <p>I support you through closing, ensuring all paperwork and details are handled for a
                                stress-free final step.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Steps to sell home End -->
        <!-- Documents Section -->
        <section class="d-block w-100 sec-pad position-relative overflow-hidden bg-white">
            <div class="container">
                <div class="rt-section-title-wrap text-center d-block w-100 mb-5">
                    <h2 class="main-title">Basic Documents to Prepare When Selling Your Home.</h2>
                    <p>Before our first meeting, I kindly ask you to prepare a few key documents in advance to help us
                        streamline the listing process and get started smoothly.</p>
                </div>
                <div class="rt-accordions two-column">
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Title Deeds and Notarial Documents</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Proof of ownership including deed of sale, mortgage discharge, servitudes, and prior loan
                                agreements to ensure a clear transfer of title.</p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Certificate of Location</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>A recent survey document detailing property boundaries, structures, and legal encumbrances;
                                valid for up to 10 years and needed for the sale.</p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Municipal, School, and Water Tax Statements</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Statements showing current tax obligations help buyers budget and understand ongoing property
                                costs.</p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Work Invoices and Repair Documents</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Records of repairs, renovations, warranties, permits, and inspections that provide
                                transparency about the property’s condition.</p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Utility Bills (Electricity, Gas, Water)</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Recent bills to inform buyers of average monthly energy costs.</p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Lease Agreements and Renewal Notices (if rented)</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Documentation of existing tenant leases and renewal terms, essential for investment
                                properties.</p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Co-ownership Documents (for condos)</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Includes declaration of co-ownership, financial statements, rules, and maintenance records
                                detailing the condo's governance.</p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Estate Documents (if selling on behalf of an estate)</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Authorization documentation such as wills, death certificates, or liquidator confirmations.
                            </p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Proof of Identity</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Government-issued photo ID verified by your realtor for legal purposes.</p>
                        </div>
                    </div>
                    <div class="rt-accordion-item">
                        <div class="rt-accordion-header">
                            <span class="title">Additional Documents</span>
                            <span class="icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="rt-accordion-body">
                            <p>Optional but useful documents like inspection reports, appraisal reports, and legal
                                disclosures to speed up the sale process.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Documents Section End -->
        <!-- Conatct Section -->
        <section class="rt-home-contact-sec sec-pad position-relative overflow-hidden">
            <div class="container">
               <x-contact source="Sell" :city="$searchedCity ?? request('search')" />
            </div>
        </section>
        <!-- Conatct Section End -->
    </div>
@endsection
@section('script')
    <!-- jQuery UI for autocomplete -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    
     <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('imagePreviewContainer');

            // Open file picker when clicking anywhere on the upload area
            uploadArea.addEventListener('click', () => fileInput.click());

            // Handle file selection
            fileInput.addEventListener('change', function () {
                const files = this.files;

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];

                    // Only process image files
                    if (!file.type.startsWith('image/')) continue;

                    const reader = new FileReader();

                    reader.onload = function (e) {
                        // Create preview HTML exactly like your existing ones
                        const previewItem = document.createElement('div');
                        previewItem.className = 'col-xs-4 col-sm-4 col-md-3 col-lg-2 file-preview-item';

                        previewItem.innerHTML = `
                                                <img src="${e.target.result}" alt="Preview">
                                                <a href="#" class="btn-remove-file" onclick="this.parentElement.remove(); return false;">
                                                    <i class="fa-solid fa-x"></i>
                                                </a>
                                            `;

                        // Insert new preview before the end (you can also prepend)
                        previewContainer.appendChild(previewItem);
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Optional: Drag & Drop support (nice bonus)
            ['dragover', 'dragenter'].forEach(evt => {
                uploadArea.addEventListener(evt, e => {
                    e.preventDefault();
                    uploadArea.style.borderColor = '#007bff';
                    uploadArea.style.backgroundColor = '#e7f3ff';
                });
            });

            ['dragleave', 'dragend'].forEach(evt => {
                uploadArea.addEventListener(evt, e => {
                    e.preventDefault();
                    uploadArea.style.borderColor = '#ccc';
                    uploadArea.style.backgroundColor = '#fafafa';
                });
            });

            uploadArea.addEventListener('drop', e => {
                e.preventDefault();
                uploadArea.style.borderColor = '#ccc';
                uploadArea.style.backgroundColor = '#fafafa';

                const droppedFiles = e.dataTransfer.files;
                if (droppedFiles.length) {
                    fileInput.files = droppedFiles;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });
        });

        // City Search Autocomplete using jQuery UI (matching property-listing implementation)
        $(document).ready(function () {
            // Initialize autocomplete for city search using local city data
            $('#city-search').autocomplete({
                source: function (request, response) {
                    var term = request.term.toLowerCase();

                    // Use local GTA cities data
                    const gtaCities = @json($gtaCities ?? []);

                    // Filter cities that start with the search term
                    const matchingCities = gtaCities.filter(function (city) {
                        return city.toLowerCase().startsWith(term);
                    }).slice(0, 8); // Limit to 8 results

                    response(matchingCities.map(function (city) {
                        return {
                            label: city + ', Ontario, Canada',
                            value: city,
                            city: city
                        };
                    }));
                },
                minLength: 2, // Start suggesting after 2 characters
                delay: 300, // Debounce for better UX
                select: function (event, ui) {
                    // Set the selected city value
                    $(this).val(ui.item.city);
                    return false;
                },
                open: function () {
                    $(this).autocomplete('widget').css('z-index', 9999);
                }
            }).autocomplete('instance')._renderItem = function (ul, item) {
                var label = item.label.replace(new RegExp('(' + this.term + ')', 'gi'), '<strong>$1</strong>');
                return $('<li>')
                    .append('<div>' + label + '</div>')
                    .appendTo(ul);
            };
        });

        // Handle sell search form submission
        const sellSearchForm = document.getElementById('sell-search-form');
        if (sellSearchForm) {
            sellSearchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const cityInput = document.getElementById('city-search');
                const city = cityInput.value.trim() || 'Mississauga';
                window.location.href = `/sell/${encodeURIComponent(city)}`;
            });
        }
        
        // Step Number Animations
        document.addEventListener('DOMContentLoaded', function () {
            const stepNumbers = document.querySelectorAll('.steps-number');
            const animations = ['animate-fadeIn', 'animate-slideInLeft', 'animate-slideInRight', 'animate-bounceIn', 'animate-zoomIn', 'animate-rotateIn'];

            // Intersection Observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const element = entry.target;
                    const index = Array.from(stepNumbers).indexOf(element);
                    const animationClass = animations[index % animations.length];
                    if (entry.isIntersecting) {
                        element.classList.add(animationClass);
                        element.style.opacity = '1'; // Trigger animation
                    } else {
                        element.classList.remove(animationClass);
                        element.style.opacity = '0'; // Reset
                    }
                });
            }, { threshold: 0.5 });

            // Observe each step number
            stepNumbers.forEach(element => {
                observer.observe(element);
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .rt-property-filter-section-sell {
            margin-top: 60px;
        }
        .rt-property-filter-section-sell {
            position: absolute;
            width: 100%;
            bottom: -50px;
            justify-content: center;
            display: flex;
            left: 0%;
        }

        /* jQuery UI Autocomplete Styling */
        .ui-autocomplete {
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: inherit;
        }

        .ui-autocomplete .ui-menu-item {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .ui-autocomplete .ui-menu-item div {
            padding: 10px 15px;
            cursor: pointer;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
        }

        .ui-autocomplete .ui-menu-item:last-child div {
            border-bottom: none;
        }

        .ui-autocomplete .ui-menu-item div:hover,
        .ui-autocomplete .ui-state-active {
            background-color: var(--rt-button-color, #007bff);
            color: #4c418c;
        }

        .ui-autocomplete .ui-menu-item strong {
            font-weight: 600;
        }

        .ui-helper-hidden-accessible {
            display: none;
        }
        .file-preview-item img {
            height: 150px;
            width: 100%;
        }
        
        /* Button Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        @keyframes glow {
            0% { box-shadow: 0 0 5px rgba(255, 105, 180, 0.5), 0 0 0 0 rgba(255, 105, 180, 0.8); }
            50% { box-shadow: 0 0 20px rgba(255, 105, 180, 0.8), 0 0 0 10px rgba(255, 105, 180, 0); }
            100% { box-shadow: 0 0 5px rgba(255, 105, 180, 0.5), 0 0 0 0 rgba(255, 105, 180, 0.8); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        @keyframes slideBg {
            0% { transform: scaleX(0); }
            100% { transform: scaleX(1); }
        }

        @keyframes borderGlow {
            0% { border-color: rgba(255, 105, 180, 0.5); }
            50% { border-color: rgba(255, 105, 180, 1); }
            100% { border-color: rgba(255, 105, 180, 0.5); }
        }

        .rt-btn, .rt-btn-submit {
            animation: pulse 2s infinite ease-in-out;
            transition: all 0.5s ease-in-out;
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(255, 105, 180, 0.5);
            animation: borderGlow 2s infinite ease-in-out;
        }

        .rt-btn::after, .rt-btn-submit::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255, 105, 180, 0.8), rgba(255, 20, 147, 0.8));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.5s ease-in-out;
            z-index: -1;
        }

        .rt-btn:hover::after, .rt-btn-submit:hover::after {
            transform: scaleX(1);
        }

        .rt-btn:hover, .rt-btn-submit:hover {
            animation: bounce 1s ease, glow 1.5s infinite;
            transform: translateY(-5px);
            color: white;
            border-color: rgba(255, 105, 180, 1);
        }

        .rt-btn:active, .rt-btn-submit:active {
            animation: shake 0.5s ease;
            transform: translateY(0);
        }

        .rt-btn .icon, .rt-btn-submit .icon {
            transition: transform 0.3s ease;
        }

        .rt-btn:hover .icon, .rt-btn-submit:hover .icon {
            transform: rotate(360deg) scale(1.2);
        }

        .rt-btn::before, .rt-btn-submit::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .rt-btn:active::before, .rt-btn-submit:active::before {
            width: 300px;
            height: 300px;
        }
        
         /* Step Number Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideInLeft {
            from { transform: translateX(-100%) scale(0.5); opacity: 0; }
            to { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes slideInRight {
            from { transform: translateX(100%) scale(0.5); opacity: 0; }
            to { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes rotateIn {
            from { transform: rotate(-180deg) scale(0.5); opacity: 0; }
            to { transform: rotate(0deg) scale(1); opacity: 1; }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        @keyframes glow {
            0% { box-shadow: 0 0 5px rgba(255, 105, 180, 0.5); }
            50% { box-shadow: 0 0 20px rgba(255, 105, 180, 0.8), 0 0 30px rgba(255, 105, 180, 0.6); }
            100% { box-shadow: 0 0 5px rgba(255, 105, 180, 0.5); }
        }

        .animate-fadeIn {
            animation: fadeIn 2s ease-out;
        }

        .animate-slideInLeft {
            animation: slideInLeft 2s ease-out;
        }

        .animate-slideInRight {
            animation: slideInRight 2s ease-out;
        }

        .animate-bounceIn {
            animation: bounceIn 2s ease-out;
        }

        .animate-zoomIn {
            animation: zoomIn 2s ease-out;
        }

        .animate-rotateIn {
            animation: rotateIn 2s ease-out;
        }

        .steps-number {
            opacity: 0;
        }

        .rt-steps-item:hover .steps-number {
            animation: bounce 1s infinite, glow 2s infinite;
        }
    </style>
@endsection