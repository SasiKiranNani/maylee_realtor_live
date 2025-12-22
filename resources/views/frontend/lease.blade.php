@extends('layouts.frontend.index')

@section('contents')
    <div class="page-wrapper rt-property-listing-page d-block w-100 position-relative">

        <!-- Property Filter -->
        <div class="rt-property-filter-section">
            <div class="container">
                <div class="rt-property-filters">
                    <form action="{{ url()->current() }}" method="GET" id="property-filter-form">
                        <div class="row justify-content-center align-items-end gy-3">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="property-search-phrase">Search By</label>
                                    <input type="text" name="search" id="property-search-phrase"
                                        value="{{ request('search') }}" class="input-search px-3"
                                        placeholder="Search by address, city, or postal code">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="property-type">Property Type</label>
                                    <select id="property-type" name="property_type">
                                        <option value="">Choose Property Type</option>
                                        @foreach ($propertySubTypes as $type)
                                            <option value="{{ $type }}"
                                                {{ request('property_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 px-4">
                                <div class="rt-price-slider">
                                    <div class="title-box">
                                        <span class="d-block">Price Range: </span>
                                        <input type="text" name="amount" id="amount"
                                            class="form-control-plaintext pt-0 pb-2 text-white" readonly
                                            value="${{ number_format(request('min_price', 0)) }} - ${{ number_format(request('max_price', $maxPrice)) }}">
                                        <input type="hidden" name="min_price" id="min_price"
                                            value="{{ request('min_price', 0) }}">
                                        <input type="hidden" name="max_price" id="max_price"
                                            value="{{ request('max_price', $maxPrice) }}">
                                    </div>
                                    <div id="price-slider"></div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="property-bedrooms">Bedroom</label>
                                    <select name="bedrooms" id="property-bedrooms">
                                        <option value="">Any</option>
                                        <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1</option>
                                        <option value="2" {{ request('bedrooms') == '2' ? 'selected' : '' }}>2
                                        </option>
                                        <option value="3" {{ request('bedrooms') == '3' ? 'selected' : '' }}>3
                                        </option>
                                        <option value="4" {{ request('bedrooms') == '4' ? 'selected' : '' }}>4
                                        </option>
                                        <option value="5" {{ request('bedrooms') == '5' ? 'selected' : '' }}>5
                                        </option>
                                        <option value="6+" {{ request('bedrooms') == '6+' ? 'selected' : '' }}>6+
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="property-bathrooms">Bathroom</label>
                                    <select name="bathrooms" id="property-bathrooms">
                                        <option value="">Any</option>
                                        <option value="1" {{ request('bathrooms') == '1' ? 'selected' : '' }}>1
                                        </option>
                                        <option value="2" {{ request('bathrooms') == '2' ? 'selected' : '' }}>2
                                        </option>
                                        <option value="3" {{ request('bathrooms') == '3' ? 'selected' : '' }}>3
                                        </option>
                                        <option value="4" {{ request('bathrooms') == '4' ? 'selected' : '' }}>4
                                        </option>
                                        <option value="5" {{ request('bathrooms') == '5' ? 'selected' : '' }}>5
                                        </option>
                                        <option value="6+" {{ request('bathrooms') == '6+' ? 'selected' : '' }}>6+
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                    <a href="#" id="advance-filter-toggle" class="rt-btn-advance-filter">
                                        <i class="fa-solid fa-sliders"></i>
                                        Advance Filters
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Property listing section -->
        <div class="rt-property-listing-wrap w-100 bg-white position-relative">
            <div class="rt-property-listing-withmap">
                <!-- Interactive Map -->
                <div class="property-list-map" id="interactiveMap" style="height: 1000px; z-index: 0"></div>
                <div id="map-warning" style="display: none; color: red; padding: 10px; text-align: center;">
                    Some properties may not be displayed due to missing coordinates.
                </div>

                <div class="property-list-wrap">
                    <div id="loading-indicator" style="display: none; text-align: center; padding: 20px;">
                        <i class="fa-solid fa-spinner fa-spin"></i> Loading properties...
                    </div>
                    <div class="rt-property-list rt-scrollbar" id="property-list-container">
                        @if (count($properties) > 0)
                            @foreach ($properties as $property)
                                <div class="rt-property-item" data-lat="{{ $property['Latitude'] }}"
                                    data-lng="{{ $property['Longitude'] }}" data-price="{{ $property['ListPrice'] }}"
                                    data-bedrooms="{{ $property['BedroomsTotal'] }}"
                                    data-bathrooms="{{ $property['BathroomsTotalInteger'] }}"
                                    data-type="{{ $property['PropertySubType'] }}" data-city="{{ $property['City'] }}"
                                    data-listing-key="{{ $property['ListingKey'] }}"
                                    data-transaction-type="{{ $property['TransactionType'] }}">
                                    <div class="rt-property-header">
                                        <figure class="rt-propert-image">
                                            @if (!empty($property['MediaURL']) && filter_var($property['MediaURL'], FILTER_VALIDATE_URL))
                                                <img src="{{ $property['MediaURL'] }}" alt="Property Image"
                                                    onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">
                                            @else
                                                <img src="{{ asset('frontend/assets/images/properties/property-1.jpg') }}"
                                                    alt="No Image">
                                            @endif
                                        </figure>
                                        <span class="property-wishlist">
                                            <i class="fa-regular fa-heart"></i>
                                        </span>
                                    </div>
                                    <div class="rt-property-body">
                                        <h2 class="property-price">{{ $property['FormattedPrice'] }}</h2>
                                        <p class="property-location">{{ $property['FullAddress'] }}</p>
                                        <div class="property-meta">
                                            <ul class="aminity-list">
                                                <li class="aminity-item">
                                                    <img src="{{ asset('frontend/assets/images/icons/bed.svg') }}">
                                                    <span>{{ $property['BedroomsTotal'] }} Bedrooms</span>
                                                </li>
                                                <li class="aminity-item">
                                                    <img src="{{ asset('frontend/assets/images/icons/bathroom.svg') }}">
                                                    <span>{{ $property['BathroomsTotalInteger'] }} Bathrooms</span>
                                                </li>
                                                <li class="aminity-item">
                                                    <img
                                                        src="{{ asset('frontend/assets/images/icons/measure-ruler.svg') }}">
                                                    <span>{{ $property['BuildingAreaTotal'] }}
                                                        {{ $property['BuildingAreaUnits'] }}</span>
                                                </li>
                                                <li class="aminity-item">
                                                    <img src="{{ asset('frontend/assets/images/icons/clock.svg') }}">
                                                    <span>{{ $property['DaysOnMarket'] }}
                                                        {{ $property['DaysOnMarket'] == 1 ? 'Day' : 'Days' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="property-footer">
                                        <a href="{{ $property['TransactionType'] == 'For Sale' ? route('buy.details', $property['ListingKey']) : route('lease.details', $property['ListingKey']) }}"
                                            class="btn-property-explore">
                                            Explore Property<span class="icon"><i
                                                    class="fa-solid fa-angles-up"></i></span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-properties">
                                <p>No properties found. Please try again later.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Pagination -->
                    @if ($totalCount > 10)
                        <div class="rt-pagination mt-4">
                            <ul class="m-0"></ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- About Section -->
        <section class="home-about-section sec-pad overflow-hidden">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-lg-6 col-md-6 col-sm-12 pb-5 pb-md-0">
                        <div class="about-me-block d-block w-100">
                            <h1 class="name">Ms. May lee</h1>
                            <h5 class="designation">Sales Representative At</h5>
                            <a href="https://www.rightathomerealty.com/" target="_blank" class="company-logo-link"><img
                                    src="{{ asset('frontend/assets/images/right-at-home-realty-logo.svg') }}"></a>
                            <div class="short-bio mt-2">
                                <p>Dedicated real estate professional committed to prioritizing client needs and best
                                    interests through every transaction. Trusted expertise in luxury, detached,
                                    semi-detached homes, and condos across Greater Toronto Area. Skilled negotiator with
                                    unwavering integrity. Fluent in English and Mandarin, serving local and international
                                    clients seamlessly.</p>
                            </div>
                        </div>
                        <div class="rt-btn-group mb-4">
                            <a href="#" class="rt-btn btn-small btn-pink">About Us <span class="icon"><i
                                        class="fa-solid fa-user"></i></span></a>
                            <a href="#" class="rt-btn btn-small">Contact Us <span class="icon"><i
                                        class="fa-solid fa-address-book"></i></span></a>
                        </div>
                        <div class="rt-link-widget mt-3">
                            <h3 class="title mb-0">Check my credentials at</h3>
                            <p class="note mb-0"><span>Note:</span> Use Fisrt Name <strong>May</strong> and Last Name
                                <strong>Lee</strong>
                            </p>
                            <a href="https://registrantsearch.reco.on.ca"
                                target="_blank">https://registrantsearch.reco.on.ca</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="rt-img-block position-relative w-100 h-100">
                            <img src="{{ asset('frontend/assets/images/girl-mockup.webp') }}">
                            <div class="rt-zooming-animation">
                                <div class="animation-box position-relative"></div>
                            </div>
                            <div class="rt-action-block">
                                <a href="#" class="btn-1 btn-animation">
                                    <span class="circle"></span>
                                    <span class="icon">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                </a>
                                <a href="#" class="btn-2 btn-animation">
                                    <span class="circle"></span>
                                    <span class="icon">
                                        <i class="fa-regular fa-envelope"></i>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- About Section End -->
        <!-- Testimonial -->
        <section class="rt-testimonial-sec sec-pad overflow-hidden bg-white">
            <div class="container">
                <div class="rt-section-title-wrap d-block w-100">
                    <h2 class="main-title">Hear What Our Happy Home Owners Say</h2>
                </div>
                <div class="swiper rt-testimonial-carousel rt-carousel top-right-nav">
                    <div class="swiper-wrapper p-2">
                        <div class="swiper-slide">
                            <div class="rt-testimonial-item">
                                <div class="rating">
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item half">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half"></i>
                                    </div>
                                    <div class="star-item blank">
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <div class="testimonial-content">
                                    As a first-time homebuyer, I was overwhelmed by the process. Maylee was a lifesaver.
                                    Their team was patient, answered every single one of my questions, and made sure I felt
                                    confident in my decisions. We found the perfect home, and they made the entire
                                    experience easy and exciting. I can't recommend them enough!
                                </div>
                                <div class="testimonial-user-details">
                                    <div class="image">
                                        <img src="{{ asset('frontend/assets/images/user-male.webp') }}">
                                    </div>
                                    <div class="content">
                                        <h3 class="name">Mark R</h3>
                                        <h6 class="designation">Buyer</h6>
                                        <p class="posted-date">Aug 28, 2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="rt-testimonial-item">
                                <div class="rating">
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item half">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half"></i>
                                    </div>
                                </div>
                                <div class="testimonial-content">
                                    Selling my property through Maylee was a breeze. They handled everything from the
                                    professional photography to the marketing strategy, and we had a great offer in just a
                                    few weeks. The communication was excellent, and they were always a step ahead, making
                                    the whole transaction incredibly smooth.
                                </div>
                                <div class="testimonial-user-details">
                                    <div class="image">
                                        <img src="{{ asset('frontend/assets/images/user-female.webp') }}">
                                    </div>
                                    <div class="content">
                                        <h3 class="name">Emily S</h3>
                                        <h6 class="designation">Seller</h6>
                                        <p class="posted-date">Aug 28, 2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="rt-testimonial-item">
                                <div class="rating">
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item half">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half"></i>
                                    </div>
                                </div>
                                <div class="testimonial-content">
                                    Finding a new apartment in a competitive market was a challenge, but Maylee's listings
                                    were exactly what I was looking for. The team was responsive, helpful, and made the
                                    application and move-in process seamless. I love my new place and am so grateful for
                                    their help.
                                </div>
                                <div class="testimonial-user-details">
                                    <div class="image">
                                        <img src="{{ asset('frontend/assets/images/user-male.webp') }}">
                                    </div>
                                    <div class="content">
                                        <h3 class="name">David T</h3>
                                        <h6 class="designation">Leasing</h6>
                                        <p class="posted-date">Aug 28, 2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="rt-testimonial-item">
                                <div class="rating">
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="star-item full">
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <div class="testimonial-content">
                                    The best real estate experience I've ever had. Whether you're looking to buy, sell, or
                                    lease, the level of professionalism and dedication from the Maylee team is unmatched.
                                    They truly put their clients first and get results.
                                </div>
                                <div class="testimonial-user-details">
                                    <div class="image">
                                        <img src="{{ asset('frontend/assets/images/user-female.webp') }}">
                                    </div>
                                    <div class="content">
                                        <h3 class="name">Sarah L</h3>
                                        <h6 class="designation">Customer</h6>
                                        <p class="posted-date">Aug 28, 2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Navigation & Pagination -->
                    <!--<div class="swiper-pagination"></div>-->
                    <div class="rt-carousel-nav swiper-button-prev">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <div class="rt-carousel-nav swiper-button-next">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </section>
        <!-- Testimonial End -->
        <!-- Conatct Section -->
        <section class="rt-home-contact-sec sec-pad position-relative overflow-hidden">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="rt-contact-info-box pb-5 pb-md-0">
                            <h2 class="title mb-5">Your Dream Home Awaits — Let’s Connect!</h2>
                            <div class="rt-icon-box-list rt-contact-list">
                                <div class="rt-icon-box">
                                    <div class="icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="content">
                                        <h3>Address</h3>
                                        <p>1550 16th Ave, Suite: 3 & 4, Richmond Hill, Ontario, Canada, L4B 3K9</p>
                                    </div>
                                </div>
                                <div class="rt-icon-box">
                                    <div class="icon">
                                        <i class="fa-solid fa-phone-volume"></i>
                                    </div>
                                    <div class="content">
                                        <h3>Phone</h3>
                                        <a href="tel:9056957888">905-695-7888</a>
                                    </div>
                                </div>
                                <div class="rt-icon-box">
                                    <div class="icon">
                                        <i class="fa-solid fa-envelope-open-text"></i>
                                    </div>
                                    <div class="content">
                                        <h3>Email</h3>
                                        <a href="#">info@mayleerealtor.com</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="rt-contact-form-box">
                            <form action="">
                                <div class="rt-input-wrap">
                                    <input type="text" name="" id="" placeholder="Name">
                                    <div class="error-msg"></div>
                                </div>
                                <div class="rt-input-wrap">
                                    <input type="email" name="" id="" placeholder="Email">
                                    <div class="error-msg"></div>
                                </div>
                                <div class="rt-input-wrap">
                                    <input type="text" name="" id="" placeholder="Phone">
                                    <div class="error-msg"></div>
                                </div>
                                <div class="rt-input-wrap">
                                    <textarea name="" id="" placeholder="Message"></textarea>
                                    <div class="error-msg"></div>
                                </div>
                                <div class="rt-btn-wrap">
                                    <button type="submit" class="rt-btn btn-pink btn-outline">
                                        Send
                                        <span class="icon"><i class="fa-solid fa-arrow-right"></i></span>
                                    </button>
                                </div>
                                <div class="rt-response-msg"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Conatct Section End -->
    </div>


    <!-- Advance Filter Options -->
    <div id="advance-filter-sidebar" class="rt-advance-filter-block">
        <div class="sidebar-header">
            <h2>Additional Filters</h2>
            <div class="filter-sidebar-close">
                <i class="fa-solid fa-x"></i>
            </div>
        </div>
        <div class="sidebar-body">
            <div class="filter-widget-wrap rt-scrollbar">
                <!-- Building Type Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Building Type</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="building-type-filter-wrap rt-checkbox-filter-group">
                            @php $uniqueSubTypes = array_unique(array_map(fn($t) => Str::trim($t), $propertySubTypes)); @endphp
                            @foreach ($uniqueSubTypes as $type)
                                @php $slug = Str::slug(Str::trim($type)); @endphp
                                <label for="building-{{ $slug }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="building_type[]"
                                        id="building-{{ $slug }}" value="{{ Str::trim($type) }}"
                                        {{ in_array(Str::trim($type), request('building_type', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ Str::trim($type) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Price Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Price Range</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-price-range-slider">
                            <div class="title-box mb-2">
                                <input type="text" name="price-range" id="price-range"
                                    class="form-control-plaintext pt-0 pb-2 text-dark" readonly
                                    value="${{ number_format(request('adv_min_price', 0)) }} - ${{ number_format(request('adv_max_price', $maxPrice)) }}">
                                <input type="hidden" name="adv_min_price" id="adv_min_price"
                                    value="{{ request('adv_min_price', 0) }}">
                                <input type="hidden" name="adv_max_price" id="adv_max_price"
                                    value="{{ request('adv_max_price', $maxPrice) }}">
                            </div>
                            <div id="price-slider-filter"></div>
                        </div>
                    </div>
                </div>
                <!-- Bedroom Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Bed Rooms</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-bedroom-filter-group">
                            <label for="bedrooms-any" class="rt-checkbox">
                                <input class="rt_checkbox_input" type="radio" name="adv_bedrooms" id="bedrooms-any"
                                    value="" {{ !request('adv_bedrooms') ? 'checked' : '' }}>
                                <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                    <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#4c418c"
                                        rx="3" />
                                    <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                        stroke-width="4" d="M4 10l5 5 9-9" />
                                </svg>
                                <span class="rt_checkbox_label">Any</span>
                            </label>
                            @foreach (['1', '2', '3', '4', '5', '6+'] as $bedroom)
                                <label for="bedrooms-{{ $bedroom }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="radio" name="adv_bedrooms"
                                        id="bedrooms-{{ $bedroom }}" value="{{ $bedroom }}"
                                        {{ request('adv_bedrooms') == $bedroom ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#4c418c" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $bedroom }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Bathroom Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Bath Rooms</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-bathroom-filter-group">
                            <label for="bathrooms-any" class="rt-checkbox">
                                <input class="rt_checkbox_input" type="radio" name="adv_bathrooms" id="bathrooms-any"
                                    value="" {{ !request('adv_bathrooms') ? 'checked' : '' }}>
                                <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                    <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#4c418c"
                                        rx="3" />
                                    <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                        stroke-width="4" d="M4 10l5 5 9-9" />
                                </svg>
                                <span class="rt_checkbox_label">Any</span>
                            </label>
                            @foreach (['1', '2', '3', '4', '5', '6+'] as $bathroom)
                                <label for="bathrooms-{{ $bathroom }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="radio" name="adv_bathrooms"
                                        id="bathrooms-{{ $bathroom }}" value="{{ $bathroom }}"
                                        {{ request('adv_bathrooms') == $bathroom ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#4c418c" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $bathroom }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Area Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Square Feet</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-sqft-filter-group">
                            @foreach (['1500-2000', '2000-2500', '2500-3000', '3000-3500', '3500-4000', '4000-4500', '4500-5000', 'over-5000'] as $range)
                                <label for="{{ $range }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="square_feet[]"
                                        id="{{ $range }}" value="{{ $range }}"
                                        {{ in_array($range, request('square_feet', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $range }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Days Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Days in the Market</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-post-days-filter-group">
                            @foreach (['any', '1day', '7days', '14days', '30days', '90days', '6months', '12months', '24months', '36months'] as $day)
                                <label for="{{ $day }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="days_on_market[]"
                                        id="{{ $day }}" value="{{ $day }}"
                                        {{ in_array($day, request('days_on_market', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span
                                        class="rt_checkbox_label">{{ $day == 'any' ? 'Any' : str_replace(['days', 'months'], [' Days', ' Months'], ucfirst($day)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Flooring Type Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Flooring Type</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-flooring-type-filter-group">
                            @foreach ($flooringOptions as $type)
                                <label for="{{ Str::slug($type) }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="flooring[]"
                                        id="{{ Str::slug($type) }}" value="{{ $type }}"
                                        {{ in_array($type, request('flooring', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $type }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Parking Spaces Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Parking Spaces</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-filter-group col-8">
                            @foreach ($parkingSpaceOptions as $spaces)
                                <label for="parking-{{ $spaces }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="parking_spaces[]"
                                        id="parking-{{ $spaces }}" value="{{ $spaces }}"
                                        {{ in_array($spaces, request('parking_spaces', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FAF9F6"
                                            stroke="#4c418c" rx="3"></rect>
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9"></path>
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $spaces }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Parking Type Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Parking Type</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-parking-type-filter-group rt-checkbox-filter-group">
                            @foreach ($parkingTypeOptions as $ptype)
                                <label for="{{ Str::slug($ptype) }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="parking_type[]"
                                        id="{{ Str::slug($ptype) }}" value="{{ $ptype }}"
                                        {{ in_array($ptype, request('parking_type', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $ptype }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Pool Type Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Pool Type</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-pool-type-filter-group rt-filter-group col-3">
                            @foreach ($poolTypeOptions as $ptype)
                                <label for="{{ Str::slug($ptype) }}-pool" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="pool_type[]"
                                        id="{{ Str::slug($ptype) }}-pool" value="{{ $ptype }}"
                                        {{ in_array($ptype, request('pool_type', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $ptype }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Amenities Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Amenities</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-amenities-filter-group rt-filter-group col-3">
                            @foreach ($amenityOptions as $amenity)
                                <label for="{{ Str::slug($amenity) }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="amenities[]"
                                        id="{{ Str::slug($amenity) }}" value="{{ $amenity }}"
                                        {{ in_array($amenity, request('amenities', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $amenity }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- View Type Filter -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>View Type</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-view-type-filter-group rt-filter-group col-3">
                            @foreach ($viewTypeOptions as $view)
                                <label for="{{ Str::slug($view) }}-view" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="view_type[]"
                                        id="{{ Str::slug($view) }}-view" value="{{ $view }}"
                                        {{ in_array($view, request('view_type', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                            stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round"
                                            stroke-width="4" d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ ucfirst($view) }} view</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar-footer">
            <div class="button-group">
                <a href="#" class="reset-btn">Reset</a>
                <a href="#" class="apply-btn">Apply</a>
            </div>
        </div>
    </div>
    <!-- Advance Filter Options End -->
@endsection

@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
    <script src="https://unpkg.com/supercluster@7.1.5/dist/supercluster.min.js"></script>

    <script>
        let map;
        let markersLayer;
        let allProperties = @json($properties);
        const maxPrice = {{ $maxPrice }};
        const cityCoordinates = @json($cityCoordinates ?? []);
        const fallbackLat = 43.6532;
        const fallbackLng = -79.3832;
        const propertiesPerPage = {{ $propertiesPerPage ?? 50 }};
        const isLeasePage = {{ request()->routeIs('lease') ? 'true' : 'false' }};
        let currentVisibleProperties = [];
        let clusterIndex;

        const totalProperties = {{ $filteredCount ?? 0 }};
        const hasMorePages = {{ $hasMorePages ? 'true' : 'false' }};

        // Pagination functions
        // let totalProperties = {{ $totalCount }};
        let totalPages = Math.ceil(totalProperties / propertiesPerPage);
        let currentPage = {{ $currentPage }};
        const expandedBounds = L.latLngBounds(
            [42.0, -83.0],
            [46.0, -74.0]
        );

        // Property listing filter
        $("#advance-filter-toggle").click(function(e) {
            e.preventDefault();
            $("#advance-filter-sidebar").toggleClass("active");
        });

        $('.filter-sidebar-close').click(function() {
            $("#advance-filter-sidebar").removeClass("active");
        });

        function initializeMap() {
            map = L.map('interactiveMap', {
                maxBounds: expandedBounds,
                maxBoundsViscosity: 1.0,
                minZoom: 6
            }).setView([fallbackLat, fallbackLng], 8);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                tileSize: 256,
                maxZoom: 20
            }).addTo(map);

            markersLayer = L.layerGroup().addTo(map);

            initializeSuperCluster();
            updateMapMarkers();

            const validProperties = allProperties.filter(p =>
                p.Latitude && p.Longitude && p.Latitude !== 0 && p.Longitude !== 0
            );

            if (validProperties.length > 0) {
                const group = L.featureGroup(
                    validProperties.map(p => L.marker([p.Latitude, p.Longitude]))
                );
                map.fitBounds(group.getBounds().pad(0.1), {
                    maxZoom: 12
                });
            } else {
                map.fitBounds(expandedBounds);
            }

            map.on('moveend', updateMapMarkers);
            map.on('zoomend', updateMapMarkers);
            map.on('zoomend', restrictToBounds);
        }

        function initializeSuperCluster() {
            const validProperties = allProperties.filter(property => {
                if (!property.Latitude || !property.Longitude || property.Latitude === 0 || property.Longitude ===
                    0) {
                    if (cityCoordinates[property.City]) {
                        property.Latitude = cityCoordinates[property.City].latitude;
                        property.Longitude = cityCoordinates[property.City].longitude;
                        return true;
                    }
                    return false;
                }
                return true;
            });

            clusterIndex = new Supercluster({
                radius: 60,
                maxZoom: 16,
                minPoints: 2
            });

            clusterIndex.load(validProperties.map(property => ({
                type: "Feature",
                geometry: {
                    type: "Point",
                    coordinates: [property.Longitude, property.Latitude]
                },
                properties: property
            })));
        }

        function restrictToBounds() {
            if (map.getZoom() < 6) {
                map.fitBounds(expandedBounds, {
                    maxZoom: 6
                });
            }
        }

        function createClusterMarker(cluster) {
            const count = cluster.properties.point_count || cluster.properties.length;
            const size = Math.min(80, 40 + (count * 2));

            return L.marker([cluster.geometry.coordinates[1], cluster.geometry.coordinates[0]], {
                icon: L.divIcon({
                    html: `
                <div class="custom-cluster-marker" style="
                    width: ${size}px;
                    height: ${size}px;
                    background: #4c418c;
                    border: 2px solid #ffffff;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    color: white;
                    font-family: Arial, sans-serif;
                    cursor: pointer;">
                    <div class="cluster-count" style="
                        font-size: ${Math.max(14, 12 + count / 10)}px;
                        font-weight: bold;">
                        ${count}
                    </div>
                </div>
                `,
                    className: 'custom-cluster',
                    iconSize: [size, size]
                })
            }).bindPopup(`
                <div class="cluster-popup" style="
                    background: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                    padding: 10px;
                    max-width: 300px;
                    font-family: Arial, sans-serif;">
                    <h4 style="color: #1976d2; font-size: 18px; margin: 0 0 5px;">
                        ${count} Properties
                    </h4>
                    <p style="color: #666; font-size: 14px; margin: 0 0 10px;">
                        Click to zoom and view properties
                    </p>
                    <button onclick="zoomToCluster(${cluster.geometry.coordinates[1]}, ${cluster.geometry.coordinates[0]})" style="
                        background: #1976d2;
                        color: white;
                        border: none;
                        padding: 8px 12px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        margin-top: 10px;">
                        Zoom to Location
                    </button>
                </div>
                `);
        }

        function createIndividualMarker(property) {
            const route = isLeasePage ?
                '/lease/' + property.ListingKey :
                '/property/' + property.ListingKey;

            const iconHtml = `
                <div style="
                    background: #1976d2;
                    color: white;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 10px;
                    font-weight: bold;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                    border: 2px solid white;">
                    $${(property.ListPrice / 1000).toFixed(0)}K
                </div>
                `;

            return L.marker([property.Latitude, property.Longitude], {
                icon: L.divIcon({
                    html: iconHtml,
                    className: 'custom-marker',
                    iconSize: [30, 30],
                    iconAnchor: [15, 30],
                    popupAnchor: [0, -30]
                })
            }).bindPopup(property.MapPopup || `
            <div style="padding: 10px;">
                <h4>${property.FormattedPrice || '$0'}</h4>
                <p>${property.FullAddress || 'Address not available'}</p>
                <a href="${route}" style="color: #1976d2;">View Details</a>
            </div>
            `);
        }

        function updateMapMarkers() {
            markersLayer.clearLayers();
            const bounds = map.getBounds();
            const zoom = map.getZoom();
            let skippedProperties = 0;

            const clusters = clusterIndex.getClusters([
                bounds.getWest(),
                bounds.getSouth(),
                bounds.getEast(),
                bounds.getNorth()
            ], zoom);

            clusters.forEach(cluster => {
                if (cluster.properties.cluster) {
                    markersLayer.addLayer(createClusterMarker(cluster));
                } else {
                    markersLayer.addLayer(createIndividualMarker(cluster.properties));
                }
            });

            skippedProperties = allProperties.filter(p =>
                (!p.Latitude || !p.Longitude || p.Latitude === 0 || p.Longitude === 0) &&
                !cityCoordinates[p.City]
            ).length;

            if (skippedProperties > 0) {
                $('#map-warning').show().text(
                    `${skippedProperties} properties not displayed due to missing coordinates.`
                );
            } else {
                $('#map-warning').hide();
            }

            updatePropertyListForCurrentView();
        }

        function updatePropertyListForCurrentView() {
            const bounds = map.getBounds();
            const currentZoom = map.getZoom();

            if (currentZoom < 12) {
                currentVisibleProperties = [...allProperties];
                $('#properties-count').text(allProperties.length);
                $('.property-list-header h4').text(`Found ${allProperties.length} Properties`);
            } else {
                currentVisibleProperties = allProperties.filter(property => {
                    if (!property.Latitude || !property.Longitude) return false;
                    return bounds.contains([property.Latitude, property.Longitude]);
                });

                $('#properties-count').text(currentVisibleProperties.length);
                $('.property-list-header h4').text(
                    `Found ${currentVisibleProperties.length} Properties in Current View`
                );
            }

            renderPropertyList(currentVisibleProperties);
        }

        function renderPropertyList(properties) {
            if (properties.length === 0) {
                $('#property-list-container').html(
                    '<div class="text-center p-4">No properties found in current view.</div>'
                );
                return;
            }

            let propertiesHtml = '';
            properties.forEach(property => {
                const route = property.TransactionType === 'For Sale' ?
                    '/property/' + property.ListingKey : '/lease/' + property.ListingKey;

                propertiesHtml += `
            <div class="rt-property-item"
                 data-lat="${property.Latitude || ''}"
                 data-lng="${property.Longitude || ''}"
                 data-price="${property.ListPrice || 0}"
                 data-bedrooms="${property.BedroomsTotal || 0}"
                 data-bathrooms="${property.BathroomsTotalInteger || 0}"
                 data-type="${property.PropertySubType || ''}"
                 data-city="${property.City || ''}"
                 data-listing-key="${property.ListingKey || ''}"
                 data-transaction-type="${property.TransactionType || ''}">
                <div class="rt-property-header">
                    <figure class="rt-propert-image">
                        <img src="${property.MediaURL || '{{ asset('frontend/assets/images/properties/property-1.jpg') }}'}"
                             alt="Property Image"
                             onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">
                    </figure>
                    <span class="property-wishlist">
                        <i class="fa-regular fa-heart"></i>
                    </span>
                </div>
                <div class="rt-property-body">
                    <h2 class="property-price">${property.FormattedPrice || '$0'}</h2>
                    <p class="property-location">${property.FullAddress || 'Address not available'}</p>
                    <div class="property-meta">
                        <ul class="aminity-list">
                            <li class="aminity-item">
                                <img src="{{ asset('frontend/assets/images/icons/bed.svg') }}">
                                <span>${property.BedroomsTotal || 0} Bedrooms</span>
                            </li>
                            <li class="aminity-item">
                                <img src="{{ asset('frontend/assets/images/icons/bathroom.svg') }}">
                                <span>${property.BathroomsTotalInteger || 0} Bathrooms</span>
                            </li>
                            <li class="aminity-item">
                                <img src="{{ asset('frontend/assets/images/icons/measure-ruler.svg') }}">
                                <span>${property.BuildingAreaTotal || 'N/A'} ${property.BuildingAreaUnits || 'Sq ft'}</span>
                            </li>
                            <li class="aminity-item">
                                <img src="{{ asset('frontend/assets/images/icons/clock.svg') }}">
                                <span>${property.DaysOnMarket || 0} ${property.DaysOnMarket == 1 ? 'Day' : 'Days'}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="property-footer">
                    <a href="${route}"
                       class="btn-property-explore">
                        Explore Property<span class="icon"><i class="fa-solid fa-angles-up"></i></span>
                    </a>
                </div>
            </div>
            `;
            });

            $('#property-list-container').html(propertiesHtml);
        }

        function zoomToCluster(lat, lng) {
            const targetZoom = Math.min(map.getZoom() + 2, 16);
            map.setView([lat, lng], targetZoom, {
                animate: true
            });
        }

        // Price Slider Initialization
        const priceSlider = document.getElementById('price-slider');
        if (priceSlider) {
            noUiSlider.create(priceSlider, {
                start: [{{ request('min_price', 0) }}, {{ request('max_price', $maxPrice) }}],
                connect: true,
                step: 100,
                range: {
                    'min': 0,
                    'max': maxPrice
                },
                format: {
                    to: value => '$' + Math.round(value).toLocaleString(),
                    from: value => Number(value.replace('$', '').replace(',', ''))
                }
            });

            priceSlider.noUiSlider.on('update', function(values) {
                $('#amount').val(values.join(' - '));
                $('#min_price').val(Number(values[0].replace('$', '').replace(',', '')));
                $('#max_price').val(Number(values[1].replace('$', '').replace(',', '')));
            });
        }

        // Sidebar Price Filter
        const sidePriceSlider = document.getElementById('price-slider-filter');
        if (sidePriceSlider) {
            noUiSlider.create(sidePriceSlider, {
                start: [{{ request('adv_min_price', 0) }}, {{ request('adv_max_price', $maxPrice) }}],
                connect: true,
                step: 100,
                range: {
                    'min': 0,
                    'max': maxPrice
                },
                format: {
                    to: value => '$' + Math.round(value).toLocaleString(),
                    from: value => Number(value.replace('$', '').replace(',', ''))
                }
            });

            sidePriceSlider.noUiSlider.on('update', function(values) {
                $('#price-range').val(values.join(' - '));
                // Update hidden fields for advanced price filter
                $('#adv_min_price').val(Number(values[0].replace('$', '').replace(',', '')));
                $('#adv_max_price').val(Number(values[1].replace('$', '').replace(',', '')));
            });
        }

        // Advanced Filter Apply Button - CORRECTED VERSION
        $('.apply-btn').click(function(e) {
            e.preventDefault();

            // Collect all advanced filter values
            const formData = new FormData();

            // Get all checked building types
            $('input[name="building_type[]"]:checked').each(function() {
                formData.append('building_type[]', $(this).val());
            });

            // Get advanced price range - FIXED
            const sidePriceSlider = document.getElementById('price-slider-filter');
            if (sidePriceSlider) {
                const sidePriceValues = sidePriceSlider.noUiSlider.get();
                formData.append('adv_min_price', Number(sidePriceValues[0].replace('$', '').replace(',', '')));
                formData.append('adv_max_price', Number(sidePriceValues[1].replace('$', '').replace(',', '')));
            }

            // Get advanced bedrooms
            const advBedrooms = $('input[name="adv_bedrooms"]:checked').val();
            if (advBedrooms) formData.append('adv_bedrooms', advBedrooms);

            // Get advanced bathrooms
            const advBathrooms = $('input[name="adv_bathrooms"]:checked').val();
            if (advBathrooms) formData.append('adv_bathrooms', advBathrooms);

            // Get square feet ranges
            $('input[name="square_feet[]"]:checked').each(function() {
                formData.append('square_feet[]', $(this).val());
            });

            // Get days on market
            $('input[name="days_on_market[]"]:checked').each(function() {
                formData.append('days_on_market[]', $(this).val());
            });

            // Get flooring types
            $('input[name="flooring[]"]:checked').each(function() {
                formData.append('flooring[]', $(this).val());
            });

            // Get parking spaces
            $('input[name="parking_spaces[]"]:checked').each(function() {
                formData.append('parking_spaces[]', $(this).val());
            });

            // Get parking types
            $('input[name="parking_type[]"]:checked').each(function() {
                formData.append('parking_type[]', $(this).val());
            });

            // Get pool types
            $('input[name="pool_type[]"]:checked').each(function() {
                formData.append('pool_type[]', $(this).val());
            });

            // Get amenities
            $('input[name="amenities[]"]:checked').each(function() {
                formData.append('amenities[]', $(this).val());
            });

            // Get view types
            $('input[name="view_type[]"]:checked').each(function() {
                formData.append('view_type[]', $(this).val());
            });

            // Also include main form filters but prioritize advanced filters
            const mainFormData = $('#property-filter-form').serializeArray();
            mainFormData.forEach(field => {
                // Don't override advanced filters with main filters
                if (!formData.has(field.name) && field.name !== 'min_price' && field.name !== 'max_price') {
                    formData.append(field.name, field.value);
                }
            });

            // Submit the combined filters
            submitFilters(formData);

            // Close the sidebar
            $("#advance-filter-sidebar").removeClass("active");
        });

        // Advanced Filter Reset Button - CORRECTED VERSION
        $('.reset-btn').click(function(e) {
            e.preventDefault();

            // Reset all advanced filters
            $('#advance-filter-sidebar input[type="checkbox"]').prop('checked', false);
            $('#advance-filter-sidebar input[type="radio"]').prop('checked', false);

            // Reset price sliders
            if (sidePriceSlider) {
                sidePriceSlider.noUiSlider.set([0, maxPrice]);
            }

            // Reset any and default radio buttons
            $('#bedrooms-any').prop('checked', true);
            $('#bathrooms-any').prop('checked', true);

            // Also reset the main form
            $('#property-filter-form')[0].reset();
            if (priceSlider) {
                priceSlider.noUiSlider.set([0, maxPrice]);
            }

            // Submit empty filters to reset everything
            submitFilters(new FormData());
        });

        // Filter Form Submission with AJAX
        function submitFilters(formData) {
            $('#loading-indicator').show();

            const currentUrl = '{{ url()->current() }}';

            $('#property-list-container').html('<div class="text-center p-4">Loading properties...</div>');

            // Convert FormData to URLSearchParams for AJAX - FIX PRICE HANDLING
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                // Skip NaN values for prices
                if ((key === 'min_price' || key === 'max_price' || key === 'adv_min_price' || key === 'adv_max_price') &&
                    (value === 'NaN' || !value || value === '0')) {
                    continue;
                }
                params.append(key, value);
            }

            $.ajax({
                url: currentUrl,
                type: 'GET',
                data: params.toString(),
                success: function(response) {
                    if (response.success) {
                        handleFilterResponse(response);
                    } else {
                        $('#property-list-container').html(
                            '<div class="text-center p-4 text-danger">Failed to load properties. Please try again.</div>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error('Filter Error:', xhr.responseText);
                    $('#property-list-container').html(
                        '<div class="text-center p-4 text-danger">Error loading properties. Please try again.</div>'
                    );
                },
                complete: function() {
                    $('#loading-indicator').hide();
                }
            });
        }

        $('#property-filter-form').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            submitFilters(formData);
        });


        function collectAdvancedFilters() {
            const filters = {};

            // Collect all advanced filter values
            $('input[name="building_type[]"]:checked').each(function() {
                if (!filters.building_type) filters.building_type = [];
                filters.building_type.push($(this).val());
            });

            if ($('#adv_min_price').val()) filters.adv_min_price = $('#adv_min_price').val();
            if ($('#adv_max_price').val()) filters.adv_max_price = $('#adv_max_price').val();
            if ($('input[name="adv_bedrooms"]:checked').val()) filters.adv_bedrooms = $(
                'input[name="adv_bedrooms"]:checked').val();
            if ($('input[name="adv_bathrooms"]:checked').val()) filters.adv_bathrooms = $(
                'input[name="adv_bathrooms"]:checked').val();

            $('input[name="square_feet[]"]:checked').each(function() {
                if (!filters.square_feet) filters.square_feet = [];
                filters.square_feet.push($(this).val());
            });

            $('input[name="days_on_market[]"]:checked').each(function() {
                if (!filters.days_on_market) filters.days_on_market = [];
                filters.days_on_market.push($(this).val());
            });

            $('input[name="flooring[]"]:checked').each(function() {
                if (!filters.flooring) filters.flooring = [];
                filters.flooring.push($(this).val());
            });

            $('input[name="parking_spaces[]"]:checked').each(function() {
                if (!filters.parking_spaces) filters.parking_spaces = [];
                filters.parking_spaces.push($(this).val());
            });

            $('input[name="parking_type[]"]:checked').each(function() {
                if (!filters.parking_type) filters.parking_type = [];
                filters.parking_type.push($(this).val());
            });

            $('input[name="pool_type[]"]:checked').each(function() {
                if (!filters.pool_type) filters.pool_type = [];
                filters.pool_type.push($(this).val());
            });

            $('input[name="amenities[]"]:checked').each(function() {
                if (!filters.amenities) filters.amenities = [];
                filters.amenities.push($(this).val());
            });

            $('input[name="view_type[]"]:checked').each(function() {
                if (!filters.view_type) filters.view_type = [];
                filters.view_type.push($(this).val());
            });

            return filters;
        }

        function handleFilterResponse(response) {
            if (response.success) {
                allProperties = response.properties || [];

                // Reinitialize clustering with new properties
                initializeSuperCluster();

                // Update UI
                currentVisibleProperties = [...allProperties];
                renderPropertyList(currentVisibleProperties);

                // Use filteredCount for display
                $('#properties-count').text(response.filteredCount || 0);
                $('.property-list-header h4').text(`Found ${response.filteredCount || 0} Properties`);

                // Update map with new data
                updateMapMarkers();

                // Update pagination using filteredCount
                if (response.filteredCount > propertiesPerPage) {
                    initializePagination(response.filteredCount, response.currentPage || 1);
                } else {
                    $('.rt-pagination').hide();
                }
            }
        }

        function showPage(pageNum) {
            currentPage = pageNum;
            const formData = new FormData();

            // Collect all current filter values
            const mainFormData = $('#property-filter-form').serializeArray();
            mainFormData.forEach(field => {
                formData.append(field.name, field.value);
            });

            // Add advanced filters
            const advFormData = collectAdvancedFilters();
            for (const [key, value] of Object.entries(advFormData)) {
                if (Array.isArray(value)) {
                    value.forEach(val => formData.append(key, val));
                } else {
                    formData.append(key, value);
                }
            }

            formData.append('page', pageNum);

            $('#loading-indicator').show();
            $('#property-list-container').html('<div class="text-center p-4">Loading properties...</div>');

            $.ajax({
                url: '{{ url()->current() }}',
                type: 'GET',
                data: new URLSearchParams(formData).toString(),
                success: function(response) {
                    if (response.success) {
                        handleFilterResponse(response);

                        // Update URL without page reload
                        const url = new URL(window.location.href);
                        url.searchParams.set('page', pageNum);
                        window.history.pushState({}, '', url.toString());
                    }
                },
                error: function(xhr) {
                    console.error('Pagination Error:', xhr.responseText);
                    $('#property-list-container').html(
                        '<div class="text-center p-4 text-danger">Error loading properties.</div>'
                    );
                },
                complete: function() {
                    $('#loading-indicator').hide();
                }
            });
        }

        function createPagination(totalCount, currentPage) {
            const totalPages = Math.ceil(totalCount / propertiesPerPage);

            if (totalPages <= 1) {
                $('.rt-pagination').hide();
                return;
            }

            $('.rt-pagination').show();
            let liTag = '';
            let active;

            // Previous button
            if (currentPage > 1) {
                liTag += `<li class="btn prev" data-page="${currentPage - 1}"><span>&laquo; Prev</span></li>`;
            }

            // Page numbers - FIXED LOGIC
            let pageArr = [];

            // Always show first page
            pageArr.push(1);

            // Show pages around current page
            let startPage = Math.max(2, currentPage - 2);
            let endPage = Math.min(totalPages - 1, currentPage + 2);

            // Add ellipsis after first page if needed
            if (startPage > 2) {
                pageArr.push('...');
            }

            // Add middle pages
            for (let i = startPage; i <= endPage; i++) {
                pageArr.push(i);
            }

            // Add ellipsis before last page if needed
            if (endPage < totalPages - 1) {
                pageArr.push('...');
            }

            // Always show last page if there's more than 1 page
            if (totalPages > 1) {
                pageArr.push(totalPages);
            }

            // Remove duplicates and sort
            pageArr = [...new Set(pageArr)].sort((a, b) => a - b);

            // Build pagination HTML
            pageArr.forEach(i => {
                if (i === '...') {
                    liTag += `<li class="dots"><span>...</span></li>`;
                } else {
                    active = (currentPage == i) ? "active" : "";
                    liTag += `<li class="numb ${active}" data-page="${i}"><span>${i}</span></li>`;
                }
            });

            // Next button
            if (currentPage < totalPages) {
                liTag += `<li class="btn next" data-page="${currentPage + 1}"><span>Next &raquo;</span></li>`;
            }

            $('.rt-pagination ul').html(liTag);
        }

        function initializePagination(totalCount, currentPage = 1) {
            const totalPages = Math.ceil(totalCount / propertiesPerPage);

            if (totalPages > 1) {
                createPagination(totalCount, currentPage);
            } else {
                $('.rt-pagination').hide();
            }
        }

        // Initialize everything when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeMap();

            // Use filteredCount for initial pagination
            if (totalProperties > propertiesPerPage) {
                initializePagination(totalProperties, currentPage);
            } else {
                $('.rt-pagination').hide();
            }

            // Event listeners for pagination - FIXED SELECTORS
            $(document).on('click', '.rt-pagination li.numb', function(e) {
                e.preventDefault();
                const pageNum = $(this).data('page');
                if (pageNum) showPage(pageNum);
            });

            $(document).on('click', '.rt-pagination li.prev, .rt-pagination li.next', function(e) {
                e.preventDefault();
                const pageNum = $(this).data('page');
                if (pageNum) showPage(pageNum);
            });
        });
    </script>

    @if (session('showLoginPopup'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Auto-triggering login popup for guest');
                const loginButton = document.getElementById('open-signin-signup-popup');
                if (loginButton) {
                    setTimeout(() => {
                        loginButton.click();
                        console.log('Login button clicked automatically');
                    }, 300); // short delay to allow modal JS to initialize
                } else {
                    console.error('Login button not found - check DOM placement');
                }
            });
        </script>
    @endif
@endsection
@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" />
    <style>
        .rt-property-listing-withmap .property-list-wrap .rt-property-list {
            padding-bottom: 125px;
        }

        .custom-cluster-marker {
            border-radius: 50%;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-family: Arial, sans-serif;
            transition: all 0.3s ease;
        }

        .custom-cluster-marker:hover {
            transform: scale(1.1);
        }

        .cluster-count {
            font-size: 14px;
            font-weight: bold;
        }

        .property-popup,
        .cluster-popup {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 10px;
            max-width: 300px;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }

        .leaflet-popup-tip {
            background: #ffffff;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
        }

        .leaflet-popup-content-wrapper,
        .leaflet-popup-tip {
            background: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection
