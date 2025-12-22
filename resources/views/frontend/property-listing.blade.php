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
                                    <label for="city-search">City Search</label>
                                    <input type="text" id="city-search" name="selected_city"
                                        value="{{ request('selected_city') ?: request('search') }}"
                                        class="input-search px-3" placeholder="Search city (e.g., Ajax, Ontario, Canada)"
                                        autocomplete="off">
                                    <div id="city-suggestions" class="city-suggestions-list" style="display: none;">
                                        <!-- City suggestions will be populated here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="property-type">Property Type</label>
                                    <select id="property-type" name="property_type">
                                        <option value="">Choose Property Type</option>
                                        @foreach ($propertySubTypes as $type)
                                            <option value="{{ $type }}" {{ request('property_type') == $type ? 'selected' : '' }}>
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
                                        <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1
                                        </option>
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
                    @php
                        $hasActiveFilters = request()->hasAny(['adv_min_price', 'adv_max_price', 'adv_bedrooms', 'adv_bathrooms', 'InteriorFeatures', 'ParkingTotal', 'parking_type', 'pool_type', 'amenities', 'view_type', 'building_type', 'square_feet', 'days_on_market', 'min_price', 'max_price', 'bedrooms', 'bathrooms', 'property_type', 'selected_city']);
                    @endphp
                    <!-- @if (count($properties) > 0)
                                            <div class="property-list-header" style="padding: 15px 20px; background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                                            <h4 style="margin: 0; color: #495057; font-weight: 600;" id="property-header-text">{{ $hasActiveFilters ? 'Showing ' . $filteredCount . ' properties' : 'Found ' . $filteredCount . ' Properties' }}</h4>
                                            <div id="properties-count" style="display: none;"></div>
                                            </div>
                                        @endif -->
                    <div id="loading-indicator"
                        style="display: none; text-align: center; padding: 40px; height: 100%; justify-content: center; text-align: center; align-items: center;">
                        <div class="loading-spinner" style="
                                                display: inline-block;
                                                width: 50px;
                                                height: 50px;
                                                border: 4px solid #f3f3f3;
                                                border-top: 4px solid #1976d2;
                                                border-radius: 50%;
                                                animation: spin 1s linear infinite;
                                                margin-bottom: 15px;">
                        </div>
                        <div style="color: #666; font-size: 16px; font-weight: 500;">Loading properties...</div>
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
                                    data-transaction-type="{{ $property['TransactionType'] }}"
                                    data-route="{{ $property['TransactionType'] == 'For Sale' ? route('buy.details', $property['ListingKey']) : route('lease.details', $property['ListingKey']) }}">
                                    <div class="rt-property-header">
                                        <figure class="rt-propert-image"
                                            style="height: 250px; text-align: center; align-items: center;">
                                            @if (!empty($property['MediaURL']) && filter_var($property['MediaURL'], FILTER_VALIDATE_URL))
                                                <img src="{{ $property['MediaURL'] }}" alt="Property Image"
                                                    onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}">
                                            @else
                                                <div class="media-placeholder"
                                                    style="background-image: url('{{ asset('frontend/assets/images/properties/property-1.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
                                                    <div
                                                        style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                                        <i class="fa-solid fa-camera"
                                                            style="font-size: 48px; color: white; margin-bottom: 10px;"></i>
                                                        <span
                                                            style="color: white; font-size: 16px; font-weight: 500;">{{ $property['MediaPlaceholder'] ?? 'Media Coming Soon' }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </figure>
                                        <span class="property-wishlist" data-listing-key="{{ $property['ListingKey'] ?? '' }}"
                                            data-transaction-type="{{ $property['TransactionType'] ?? 'For Sale' }}">
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
                                                    <img src="{{ asset('frontend/assets/images/icons/measure-ruler.svg') }}">
                                                    <span>{{ $property['LivingAreaRange'] }}
                                                        sq ft (Living Area)</span>
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
                                            Explore Property<span class="icon"><i class="fa-solid fa-angles-up"></i></span>
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
    <section class="home-about-section sec-pad overflow-hidden bg-white">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-lg-6 col-md-6 col-sm-12 pb-5 pb-md-0">
                    <div class="about-me-block d-block w-100">
                       <div class="about-head d-flex">
                            <div class="name col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                <h1 class="name">Ms. May lee</h1>
                                <h5 class="designation">Sales Representative At</h5>
                            </div>
                            <div class="log col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                <a href="https://www.rightathomerealty.com/" target="_blank" class="company-logo-link"><img
                                src="{{ asset('frontend/assets/images/right-at-home-realty-logo.svg') }}"></a>
                            </div>
                       </div>
                        <div class="short-bio mt-2">
                            <p>Dedicated real estate professional committed to prioritizing client needs and best interests
                                through every transaction.</p>
                            <p>Trusted expertise in luxury, detached, semi-detached homes, and condos across Greater Toronto
                                Area.</p>
                            <p>Skilled negotiator with unwavering integrity.</p>
                            <p>Fluent in English and Mandarin, serving local and international clients seamlessly.</p>
                        </div>
                    </div>
                    <div class="rt-btn-group mb-4 mt-4">
                        <a href="{{ route('frontend.about-us') }}" class="rt-btn btn-small btn-pink">About Me <span class="icon"><i
                                    class="fa-solid fa-user"></i></span></a>
                        <a href="https://registrantsearch.reco.on.ca" target="_blank"
                            class="rt-btn btn-small btn-pink cred">View Credentials <span class="icon"><i
                                    class="fa-solid fa-user"></i></span></a>
                    </div>
                    
                            <p class="note mb-2"><span>Note:</span> Type in First Name <strong>May</strong> and Last Name
                            <strong>Lee</strong>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="rt-img-block position-relative w-100 h-100">
                        <img src="{{ asset('assets/maylee-1.png') }}">
                        <div class="rt-zooming-animation">
                            <div class="animation-box position-relative"></div>
                        </div>
                        <div class="rt-action-block">
                            <a href="mailto:company.sunseaz@gmail.com" class="btn-1 btn-animation">
                                <span class="circle"></span>
                              <span class="icon">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                            </a>
                            <a href="tel:9493851281" class="btn-2 btn-animation">
                                <span class="circle"></span>
                                  <span class="icon">
                                    <i class="fa-solid fa-phone"></i>
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
            <x-testimonials />
            </div>
        </section>
        <!-- Testimonial End -->
        <!-- Conatct Section -->
        <section class="rt-home-contact-sec sec-pad position-relative overflow-hidden">
            <div class="container">
                @if (request()->routeIs('buy'))
                    <x-contact source="Buy - Property Listing" :city="request('selected_city') ?: request('search')" />
                @elseif (request()->routeIs('lease'))
                    <x-contact source="Lease - Property Listing" :city="request('selected_city') ?: request('search')" />
                @else
                    <x-contact source="Property Listing" :city="request('selected_city') ?: request('search')" />
                @endif
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
                            @foreach ($propertySubTypes as $type)
                                @php $slug = Str::slug($type); @endphp
                                <label for="building-{{ $slug }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="building_type[]"
                                        id="building-{{ $slug }}" value="{{ $type }}" {{ in_array($type, request('building_type', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#790dc9" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $type }}</span>
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
                                <input class="rt_checkbox_input" type="radio" name="adv_bedrooms" id="bedrooms-any" value=""
                                    {{ !request('adv_bedrooms') ? 'checked' : '' }}>
                                <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                    <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#4c418c" rx="3" />
                                    <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                        d="M4 10l5 5 9-9" />
                                </svg>
                                <span class="rt_checkbox_label">Any</span>
                            </label>
                            @foreach (['1', '2', '3', '4', '5', '6+'] as $bedroom)
                                <label for="bedrooms-{{ $bedroom }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="radio" name="adv_bedrooms"
                                        id="bedrooms-{{ $bedroom }}" value="{{ $bedroom }}" {{ request('adv_bedrooms') == $bedroom ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#4c418c" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
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
                                    <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#4c418c" rx="3" />
                                    <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                        d="M4 10l5 5 9-9" />
                                </svg>
                                <span class="rt_checkbox_label">Any</span>
                            </label>
                            @foreach (['1', '2', '3', '4', '5', '6+'] as $bathroom)
                                <label for="bathrooms-{{ $bathroom }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="radio" name="adv_bathrooms"
                                        id="bathrooms-{{ $bathroom }}" value="{{ $bathroom }}" {{ request('adv_bathrooms') == $bathroom ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#4c418c" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
                                    </svg>
                                    <span class="rt_checkbox_label">{{ $bathroom }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Area Filter (UPDATED: Added fallbacks like price slider) -->
                <div class="rt-filter-widget">
                    <div class="widget-header">
                        <h3>Square Feet</h3>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                    <div class="widget-body">
                        <div class="rt-area-range-slider">
                            <div class="title-box mb-2">
                                <input type="text" name="area-range" id="area-range"
                                    class="form-control-plaintext pt-0 pb-2 text-dark" readonly
                                    value="{{ number_format(request('adv_min_area', 500)) }} - {{ number_format(request('adv_max_area', $maxArea ?? 10000)) }} sq ft">
                                <input type="hidden" name="adv_min_area" id="adv_min_area"
                                    value="{{ request('adv_min_area', 500) }}">
                                <input type="hidden" name="adv_max_area" id="adv_max_area"
                                    value="{{ request('adv_max_area', $maxArea ?? 10000) }}">
                            </div>
                            <div id="area-slider-filter"></div>
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
                                    <input class="rt_checkbox_input" type="checkbox" name="days_on_market[]" id="{{ $day }}"
                                        value="{{ $day }}" {{ in_array($day, request('days_on_market', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
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
                                    <input class="rt_checkbox_input" type="checkbox" name="InteriorFeatures[]"
                                        id="{{ Str::slug($type) }}" value="{{ $type }}" {{ in_array($type, request('InteriorFeatures', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
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
                            @foreach ($parkingTotalOptions as $spaces)
                                <label for="parking-{{ $spaces }}" class="rt-checkbox">
                                    <input class="rt_checkbox_input" type="checkbox" name="ParkingTotal[]"
                                        id="parking-{{ $spaces }}" value="{{ $spaces }}" {{ in_array($spaces, request('ParkingTotal', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FAF9F6" stroke="#4c418c" rx="3">
                                        </rect>
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9"></path>
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
                                        id="{{ Str::slug($ptype) }}" value="{{ $ptype }}" {{ in_array($ptype, request('parking_type', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
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
                                        id="{{ Str::slug($ptype) }}-pool" value="{{ $ptype }}" {{ in_array($ptype, request('pool_type', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
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
                                        id="{{ Str::slug($amenity) }}" value="{{ $amenity }}" {{ in_array($amenity, request('amenities', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
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
                                        id="{{ Str::slug($view) }}-view" value="{{ $view }}" {{ in_array($view, request('view_type', [])) ? 'checked' : '' }}>
                                    <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">
                                        <rect width="21" height="21" x=".5" y=".5" fill="#FFF" stroke="#006F94" rx="3" />
                                        <path class="tick" stroke="#4c418c" fill="none" stroke-linecap="round" stroke-width="4"
                                            d="M4 10l5 5 9-9" />
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">

    <script>
        let map;
        let markersLayer;
        let allProperties = @json($properties);
        let allPropertiesForClustering = @json($allPropertiesForClustering ?? []);
        const maxPrice = {{ $maxPrice }};
        const maxArea = parseInt('{{ $maxArea ?? 10000 }}') || 10000;
        console.log('maxArea initialized:', maxArea);
        const cityCoordinates = @json($cityCoordinates ?? []);
        const fallbackLat = 43.6532;
        const fallbackLng = -79.3832;
        const propertiesPerPage = {{ $propertiesPerPage ?? 50 }};
        const isLeasePage = {{ request()->routeIs('lease') ? 'true' : 'false' }};
        let currentVisibleProperties = [];
        let clusterIndex;

        const totalProperties = {{ $totalCount ?? 0 }};
        const hasMorePages = {{ $hasMorePages ? 'true' : 'false' }};

        // Pagination functions
        let totalPages = Math.ceil(totalProperties / propertiesPerPage);
        let currentPage = {{ $currentPage }};
        const expandedBounds = L.latLngBounds(
            [42.5, -141.0],
            [46.0, -52.0]
        );

        // Property listing filter
        $("#advance-filter-toggle").click(function (e) {
            e.preventDefault();
            $("#advance-filter-sidebar").toggleClass("active");

            // Sync filters when opening advanced filters
            if ($("#advance-filter-sidebar").hasClass("active")) {
                setTimeout(function () {
                    syncPropertyTypeToAdvanced();
                    syncBedroomsToAdvanced();
                    syncBathroomsToAdvanced();
                    syncPriceToAdvanced();
                }, 50);
            }
        });

        $('.filter-sidebar-close').click(function () {
            $("#advance-filter-sidebar").removeClass("active");
        });

        // Widget collapse functionality
        $('.rt-filter-widget .widget-header').click(function () {
            $(this).next('.widget-body').slideToggle(300);
            $(this).find('i.fa-angle-down').toggleClass('rotated');
        });

        function initializeMap() {
            console.time('initializeMap');
            console.time('mapInit');
            map = L.map('interactiveMap', {
                maxBounds: expandedBounds,
                maxBoundsViscosity: 1.0,
                minZoom: 9
            }).setView([fallbackLat, fallbackLng], 8);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                tileSize: 256,
                maxZoom: 20
            }).addTo(map);
            console.timeEnd('mapInit');

            console.time('markerLayer');
            markersLayer = L.layerGroup().addTo(map);
            console.timeEnd('markerLayer');

            console.time('initializeSuperCluster');
            initializeSuperCluster();
            console.timeEnd('initializeSuperCluster');

            console.time('updateMapMarkers');
            updateMapMarkers();
            console.timeEnd('updateMapMarkers');

            console.time('fitBounds');
            const validProperties = allProperties.filter(p =>
                p.Latitude && p.Longitude && p.Latitude !== 0 && p.Longitude !== 0
            );

            if (validProperties.length > 0) {
                const group = L.featureGroup(
                    validProperties.map(p => L.marker([p.Latitude, p.Longitude]))
                );
                map.fitBounds(group.getBounds(), {
                    paddingBottomRight: [0, 150], // Add padding to bottom to push markers up
                    maxZoom: 15
                });
            } else {
                map.fitBounds(expandedBounds);
            }
            console.timeEnd('fitBounds');

            map.on('moveend', updateMapMarkers);
            map.on('zoomend', updateMapMarkers);
            map.on('zoomend', restrictToBounds);

            console.timeEnd('initializeMap');
        }

        function createCityMarkers() {
            Object.keys(cityCounts).forEach(city => {
                const count = cityCounts[city] || 0;
                if (count > 0 && cityCoordinates[city]) {
                    const coords = cityCoordinates[city];
                    const cityMarker = createCityMarker(city, count, coords.latitude, coords.longitude);
                    markersLayer.addLayer(cityMarker);
                }
            });
        }

        function createCityMarker(city, count, lat, lng) {
            const size = Math.min(80, 40 + (count * 2));

            return L.marker([lat, lng], {
                icon: L.divIcon({
                    html: `
                                                                                                            <div class="city-cluster-marker" style="
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
                                                                                                                <div class="city-cluster-count" style="
                                                                                                                    font-size: ${Math.max(14, 12 + count / 10)}px;
                                                                                                                    font-weight: bold;">
                                                                                                                    ${count}
                                                                                                                </div>
                                                                                                        </div>
                                                                                                        `,
                    className: 'city-cluster',
                    iconSize: [size, size]
                })
            }).bindPopup(`
                                                                                                        <div class="city-popup" style="
                                                                                                            background: #ffffff;
                                                                                                            border-radius: 8px;
                                                                                                            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                                                                                                            padding: 10px;
                                                                                                            max-width: 300px;
                                                                                                            font-family: Arial, sans-serif;">
                                                                                                            <h4 style="color: #1976d2; font-size: 18px; margin: 0 0 5px;">
                                                                                                                ${city}
                                                                                                            </h4>
                                                                                                            <p style="color: #666; font-size: 14px; margin: 0 0 10px;">
                                                                                                                ${count} Properties Available
                                                                                                            </p>
                                                                                                            <button onclick="zoomToCity('${city}', ${lat}, ${lng})" style="
                                                                                                                background: #1976d2;
                                                                                                                color: white;
                                                                                                                border: none;
                                                                                                                padding: 8px 12px;
                                                                                                                border-radius: 4px;
                                                                                                                cursor: pointer;
                                                                                                                font-size: 14px;
                                                                                                                margin-top: 10px;">
                                                                                                                View Properties
                                                                                                            </button>
                                                                                                        </div>
                                                                                                        `);
        }

        function initializeSuperCluster() {
            // Use allPropertiesForClustering instead of paginated allProperties for clustering
            const propertiesForClustering = allPropertiesForClustering.length > 0 ? allPropertiesForClustering :
                allProperties;

            const validProperties = propertiesForClustering.filter(property => {
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

            console.log("Initialized Supercluster with " + validProperties.length + " properties for clustering");
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
                                                                                                            background: var(--rt-button-color);
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
                                                                                                                font-size: 14px;
                                                                                                                font-weight: bold;">
                                                                                                                ${count}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        `,
                    className: 'custom-cluster',
                    iconSize: [size, size]
                })
            }).on('click', function (e) {
                L.DomEvent.stopPropagation(e);
                // If cluster has only 1 property, bind and open the property popup directly
                if (count === 1) {
                    // Find the single property in the cluster
                    const leaves = clusterIndex.getLeaves(cluster.properties.cluster_id, Infinity);
                    if (leaves.length > 0) {
                        const property = leaves[0].properties;
                        const route = isLeasePage ?
                            '/lease/' + property.ListingKey :
                            '/property/' + property.ListingKey;
                        const popup = createPropertyPopup(property, route);
                         setTimeout(() => {
                            if (!map) return;
                            L.popup({
                                closeButton: true,
                                maxWidth: 300
                            }).setLatLng([property.Latitude, property.Longitude]).setContent(popup)
                                .openOn(map);
                         }, 10);
                    }
                } else {
                    // For clusters with more than 1 property, zoom in
                    const targetZoom = Math.min(map.getZoom() + 2, 16);
                     setTimeout(() => {
                        if (!map) return;
                        map.setView([cluster.geometry.coordinates[1], cluster.geometry.coordinates[0]], targetZoom, {
                            animate: true
                        });
                    }, 10);
                }
            });
        }

        function createIndividualMarker(property) {
            const route = property.TransactionType === 'For Lease' || property.TransactionType === 'For Sub-Lease' ?
                '/lease/' + property.ListingKey :
                '/property/' + property.ListingKey;

            const size = 50;
            
            const iconHtml = `
                            <div style="
                                background: var(--rt-accent-color);
                                color: white;
                                border-radius: 50%;
                                width: ${size}px;
                                height: ${size}px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 10px;
                                font-weight: bold;
                                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                                border: 2px solid white;">
                                ${(property.ListPrice / 1000).toFixed(0)}K
                            </div>
                            `;

            const marker = L.marker([property.Latitude, property.Longitude], {
                icon: L.divIcon({
                    html: iconHtml,
                    className: 'custom-marker',
                    iconSize: [size, size],
                    iconAnchor: [size/2, size],
                    popupAnchor: [0, -size]
                })
            });

            // Bind the custom popup to the marker
            const popup = createPropertyPopup(property, route);
            marker.bindPopup(popup, {
                closeButton: true,
                maxWidth: 300
            });
            return marker;
        }

        // Function to create the custom property popup HTML matching the design
        function createPropertyPopup(property, route) {
            const safeAddress = (property.FullAddress || property.UnparsedAddress || 'Address not available').replace(/</g,
                '&lt;').replace(/>/g, '&gt;');
            const safePrice = property.FormattedPrice || '$' + (property.ListPrice || 0).toLocaleString();
            const safeBeds = property.BedroomsTotal || 0;
            const safeBaths = property.BathroomsTotalInteger || 0;
            const safeSqft = property.LivingAreaRange || 'N/A';
            const safeDays = property.DaysOnMarket || 0;
            const safeImage = property.MediaURL || (property.MediaURLs && property.MediaURLs.length > 0 ? property
                .MediaURLs[0] : '{{ asset('frontend/assets/images/properties/property-1.jpg') }}');

            let imageHtml = '';
            if (safeImage && safeImage !== 'null' && safeImage !== '') {
                imageHtml =
                    `<img src="${safeImage}" alt="Property Image" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px 8px 0 0;" onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">`;
            } else {
                imageHtml = `
                        <div style="width: 100%; height: 150px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 8px 8px 0 0; color: #999;">
                            <i class="fa-solid fa-camera" style="font-size: 48px;"></i>
                        </div>
                    `;
            }

            return `
                    <div style="background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); max-width: 280px; font-family: Arial, sans-serif; overflow: hidden;">
                        ${imageHtml}
                        <div style="padding: 12px;">
                            <h3 style="margin: 0 0 8px; font-size: 20px; color: #1976d2; font-weight: bold;">${safePrice}</h3>
                            <p style="margin: 0 0 12px; font-size: 14px; color: #333; line-height: 1.4;">${safeAddress}</p>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 12px; color: #666;">
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <img src="{{ asset('frontend/assets/images/icons/bed.svg') }}" style="width: 16px; height: 16px;">
                                    <span>${safeBeds} Beds</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <img src="{{ asset('frontend/assets/images/icons/bathroom.svg') }}" style="width: 16px; height: 16px;">
                                    <span>${safeBaths} Baths</span>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 12px; color: #666;">
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <img src="{{ asset('frontend/assets/images/icons/measure-ruler.svg') }}" style="width: 16px; height: 16px;">
                                    <span>${safeSqft} Sq ft</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <img src="{{ asset('frontend/assets/images/icons/clock.svg') }}" style="width: 16px; height: 16px;">
                                    <span>${safeDays} Days</span>
                                </div>
                            </div>
                            <a href="${route}" style="color: white; text-decoration: none; padding: 10px 16px; border-radius: 20px; font-size: 14px; font-weight: bold; display: block; text-align: center; transition: opacity 0.3s; opacity: 1; display: flex; align-items: center; justify-content: center; color: var(--rt-primary-color); line-height: 14px; background: transparent; border-radius: 50px; border: 2px solid var(--rt-button-color);">
                                Explore Property <i class="fa-solid fa-arrow-up" style="margin-left: 5px;"></i>
                            </a>
                        </div>
                    </div>
                `;
        }

        function updateMapMarkers() {
            console.time('updateMapMarkers');
            console.log('updateMapMarkers called with allPropertiesForClustering count:', allPropertiesForClustering.length);
            console.time('clearMarkers');
            markersLayer.clearLayers();
            console.timeEnd('clearMarkers');

            console.time('getClusters');
            const bounds = map.getBounds();
            const zoom = map.getZoom();

            const clusters = clusterIndex.getClusters([
                bounds.getWest(),
                bounds.getSouth(),
                bounds.getEast(),
                bounds.getNorth()
            ], zoom);
            console.timeEnd('getClusters');

            console.time('createMarkers');
            clusters.forEach(cluster => {
                if (cluster.properties.cluster) {
                    markersLayer.addLayer(createClusterMarker(cluster));
                } else {
                    markersLayer.addLayer(createIndividualMarker(cluster.properties));
                }
            });
            console.timeEnd('createMarkers');

            console.time('checkSkipped');
            const skippedProperties = allProperties.filter(p =>
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
            console.timeEnd('checkSkipped');

            console.time('updatePropertyList');
            updatePropertyListForCurrentView();
            console.timeEnd('updatePropertyList');

            console.timeEnd('updateMapMarkers');
        }

        function updatePropertyListForCurrentView() {
            const bounds = map.getBounds();
            const currentZoom = map.getZoom();

            if (currentZoom < 12) {
                currentVisibleProperties = [...allProperties];
                $('#properties-count').text(allProperties.length);
                $('.property-list-header h4').text(`Found ${allProperties.length} Properties`);
            } else {
                // Use allPropertiesForClustering for map-based filtering to show all properties
                currentVisibleProperties = allPropertiesForClustering.filter(property => {
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

        // In the JS script - UPDATE: Fix ParkingTotal in renderPropertyList template literal (PHP echo error)
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

                // Handle image display properly
                let imageHtml = '';
                if (property.MediaURL && property.MediaURL !== 'null' && property.MediaURL !== '') {
                    imageHtml =
                        `<img src="${property.MediaURL}" alt="Property Image" onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">`;
                } else if (property.MediaURLs && property.MediaURLs.length > 0 && property.MediaURLs[0] !==
                    'null' && property.MediaURLs[0] !== '') {
                    imageHtml =
                        `<img src="${property.MediaURLs[0]}" alt="Property Image" onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">`;
                } else {
                    imageHtml = `
                            <div class="media-placeholder"style="background-image: url('{{ asset('frontend/assets/images/properties/property-1.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-camera" style="font-size: 48px; color: white; margin-bottom: 10px;"></i>
                                    <span style="color: white; font-size: 16px; font-weight: 500;">Media Coming Soon</span>
                                </div>
                            </div>
                        `;
                }

                const parkingTotal = property.ParkingTotal || 0;
                const parkingText = parkingTotal + ' Parking Total' + (parkingTotal > 1 ? 's' : '');

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
                                                                                                    <figure class="rt-propert-image" style="height: 250px; text-align: center; align-items: center;">
                                                                                                        ${imageHtml}
                                                                                                    </figure>
                                                                                                    <span class="property-wishlist" 
                                                                                                                  data-listing-key="${property.ListingKey || ''}" 
                                                                                                                  data-transaction-type="${property.TransactionType || ''}">
                                                                                                        <i class="fa-regular fa-heart"></i>
                                                                                                    </span>
                                                                                                </div>
                                                                                                <div class="rt-property-body">
                                                                                                    <h2 class="property-price">${property.FormattedPrice || '$' + (property.ListPrice || 0).toLocaleString()}</h2>
                                                                                                    <p class="property-location">${property.FullAddress || property.UnparsedAddress || 'Address not available'}</p>
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
                                                                                                                <span>${property.LivingAreaRange || 'N/A'} sq ft (Living Area)</span>
                                                                                                            </li>

                                                                                                            <li class="aminity-item">
                                                                                                                <img src="{{ asset('frontend/assets/images/icons/clock.svg') }}">
                                                                                                                <span>${property.DaysOnMarket || 0} ${(property.DaysOnMarket || 0) == 1 ? 'Day' : 'Days'}</span>
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

            priceSlider.noUiSlider.on('update', function (values) {
                $('#amount').val(values.join(' - '));
                $('#min_price').val(Number(values[0].replace('$', '').replace(',', '')));
                $('#max_price').val(Number(values[1].replace('$', '').replace(',', '')));
            });

            // Auto-apply filters when slider changes
            priceSlider.noUiSlider.on('change', function (values) {
                // Debounce to prevent too many requests
                clearTimeout(window.priceFilterTimeout);
                window.priceFilterTimeout = setTimeout(function () {
                    const formData = new FormData();
                    formData.append('min_price', Number(values[0].replace('$', '').replace(',', '')));
                    formData.append('max_price', Number(values[1].replace('$', '').replace(',', '')));

                    // Include ALL current form values
                    $('#property-filter-form').serializeArray().forEach(field => {
                        if (field.name !== 'min_price' && field.name !== 'max_price') {
                            formData.append(field.name, field.value);
                        }
                    });

                    submitFilters(formData);
                }, 500); // 500ms delay
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

            sidePriceSlider.noUiSlider.on('update', function (values) {
                $('#price-range').val(values.join(' - '));
                // Update hidden fields for advanced price filter
                $('#adv_min_price').val(Number(values[0].replace('$', '').replace(',', '')));
                $('#adv_max_price').val(Number(values[1].replace('$', '').replace(',', '')));
            });

            // Auto-apply advanced price filter when slider changes
            sidePriceSlider.noUiSlider.on('change', function (values) {
                // Debounce to prevent too many requests
                clearTimeout(window.advPriceFilterTimeout);
                window.advPriceFilterTimeout = setTimeout(function () {
                    const formData = new FormData();
                    formData.append('adv_min_price', Number(values[0].replace('$', '').replace(',', '')));
                    formData.append('adv_max_price', Number(values[1].replace('$', '').replace(',', '')));

                    // Include other advanced filter values
                    const advFormData = collectAdvancedFilters();
                    for (const [key, value] of Object.entries(advFormData)) {
                        if (Array.isArray(value)) {
                            value.forEach(val => formData.append(key, val));
                        } else {
                            formData.append(key, value);
                        }
                    }

                    submitFilters(formData);
                }, 500); // 500ms delay
            });
        }

        // Area Slider Initialization (Main - if exists)
        const areaSlider = document.getElementById('area-slider');
        if (areaSlider) {
            const safeMaxArea = isNaN(maxArea) ? 3000 : Math.max(2500, maxArea);
            const currentMin = parseInt('{{ request('min_area', 0) }}') || 0;
            const currentMax = Math.min(parseInt('{{ request('max_area', $maxArea ?? 3000) }}') || safeMaxArea, safeMaxArea);

            console.log('Main area slider init:', {
                safeMaxArea: safeMaxArea,
                currentMin: currentMin,
                currentMax: currentMax
            });

            noUiSlider.create(areaSlider, {
                start: [currentMin, currentMax],
                connect: true,
                step: 100,
                range: {
                    'min': 0,
                    'max': safeMaxArea
                },
                format: {
                    to: value => {
                        const rounded = Math.round(value);
                        return isNaN(rounded) ? '0' : rounded.toLocaleString();
                    },
                    from: value => {
                        if (typeof value === 'string') {
                            const clean = value.replace(/[^0-9.]/g, '');
                            const num = parseFloat(clean);
                            return isNaN(num) ? 0 : num;
                        }
                        return value || 0;
                    }
                }
            });

            // Rest of the code remains the same...
        }

        // Sidebar Area Filter (Advanced) - Static max area
        const sideAreaSlider = document.getElementById('area-slider-filter');
        if (sideAreaSlider) {
            // STATIC maximum area - always 10000, minimum 500
            const staticMaxArea = 10000;
            const staticMinArea = 500;
            const advMinArea = parseInt('{{ request('adv_min_area', 500) }}') || staticMinArea;
            const advMaxArea = parseInt('{{ request('adv_max_area', 10000) }}') || staticMaxArea;

            console.log('Area slider init:', {
                staticMaxArea: staticMaxArea,
                staticMinArea: staticMinArea,
                advMinArea: advMinArea,
                advMaxArea: advMaxArea
            });

            noUiSlider.create(sideAreaSlider, {
                start: [advMinArea, advMaxArea],
                connect: true,
                step: 100,
                range: {
                    'min': staticMinArea,
                    'max': staticMaxArea
                },
                pips: {
                    mode: 'values',
                    values: [500, 1000, 2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000],
                    density: 100,
                    format: {
                        to: function (value) {
                            if (value === 500) return '500';
                            if (value === 1000) return '1k';
                            if (value === 2000) return '2k';
                            if (value === 3000) return '3k';
                            if (value === 4000) return '4k';
                            if (value === 5000) return '5k';
                            if (value === 6000) return '6k';
                            if (value === 7000) return '7k';
                            if (value === 8000) return '8k';
                            if (value === 9000) return '9k';
                            if (value === 10000) return '10k';
                            return Math.round(value).toLocaleString();
                        }
                    }
                },
                format: {
                    to: value => Math.round(value),
                    from: value => {
                        if (typeof value === 'string') {
                            const clean = value.replace(/[^0-9.]/g, '');
                            const num = parseFloat(clean);
                            return isNaN(num) ? staticMinArea : num;
                        }
                        return value || staticMinArea;
                    }
                }
            });

            sideAreaSlider.noUiSlider.on('update', function (values) {
                const minVal = Math.round(values[0]);
                const maxVal = Math.round(values[1]);
                const safeMin = isNaN(minVal) ? staticMinArea : Math.max(staticMinArea, minVal);
                const safeMax = isNaN(maxVal) ? staticMaxArea : Math.min(Math.max(maxVal, staticMinArea), staticMaxArea);

                $('#area-range').val(safeMin.toLocaleString() + ' - ' + safeMax.toLocaleString() + ' sq ft');
                $('#adv_min_area').val(safeMin);
                $('#adv_max_area').val(safeMax);
            });

            sideAreaSlider.noUiSlider.on('change', function (values) {
                clearTimeout(window.advAreaFilterTimeout);
                window.advAreaFilterTimeout = setTimeout(function () {
                    const safeMin = Math.round(values[0]);
                    const safeMax = Math.round(values[1]);

                    if (isNaN(safeMin) || isNaN(safeMax) || safeMin < 0 || safeMax < 0) return;

                    const formData = new FormData();
                    formData.append('adv_min_area', safeMin);
                    formData.append('adv_max_area', safeMax);

                    const advFormData = collectAdvancedFilters();
                    for (const [key, value] of Object.entries(advFormData)) {
                        if (Array.isArray(value)) {
                            value.forEach(val => formData.append(key, val));
                        } else {
                            formData.append(key, value);
                        }
                    }

                    submitFilters(formData);
                }, 500);
            });
        }

        console.log('maxArea from backend:', maxArea);
        console.log('maxArea type:', typeof maxArea);
        console.log('maxArea parsed:', parseInt(maxArea));
        // Advanced Filter Apply Button - PREVENT MULTIPLE CLICKS
        $('.apply-btn').click(function (e) {
            e.preventDefault();

            // Prevent multiple clicks
            if ($(this).hasClass('loading')) {
                return;
            }
            $(this).addClass('loading');

            // Collect all advanced filter values
            const formData = new FormData();

            // Get all checked building types
            $('input[name="building_type[]"]:checked').each(function () {
                formData.append('building_type[]', $(this).val());
            });

            // Get advanced price range - FIXED
            const sidePriceSlider = document.getElementById('price-slider-filter');
            if (sidePriceSlider) {
                const sidePriceValues = sidePriceSlider.noUiSlider.get();
                formData.append('adv_min_price', Number(sidePriceValues[0].replace('$', '').replace(',', '')));
                formData.append('adv_max_price', Number(sidePriceValues[1].replace('$', '').replace(',', '')));
            }

            // Get advanced area range
            const sideAreaSlider = document.getElementById('area-slider-filter');
            if (sideAreaSlider) {
                const sideAreaValues = sideAreaSlider.noUiSlider.get();
                formData.append('adv_min_area', Math.round(sideAreaValues[0]));
                formData.append('adv_max_area', Math.round(sideAreaValues[1]));
            }

            // Get advanced bedrooms
            const advBedrooms = $('input[name="adv_bedrooms"]:checked').val();
            if (advBedrooms) formData.append('adv_bedrooms', advBedrooms);

            // Get advanced bathrooms
            const advBathrooms = $('input[name="adv_bathrooms"]:checked').val();
            if (advBathrooms) formData.append('adv_bathrooms', advBathrooms);

            // Get square feet ranges
            $('input[name="square_feet[]"]:checked').each(function () {
                formData.append('square_feet[]', $(this).val());
            });

            // Get days on market
            $('input[name="days_on_market[]"]:checked').each(function () {
                formData.append('days_on_market[]', $(this).val());
            });

            // Get flooring types
            $('input[name="InteriorFeatures[]"]:checked').each(function () {
                formData.append('InteriorFeatures[]', $(this).val());
            });

            // Get parking spaces
            $('input[name="ParkingTotal[]"]:checked').each(function () {
                formData.append('ParkingTotal[]', $(this).val());
            });

            // Get parking types
            $('input[name="parking_type[]"]:checked').each(function () {
                formData.append('parking_type[]', $(this).val());
            });

            // Get pool types
            $('input[name="pool_type[]"]:checked').each(function () {
                formData.append('pool_type[]', $(this).val());
            });

            // Get amenities
            $('input[name="amenities[]"]:checked').each(function () {
                formData.append('amenities[]', $(this).val());
            });

            // Get view types
            $('input[name="view_type[]"]:checked').each(function () {
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

            // Re-enable button after short delay
            setTimeout(() => {
                $(this).removeClass('loading');
            }, 1000);
        });

        // Advanced Filter Reset Button - CORRECTED VERSION
        $('.reset-btn').click(function (e) {
            e.preventDefault();

            // Reset all advanced filters
            $('#advance-filter-sidebar input[type="checkbox"]').prop('checked', false);
            $('#advance-filter-sidebar input[type="radio"]').prop('checked', false);

            // Reset price sliders
            if (sidePriceSlider) {
                sidePriceSlider.noUiSlider.set([0, maxPrice]);
            }

            // Reset area sliders
            if (sideAreaSlider) {
                sideAreaSlider.noUiSlider.set([500, 10000]);
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

        // Filter Form Submission with AJAX - PREVENT MULTIPLE SUBMISSIONS
        function submitFilters(formData) {
            // Prevent multiple simultaneous requests
            if ($('#property-list-container').hasClass('loading')) {
                return;
            }

            $('#property-list-container').addClass('loading');
            $('#loading-indicator').show();

            const currentUrl = '{{ url()->current() }}';

            // Hide existing properties and show loading indicator
            $('#property-list-container').hide();
            $('.property-list-header').hide();
            $('#loading-indicator').show();

            // Convert FormData to URLSearchParams for AJAX - FIX PRICE HANDLING
            // In submitFilters() - UPDATE: Sanitize before append
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                let safeValue = value;
                if ((key.includes('area') || key.includes('price')) && (value === 'NaN' || isNaN(parseFloat(value)))) {
                    safeValue = ''; // Skip invalid
                    continue;
                }
                params.append(key, safeValue);
            }

            $.ajax({
                url: currentUrl,
                type: 'GET',
                data: params.toString(),
                success: function (response) {
                    if (response.success) {
                        // Ensure allPropertiesForClustering is set from response
                        if (!response.allPropertiesForClustering && response.properties) {
                            response.allPropertiesForClustering = response.properties;
                        }
                        handleFilterResponse(response);
                    } else {
                        $('#property-list-container').html(
                            '<div class="text-center p-4 text-danger">Failed to load properties. Please try again.</div>'
                        ).show();
                    }
                },
                error: function (xhr) {
                    console.error('Filter Error:', xhr.responseText);
                    $('#property-list-container').html(
                        '<div class="text-center p-4 text-danger">Error loading properties. Please try again.</div>'
                    ).show();
                },
                complete: function () {
                    $('#loading-indicator').hide();
                    $('#property-list-container').removeClass('loading');
                }
            });
        }

        $('#property-filter-form').on('submit', function (e) {
            e.preventDefault();

            // Prevent multiple submissions
            if ($(this).hasClass('loading')) {
                return;
            }
            $(this).addClass('loading');

            const formData = new FormData(this);

            // Add advanced filters to main form submission - OVERRIDE main filters with advanced if they exist
            const advFormData = collectAdvancedFilters();
            for (const [key, value] of Object.entries(advFormData)) {
                if (Array.isArray(value)) {
                    // Remove any existing values for this key first
                    formData.delete(key);
                    value.forEach(val => formData.append(key, val));
                } else {
                    // Remove existing value and add new one
                    formData.set(key, value);
                }
            }

            submitFilters(formData);

            // Re-enable form after short delay
            setTimeout(() => {
                $(this).removeClass('loading');
            }, 1000);
        });


        function collectAdvancedFilters() {
            const filters = {};

            // Collect all advanced filter values
            $('input[name="building_type[]"]:checked').each(function () {
                if (!filters.building_type) filters.building_type = [];
                filters.building_type.push($(this).val());
            });

            if ($('#adv_min_price').val()) filters.adv_min_price = $('#adv_min_price').val();
            if ($('#adv_max_price').val()) filters.adv_max_price = $('#adv_max_price').val();
            if ($('#adv_min_area').val()) filters.adv_min_area = $('#adv_min_area').val();
            if ($('#adv_max_area').val()) filters.adv_max_area = $('#adv_max_area').val();
            if ($('input[name="adv_bedrooms"]:checked').val()) filters.adv_bedrooms = $(
                'input[name="adv_bedrooms"]:checked').val();
            if ($('input[name="adv_bathrooms"]:checked').val()) filters.adv_bathrooms = $(
                'input[name="adv_bathrooms"]:checked').val();

            $('input[name="square_feet[]"]:checked').each(function () {
                if (!filters.square_feet) filters.square_feet = [];
                filters.square_feet.push($(this).val());
            });

            $('input[name="days_on_market[]"]:checked').each(function () {
                if (!filters.days_on_market) filters.days_on_market = [];
                filters.days_on_market.push($(this).val());
            });

            $('input[name="InteriorFeatures[]"]:checked').each(function () {
                if (!filters.InteriorFeatures) filters.InteriorFeatures = [];
                filters.InteriorFeatures.push($(this).val());
            });

            $('input[name="ParkingTotal[]"]:checked').each(function () {
                if (!filters.ParkingTotal) filters.ParkingTotal = [];
                filters.ParkingTotal.push($(this).val());
            });

            $('input[name="parking_type[]"]:checked').each(function () {
                if (!filters.parking_type) filters.parking_type = [];
                filters.parking_type.push($(this).val());
            });

            $('input[name="pool_type[]"]:checked').each(function () {
                if (!filters.pool_type) filters.pool_type = [];
                filters.pool_type.push($(this).val());
            });

            $('input[name="amenities[]"]:checked').each(function () {
                if (!filters.amenities) filters.amenities = [];
                filters.amenities.push($(this).val());
            });

            $('input[name="view_type[]"]:checked').each(function () {
                if (!filters.view_type) filters.view_type = [];
                filters.view_type.push($(this).val());
            });

            return filters;
        }

        function handleFilterResponse(response) {
            if (response.success) {
                // Update ALL property arrays with filtered data
                allProperties = response.properties || [];
                allPropertiesForClustering = response.allPropertiesForClustering || response.properties || [];

                // Reinitialize clustering with new filtered properties
                initializeSuperCluster();

                // Update UI with filtered properties
                currentVisibleProperties = [...allProperties];
                renderPropertyList(currentVisibleProperties);

                // Use filteredCount for display
                $('#properties-count').text(response.filteredCount || 0);
                $('.property-list-header h4').text(`Found ${response.filteredCount || 0} Properties`);

                // Update map with new filtered data
                updateMapMarkers();

                // Update pagination using filteredCount
                if (response.filteredCount > propertiesPerPage) {
                    const pageToUse = response.currentPage || currentPage;
                    initializePagination(response.filteredCount, pageToUse);
                } else {
                    $('.rt-pagination').hide();
                }

                // Show property list container
                $('#property-list-container').show();

                console.log('Filter applied successfully:', {
                    filteredCount: response.filteredCount,
                    propertiesCount: allProperties.length,
                    clusteringCount: allPropertiesForClustering.length
                });
            }
        }

        function showPage(pageNum) {
            // Update the global currentPage variable
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
            $('#property-list-container').hide();

            $.ajax({
                url: '{{ url()->current() }}',
                type: 'GET',
                data: new URLSearchParams(formData).toString(),
                success: function (response) {
                    if (response.success) {
                        handleFilterResponse(response);

                        // Update URL without page reload
                        const url = new URL(window.location.href);
                        url.searchParams.set('page', pageNum);
                        window.history.pushState({}, '', url.toString());

                        // Debug log
                        console.log('Page changed to:', pageNum, 'Global currentPage:', currentPage);
                    }
                },
                error: function (xhr) {
                    console.error('Pagination Error:', xhr.responseText);
                    $('#property-list-container').html(
                        '<div class="text-center p-4 text-danger">Error loading properties.</div>'
                    ).show();
                },
                complete: function () {
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

            // Always show Previous button if not on first page
            if (currentPage > 1) {
                liTag += `<li class="btn prev" data-page="${currentPage - 1}"><span>Prev</span></li>`;
            } else {
                liTag += `<li class="btn prev disabled"><span>Prev</span></li>`;
            }

            // Calculate page range to show exactly 5 pages around current page
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            // Adjust to always show 5 pages when possible
            if (endPage - startPage + 1 < 5) {
                if (startPage === 1) {
                    // We're at the beginning, extend end
                    endPage = Math.min(totalPages, startPage + 4);
                } else if (endPage === totalPages) {
                    // We're at the end, extend start
                    startPage = Math.max(1, endPage - 4);
                }
            }

            // Show exactly 5 consecutive page numbers (or fewer if total pages < 5)
            const pagesToShow = Math.min(5, totalPages);
            startPage = Math.max(1, Math.min(startPage, totalPages - pagesToShow + 1));
            endPage = Math.min(totalPages, startPage + pagesToShow - 1);

            // Show page numbers
            for (let i = startPage; i <= endPage; i++) {
                const active = (currentPage == i) ? "active" : "";
                liTag += `<li class="numb ${active}" data-page="${i}"><span>${i}</span></li>`;
            }

            // Always show Next button if not on last page
            if (currentPage < totalPages) {
                liTag += `<li class="btn next" data-page="${currentPage + 1}"><span>Next</span></li>`;
            } else {
                liTag += `<li class="btn next disabled"><span>Next</span></li>`;
            }

            $('.rt-pagination ul').html(liTag);

            console.log('Pagination created:', {
                currentPage: currentPage,
                totalPages: totalPages,
                startPage: startPage,
                endPage: endPage,
                pagesToShow: pagesToShow
            });
        }

        function initializePagination(totalCount, currentPage = 1) {
            const totalPages = Math.ceil(totalCount / propertiesPerPage);

            if (totalPages > 1) {
                createPagination(totalCount, currentPage);
            } else {
                $('.rt-pagination').hide();
            }
        }

        // Filter synchronization functions
        function syncPropertyTypeToAdvanced() {
            console.log('syncPropertyTypeToAdvanced called');
            const mainValue = $('#property-type').val();
            console.log('Main property type value:', mainValue);
            // Uncheck all advanced building type checkboxes
            $('input[name="building_type[]"]').prop('checked', false);
            // Check the corresponding advanced checkbox if main value is selected
            if (mainValue && mainValue !== '') {
                $(`input[name="building_type[]"][value="${mainValue}"]`).prop('checked', true);
                console.log('Checked advanced checkbox for:', mainValue);
            }
        }

        function syncPropertyTypeToMain() {
            const checkedBoxes = $('input[name="building_type[]"]:checked');
            if (checkedBoxes.length === 1) {
                // If only one checkbox is checked, set main select to that value
                $('#property-type').val(checkedBoxes.val());
            } else if (checkedBoxes.length === 0) {
                // If no checkboxes checked, reset main select
                $('#property-type').val('');
            }
            // If multiple checkboxes checked, keep main select as is (don't change)
        }

        function syncBedroomsToAdvanced() {
            const mainValue = $('#property-bedrooms').val();
            // Uncheck all advanced bedroom radios
            $('input[name="adv_bedrooms"]').prop('checked', false);
            // Check the corresponding advanced radio
            if (mainValue && mainValue !== '') {
                $(`input[name="adv_bedrooms"][value="${mainValue}"]`).prop('checked', true);
            } else {
                // If main is "Any", check the "Any" radio in advanced
                $('#bedrooms-any').prop('checked', true);
            }
        }

        function syncBedroomsToMain() {
            const checkedRadio = $('input[name="adv_bedrooms"]:checked');
            if (checkedRadio.length > 0 && checkedRadio.val() !== '') {
                $('#property-bedrooms').val(checkedRadio.val());
            } else {
                // If "Any" is selected in advanced, set main to empty (Any)
                $('#property-bedrooms').val('');
            }
        }

        function syncBathroomsToAdvanced() {
            console.log('syncBathroomsToAdvanced called');
            const mainValue = $('#property-bathrooms').val();
            console.log('Main bathrooms value:', mainValue);
            // Uncheck all advanced bathroom radios
            $('input[name="adv_bathrooms"]').prop('checked', false);
            // Check the corresponding advanced radio
            if (mainValue && mainValue !== '') {
                $(`input[name="adv_bathrooms"][value="${mainValue}"]`).prop('checked', true);
                console.log('Checked advanced bathroom radio for:', mainValue);
            } else {
                // If main is "Any", check the "Any" radio in advanced
                $('#bathrooms-any').prop('checked', true);
                console.log('Checked "Any" for bathrooms');
            }
        }

        function syncBathroomsToMain() {
            const checkedRadio = $('input[name="adv_bathrooms"]:checked');
            if (checkedRadio.length > 0 && checkedRadio.val() !== '') {
                $('#property-bathrooms').val(checkedRadio.val());
            } else {
                // If "Any" is selected in advanced, set main to empty (Any)
                $('#property-bathrooms').val('');
            }
        }

        function syncPriceToAdvanced() {
            const mainSlider = document.getElementById('price-slider');
            const advSlider = document.getElementById('price-slider-filter');
            if (mainSlider && advSlider) {
                const mainValues = mainSlider.noUiSlider.get();
                advSlider.noUiSlider.set(mainValues);
            }
        }

        function syncPriceToMain() {
            const mainSlider = document.getElementById('price-slider');
            const advSlider = document.getElementById('price-slider-filter');
            if (mainSlider && advSlider) {
                const advValues = advSlider.noUiSlider.get();
                mainSlider.noUiSlider.set(advValues);
            }
        }

        // Initialize everything when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            initializeMap();

            // Use filteredCount for initial pagination
            if (totalProperties > propertiesPerPage) {
                initializePagination(totalProperties, currentPage);
            } else {
                $('.rt-pagination').hide();
            }

            // Event listeners for pagination - FIXED SELECTORS
            $(document).on('click', '.rt-pagination li.numb', function (e) {
                e.preventDefault();
                const pageNum = $(this).data('page');
                if (pageNum) showPage(pageNum);
            });

            $(document).on('click', '.rt-pagination li.prev, .rt-pagination li.next', function (e) {
                e.preventDefault();
                const pageNum = $(this).data('page');
                if (pageNum) showPage(pageNum);
            });

            // Filter synchronization event listeners
            // Main filter changes -> Advanced filter
            $('#property-type').on('change', function () {
                console.log('Property type changed');
                syncPropertyTypeToAdvanced();
            });
            $('#property-bedrooms').on('change', function () {
                console.log('Bedrooms changed');
                syncBedroomsToAdvanced();
            });
            $('#property-bathrooms').on('change', function () {
                console.log('Bathrooms changed');
                syncBathroomsToAdvanced();
            });

            // Advanced filter changes -> Main filter
            $(document).on('change', 'input[name="building_type[]"]', syncPropertyTypeToMain);
            $(document).on('change', 'input[name="adv_bedrooms"]', syncBedroomsToMain);
            $(document).on('change', 'input[name="adv_bathrooms"]', syncBathroomsToMain);

            // Price slider synchronization
            const mainPriceSlider = document.getElementById('price-slider');
            const advPriceSlider = document.getElementById('price-slider-filter');

            if (mainPriceSlider) {
                mainPriceSlider.noUiSlider.on('change', syncPriceToAdvanced);
            }
            if (advPriceSlider) {
                advPriceSlider.noUiSlider.on('change', syncPriceToMain);
            }

            // Initial sync will happen when user opens advanced filters
        });

        $(document).ready(function () {
            console.log('Document ready, initializing autocomplete'); // Debug log

            // Initialize autocomplete for city search using local city data
            $('#city-search').autocomplete({
                source: function (request, response) {
                    var term = request.term.toLowerCase();
                    console.log('City autocomplete search term:', term); // Debug log

                    // Use local GTA cities data instead of Nominatim
                    const gtaCities = @json($gtaCities ?? []);

                    // Filter cities that start with the search term only
                    const matchingCities = gtaCities.filter(function (city) {
                        return city.toLowerCase().startsWith(term);
                    }).slice(0, 8); // Limit to 8 results

                    console.log('Matching cities:', matchingCities); // Debug log

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
                    console.log('Selected city:', ui.item);
                    // Set the selected city value
                    $('#selected_city').val(ui.item.city);
                    // Don't auto-submit - let user click search button
                    // $('#property-filter-form').submit();

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

            // Reset selected city on input clear
            $('#city-search').on('input', function () {
                if (!this.value) {
                    $('#selected_city').val('');
                }
            });

            console.log('Autocomplete initialized'); // Final debug log
        });
    </script>

    @if (session('showLoginPopup'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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

        .media-placeholder {
            display: flex;
            gap: 10px;
            justify-content: center;
            height: 100%;
            align-items: center;
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

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
            20 background-color: #fff3cd;
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

        .ui-widget.ui-widget-content {
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: transparent;
            color: white;
            border-radius: 10px 10px 10px 10px;
            width: 32%;
            background: #0a1e37e0;
        }

        /* Area Slider Pips Styling */
        #area-slider-filter .noUi-pips {
            padding-top: 10px;
        }

        #area-slider-filter .noUi-value {
            font-size: 11px;
            color: #666;
        }

        #area-slider-filter {
            margin-bottom: 25px;
        }

        /* Widget collapse functionality */
        .rt-filter-widget .widget-header {
            cursor: pointer;
        }

        .rt-filter-widget .widget-header i.fa-angle-down {
            transition: transform 0.3s ease;
        }

        .rt-filter-widget .widget-header i.fa-angle-down.rotated {
            transform: rotate(180deg);
        }

        #area-slider-filter .noUi-connect {
            background: #4c418c;
        }

        #area-slider-filter.noUi-target.noUi-horizontal {
            box-shadow: none;
            height: 6px;
        }

        #area-slider-filter.noUi-horizontal .noUi-handle {
            width: 24px;
            height: 24px;
            right: -17px;
            top: -10px;
            border-radius: 50%;
            color: #4c418c;
            border: 3px solid var(--rt-button-color);
            box-shadow: none;
        }

        .noUi-handle:after,
        .noUi-handle:before {
            content: "";
            display: block;
            position: absolute;
            height: 14px;
            width: 1px;
            background: transparent;
            left: 14px;
            top: 6px;
        }
    </style>
@endsection