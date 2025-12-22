@extends('layouts.frontend.index')

@section('contents')
    <div class="page-wrapper rt-property-single-page d-block w-100 position-relative">
        <div class="rt-property-details-header">
            <div class="container position-relative">
                <div class="property-info">
                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-sm-12 pull-left">
                            <h1 class="property-name">{{ $property['StreetNumber'] }}
                                {{ $property['StreetName'] }}
                                {{ $property['StreetSuffix'] }}</h1>
                            <div class="property-location">
                                <span class="icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </span>
                                <span class="text">{{ $property['FullAddress'] }}</span>
                            </div>
                            <div class="property-posted mt-2">
                                <span class="icon">
                                    <i class="fa-regular fa-clock"></i>
                                </span>
                                <span class="text">
                                    @if ($property['DaysOnMarket'] == 1)
                                        1 Day
                                    @elseif($property['DaysOnMarket'] < 30)
                                        {{ $property['DaysOnMarket'] }} Days
                                    @elseif($property['DaysOnMarket'] < 365)
                                        {{ floor($property['DaysOnMarket'] / 30) }} Months
                                    @else
                                        {{ floor($property['DaysOnMarket'] / 365) }} Years
                                    @endif
                                    Back Posted
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-12 pull-right">
                            <div class="property-status mb-1">
                                {{ $property['TransactionType'] }}
                            </div>
                            <h2 class="property-price">
                                {{ $property['FormattedPrice'] }}
                                @if ($property['TransactionType'] === 'For Lease' || $property['TransactionType'] === 'For Sub-Lease')
                                    <span>/Month</span>
                                @endif
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rt-property-details-wrapper d-block w-100 sec-pad position-relative">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-8">
                        <!-- Property Gallery -->
                        <div class="rt-property-gallery-box inner-box mb-4">
                            <div class="box-content">
                                <!-- Gallery Slider -->
                                <div class="swiper rt-single-property-gallery-slider">
                                    <div class="swiper-wrapper">
                                        @foreach ($property['MediaURLs'] as $mediaUrl)
                                            <div class="swiper-slide">
                                                <img src="{{ $mediaUrl }}"
                                                    onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">
                                            </div>
                                        @endforeach
                                        @if (empty($property['MediaURLs']))
                                            <div class="swiper-slide">
                                                <img src="{{ asset('frontend/assets/images/properties/property-1.jpg') }}">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="swiper-button-next" aria-label="Next slide"></div>
                                    <div class="swiper-button-prev" aria-label="Previous slide"></div>
                                </div>
                                <!-- Gallery Slider End -->
                                <!-- Thumbs Slider -->
                                <div class="swiper rt-single-property-gallery-thumbs-slider">
                                    <div class="swiper-wrapper">
                                        @foreach ($property['MediaURLs'] as $mediaUrl)
                                            <div class="swiper-slide">
                                                <img src="{{ $mediaUrl }}"
                                                    onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">
                                            </div>
                                        @endforeach
                                        @if (empty($property['MediaURLs']))
                                            <div class="swiper-slide">
                                                <img src="{{ asset('frontend/assets/images/properties/property-1.jpg') }}">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="swiper-button-next" aria-label="Next slide"></div>
                                    <div class="swiper-button-prev" aria-label="Previous slide"></div>
                                </div>
                                <!-- Thumbs Slider End -->
                            </div>
                        </div>
                        <!-- Property Gallery End -->
                        <!-- Property Overview -->
                        <div class="rt-property-overview-box inner-box mb-4">
                            <div class="box-header">
                                <h2 class="box-title">Overview</h2>
                            </div>
                            <div class="box-content">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4 col-md-4">
                                        <div class="feature-panel mb-4">
                                            <div class="feature-img">
                                                <img
                                                    src="{{ asset('frontend/assets/images/icons/rt-property-type.webp') }}">
                                            </div>
                                            <div class="feature-content">
                                                <h5>Type:</h5>
                                                <p>{{ $property['PropertySubType'] ?? 'Land' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-4">
                                        <div class="feature-panel mb-4">
                                            <div class="feature-img">
                                                <img src="{{ asset('frontend/assets/images/icons/rt-psize.webp') }}">
                                            </div>
                                            <div class="feature-content">
                                                <h5>Area:</h5>
                                                <p>{{ $property['BuildingAreaTotal'] }}
                                                    {{ $property['BuildingAreaUnits'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-4">
                                        <div class="feature-panel mb-4">
                                            <div class="feature-img">
                                                <img src="{{ asset('frontend/assets/images/icons/rt-bed.webp') }}">
                                            </div>
                                            <div class="feature-content">
                                                <h5>Beds:</h5>
                                                <p>{{ $property['BedroomsTotal'] ?? 0 }} Bedrooms</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-4">
                                        <div class="feature-panel mb-4">
                                            <div class="feature-img">
                                                <img src="{{ asset('frontend/assets/images/icons/rt-bathroom.webp') }}">
                                            </div>
                                            <div class="feature-content">
                                                <h5>Baths:</h5>
                                                <p>{{ $property['BathroomsTotalInteger'] ?? 0 }} Bathrooms</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-4">
                                        <div class="feature-panel mb-4">
                                            <div class="feature-img">
                                                <img src="{{ asset('frontend/assets/images/icons/furnished.webp') }}">
                                            </div>
                                            <div class="feature-content">
                                                <h5>Furnished:</h5>
                                                <p>{{ in_array('Furnished', (array) ($property['InteriorFeatures'] ?? [])) ? 'Yes' : 'No' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-4">
                                        <div class="feature-panel mb-4">
                                            <div class="feature-img">
                                                <img src="{{ asset('frontend/assets/images/icons/rt-garage-icon.webp') }}">
                                            </div>
                                            <div class="feature-content">
                                                <h5>Parking:</h5>
                                                <p>{{ $property['GarageType'] ?? 'None' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="property-description">
                                    <p>{{ $property['PublicRemarks'] ?? 'No description available.' }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Property Overview End -->
                        <!-- Property Units -->
                        <div class="rt-property-units-box inner-box mb-4">
                            <div class="box-header">
                                <h2 class="box-title">Property Units</h2>
                            </div>
                            <div class="box-content">
                                @php
                                    $bedroomGroups = [];
                                    $bedrooms = $property['BedroomsTotal'] ?? 0;
                                    $bedroomGroups[$bedrooms] = [
                                        'count' => 1,
                                        'bathrooms' => $property['BathroomsTotalInteger'] ?? 0,
                                        'area' => $property['BuildingAreaTotal'] ?? 'N/A',
                                        'price' =>
                                            $property['FormattedPrice'] ??
                                            '$' . number_format($property['ListPrice'] ?? 0),
                                        'date' => $property['ListingContractDate']
                                            ? \Carbon\Carbon::parse($property['ListingContractDate'])->format('M jS Y')
                                            : 'N/A',
                                        'image_count' => count($property['MediaURLs'] ?? []),
                                    ];
                                @endphp
                                @foreach ($bedroomGroups as $beds => $unit)
                                    <div class="rt-property-unit-type-card mb-3">
                                        <div class="card-header">
                                            <div class="pull-left">
                                                <h3>{{ $beds == 0 ? 'Land' : $beds . ' Bedroom' . ($beds > 1 ? 's' : '') }}
                                                </h3>
                                            </div>
                                            <div class="pull-right">
                                                <span class="no-of-units">{{ $unit['count'] }}
                                                    Unit{{ $unit['count'] > 1 ? 's' : '' }}</span>
                                                <span class="icon">
                                                    <i class="fa-solid fa-angle-down"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <ul class="units-list">
                                                <li>
                                                    <div class="pull-left">
                                                        <span class="price">{{ $unit['price'] }}</span>
                                                        <span class="bath">{{ $unit['bathrooms'] }}
                                                            Bath{{ $unit['bathrooms'] > 1 ? 's' : '' }}</span>
                                                        <span class="sqft">{{ $unit['area'] }}
                                                            {{ $property['BuildingAreaUnits'] }}</span>
                                                        <span class="date">{{ $unit['date'] }}</span>
                                                    </div>
                                                    <div class="pull-right">
                                                        <div class="unit-img-gallery-link">
                                                            <i class="fa-solid fa-image"></i>
                                                            <span class="img-count">{{ $unit['image_count'] }}</span>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Property Units End -->
                        <!-- Property Details -->
                        <div class="rt-property-details-box inner-box mb-4">
                            <div class="box-header">
                                <h2 class="box-title">Property Details</h2>
                            </div>
                            <div class="box-content">
                                <ul class="rt-property-info-list">
                                    <li>
                                        <span class="title">Property Type:</span>
                                        <span>{{ $property['PropertyType'] ?? 'Commercial' }}</span>
                                    </li>
                                    <li>
                                        <span class="title">Property Sub Type:</span>
                                        <span>{{ $property['PropertySubType'] ?? 'Land' }}</span>
                                    </li>
                                    <li>
                                        <span class="title">Lease Terms:</span>
                                        <span>{{ $property['MinimumRentalTermMonths'] ?? 'N/A' }}</span>
                                    </li>
                                    <li>
                                        <span class="title">Parking Spots:</span>
                                        <span>{{ $property['ParkingTotal'] ?? '0' }}</span>
                                    </li>
                                    <li>
                                        <span class="title">Short Term:</span>
                                        <span>{{ isset($property['MinimumRentalTermMonths']) && $property['MinimumRentalTermMonths'] < 12 ? 'Yes' : 'No' }}</span>
                                    </li>
                                    <li>
                                        <span class="title">Furnished:</span>
                                        <span>{{ in_array('Furnished', (array) ($property['InteriorFeatures'] ?? [])) ? 'Yes' : 'No' }}</span>
                                    </li>
                                    <li>
                                        <span class="title">Year Built:</span>
                                        <span>{{ $property['YearBuilt'] ?? 'N/A' }}</span>
                                    </li>
                                    <li>
                                        <span class="title"># of Floors:</span>
                                        <span>N/A</span>
                                    </li>
                                    <li>
                                        <span class="title"># of units:</span>
                                        <span>1</span>
                                    </li>
                                    <li>
                                        <span class="title">MLS:</span>
                                        <span>{{ $property['ListingKey'] ?? 'X12294934' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Property Details End -->
                        <!-- Property Features -->
                        <div class="rt-property-features inner-box mb-4">
                            <div class="box-header">
                                <h2 class="box-title">Features</h2>
                            </div>
                            <div class="box-content">
                                <div class="row">
                                    @php
                                        $features = array_unique(
                                            array_merge(
                                                (array) ($property['InteriorFeatures'] ?? []),
                                                (array) ($property['ParkingFeatures'] ?? []),
                                                (array) ($property['PoolFeatures'] ?? []),
                                                (array) ($property['AssociationAmenities'] ?? []),
                                                (array) ($property['CommunityFeatures'] ?? []),
                                                (array) ($property['LaundryFeatures'] ?? []),
                                                (array) ($property['View'] ?? []),
                                                (array) ($property['Sewer'] ?? []),
                                                (array) ($property['Utilities'] ?? []),
                                                (array) ($property['BusinessType'] ?? []),
                                            ),
                                        );
                                        $features = array_filter($features, fn($f) => !empty($f));
                                        if (empty($features)) {
                                            $features = ['Industrial Zoning', 'Fenced Lot'];
                                        }
                                    @endphp
                                    @foreach ($features as $feature)
                                        <div class="col-xs-12 col-sm-6 col-md-4 mb-3">
                                            <div class="rt-feature-item">
                                                <div class="icon"><i class="fa-solid fa-check"></i></div>
                                                <div class="text">{{ $feature }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- Property Features End -->
                        <!-- Near By Amenities -->
                        <div class="rt-property-near-by-amenitirs inner-box">
                            <div class="box-header">
                                <h2 class="box-title">Nearby Amenities</h2>
                            </div>

                            <div class="box-content">
                                <div class="amenities-card">
                                    <!-- Left: Amenity List -->
                                    <div class="amenities-left">
                                        <ul class="places-type-list">
                                            @php
                                                $amenities = [
                                                    ['key' => 'school', 'label' => 'Schools', 'icon' => 'fa-school'],
                                                    [
                                                        'key' => 'bus_station',
                                                        'label' => 'Bus Stations',
                                                        'icon' => 'fa-bus',
                                                    ],
                                                    [
                                                        'key' => 'restaurant',
                                                        'label' => 'Restaurants',
                                                        'icon' => 'fa-utensils',
                                                    ],
                                                    [
                                                        'key' => 'pharmacy',
                                                        'label' => 'Pharmacies',
                                                        'icon' => 'fa-pills',
                                                    ],
                                                    ['key' => 'gym', 'label' => 'Gyms', 'icon' => 'fa-dumbbell'],
                                                    [
                                                        'key' => 'supermarket',
                                                        'label' => 'Supermarkets',
                                                        'icon' => 'fa-cart-shopping',
                                                    ],
                                                    [
                                                        'key' => 'hospital',
                                                        'label' => 'Hospitals',
                                                        'icon' => 'fa-hospital',
                                                    ],
                                                    ['key' => 'park', 'label' => 'Parks', 'icon' => 'fa-tree'],
                                                    ['key' => 'cafe', 'label' => 'Cafes', 'icon' => 'fa-mug-hot'],
                                                    [
                                                        'key' => 'convenience_store',
                                                        'label' => 'Convenience Stores',
                                                        'icon' => 'fa-store',
                                                    ],
                                                    [
                                                        'key' => 'gas_station',
                                                        'label' => 'Gas Stations',
                                                        'icon' => 'fa-gas-pump',
                                                    ],
                                                    ['key' => 'laundry', 'label' => 'Laundromats', 'icon' => 'fa-soap'],
                                                    ['key' => 'bar', 'label' => 'Bars', 'icon' => 'fa-beer-mug-empty'],
                                                    [
                                                        'key' => 'bank',
                                                        'label' => 'Banks',
                                                        'icon' => 'fa-building-columns',
                                                    ],
                                                ];
                                            @endphp

                                            @foreach ($amenities as $item)
                                                <li class="places-type" data-osm="{{ $item['key'] }}">
                                                    <span>
                                                        <i class="fa-solid {{ $item['icon'] }} me-2"></i>
                                                        {{ $item['label'] }}
                                                    </span>
                                                    <input type="checkbox" class="amenity-toggle-input"
                                                        value="{{ $item['key'] }}">
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <!-- Right: Map -->
                                    <div class="amenities-map-panel">
                                        <div id="amenitiesMap"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Near By Amenities End -->
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4">
                        <div class="rt-agent-details-widget rt-property-side-widget">
                            <div class="profile-pic mb-2">
                                <img src="{{ asset('frontend/assets/images/maylee.webp') }}">
                            </div>
                            <div class="profile-info">
                                <h3>Maylee</h3>
                                <p>Sales Representative At Right At Home Realty</p>
                                <ul class="contact-info">
                                    <li>
                                        <a href="#">
                                            <span class="icon">
                                                <i class="fa-solid fa-phone"></i>
                                            </span>
                                            <span class="text">904-567-7890</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="icon">
                                                <i class="fa-solid fa-envelope-open-text"></i>
                                            </span>
                                            <span class="text">me@mayleerealtor.com</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="rt-book-tour-widget rt-property-side-widget">
                            <div class="widget-header mb-3">
                                <h4>Request A Tour</h4>
                            </div>
                            <div class="widget-body">
                                <form action="{{ route('tour-bookings.store') }}" method="POST" id="tour-request-form">
                                    @csrf
                                    <input type="hidden" name="listing_key"
                                        value="{{ $property['ListingKey'] ?? '' }}">

                                    <input type="hidden" name="transaction_type"
                                        value="{{ $property['TransactionType'] ?? '' }}">

                                    <div class="form-group d-block w-100">
                                        <input type="text" name="name" id="reqt-name" placeholder="Enter Name"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="error-msg" style="color: red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group d-block w-100">
                                        <input type="email" name="email" id="reqt-email" placeholder="Enter Email"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="error-msg" style="color: red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group d-block w-100">
                                        <input type="text" name="phone" id="reqt-phone"
                                            placeholder="Enter Phone No" value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <div class="error-msg" style="color: red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group d-block w-100">
                                        <input type="date" name="date" id="reqt-date" min="{{ date('Y-m-d') }}"
                                            value="{{ old('date') }}" required>
                                        @error('date')
                                            <div class="error-msg" style="color: red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group d-block w-100">
                                        <select name="slot_booking_id" id="reqt-time" class="rt-select" required>
                                            <option value="">Select Date First</option>
                                        </select>
                                        @error('slot_booking_id')
                                            <div class="error-msg" style="color: red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group d-block w-100">
                                        <textarea name="message" id="reqt-msg" placeholder="Enter Message"></textarea>
                                        @error('message')
                                            <div class="error-msg" style="color: red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group d-block w-100">
                                        <label for="reqt-consent" class="rt-checkbox">
                                            <input class="rt_checkbox_input" type="checkbox" id="reqt-consent"
                                                name="consent" value="1" {{ old('consent') ? 'checked' : '' }}
                                                required>
                                            <svg class="rt_checkbox_icon" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 22 22">
                                                <rect width="21" height="21" x=".5" y=".5" fill="#FFF"
                                                    stroke="#006F94" rx="3"></rect>
                                                <path class="tick" stroke="#4c418c" fill="none"
                                                    stroke-linecap="round" stroke-width="4" d="M4 10l5 5 9-9"></path>
                                            </svg>
                                            <span class="rt_checkbox_label">I consent to be contacted regarding this
                                                property.</span>
                                        </label>
                                        @error('consent')
                                            <div class="error-msg" style="color: red;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group d-block w-100">
                                        <button class="book-tour" type="submit">Schedule A Tour</button>
                                    </div>
                                    @if (session('success'))
                                        <div style="color: green; margin-top: 10px;">{{ session('success') }}</div>
                                    @endif
                                    @if (session('error'))
                                        <div style="color: red; margin-top: 10px;">{{ session('error') }}</div>
                                    @endif
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- Related Properties -->
        <section class="rt-related-properties sec-pad overflow-hidden">
            <div class="container">
                <div class="rt-section-title-wrap d-block w-100">
                    <h2 class="main-title">Similar Properties</h2>
                </div>

                @if (!empty($relatedProperties) && count($relatedProperties) > 0)
                    <div class="swiper rt-property-carousel rt-carousel top-right-nav">
                        <div class="swiper-wrapper">
                            @foreach ($relatedProperties as $relatedProperty)
                                <div class="swiper-slide">
                                    <div class="rt-property-item">
                                        <div class="rt-property-header">
                                            <figure class="rt-propert-image">
                                                <img src="{{ $relatedProperty['MediaURL'] }}"
                                                    alt="{{ $relatedProperty['FullAddress'] }}"
                                                    onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">
                                            </figure>
                                            <span class="property-type-lable">
                                                {{ $relatedProperty['TransactionType'] === 'For Sale'
                                                    ? 'Sell'
                                                    : ($relatedProperty['TransactionType'] === 'For Sub-Lease'
                                                        ? 'Sub-Lease'
                                                        : 'Lease') }}
                                            </span>

                                            <span class="property-wishlist">
                                                <i class="fa-regular fa-heart"></i>
                                            </span>
                                        </div>
                                        <div class="rt-property-body">
                                            <h2 class="property-price">{{ $relatedProperty['FormattedPrice'] }}</h2>
                                            <h3 class="property-name">{{ $relatedProperty['StreetNumber'] }}
                                                {{ $relatedProperty['StreetName'] }}
                                                {{ $relatedProperty['StreetSuffix'] }}</h3>
                                            <p class="property-location">{{ $relatedProperty['FullAddress'] }}</p>
                                            <div class="property-meta">
                                                <ul class="aminity-list">
                                                    <li class="aminity-item">
                                                        <img src="{{ asset('frontend/assets/images/icons/bed.svg') }}">
                                                        <span>{{ $relatedProperty['BedroomsTotal'] }} Bedrooms</span>
                                                    </li>
                                                    <li class="aminity-item">
                                                        <img
                                                            src="{{ asset('frontend/assets/images/icons/bathroom.svg') }}">
                                                        <span>{{ $relatedProperty['BathroomsTotalInteger'] }}
                                                            Bathrooms</span>
                                                    </li>
                                                    <li class="aminity-item">
                                                        <img
                                                            src="{{ asset('frontend/assets/images/icons/measure-ruler.svg') }}">
                                                        <span>
                                                            @if ($relatedProperty['BuildingAreaTotal'] !== 'N/A')
                                                                {{ number_format($relatedProperty['BuildingAreaTotal']) }}
                                                                {{ $relatedProperty['BuildingAreaUnits'] }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </li>
                                                    <li class="aminity-item">
                                                        <img src="{{ asset('frontend/assets/images/icons/clock.svg') }}">
                                                        <span>
                                                            @if ($relatedProperty['DaysOnMarket'] == 1)
                                                                1 Day
                                                            @elseif($relatedProperty['DaysOnMarket'] < 30)
                                                                {{ $relatedProperty['DaysOnMarket'] }} Days
                                                            @elseif($relatedProperty['DaysOnMarket'] < 365)
                                                                {{ floor($relatedProperty['DaysOnMarket'] / 30) }} Months
                                                            @else
                                                                {{ floor($relatedProperty['DaysOnMarket'] / 365) }} Years
                                                            @endif
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="property-footer">
                                            @if ($relatedProperty['TransactionType'] === 'For Sale')
                                                <a href="{{ route('buy.details', $relatedProperty['ListingKey']) }}"
                                                    class="btn-property-explore">
                                                    Explore Property
                                                    <span class="icon"><i class="fa-solid fa-angles-up"></i></span>
                                                </a>
                                            @else
                                                <a href="{{ route('lease.details', $relatedProperty['ListingKey']) }}"
                                                    class="btn-property-explore">
                                                    Explore Property
                                                    <span class="icon"><i class="fa-solid fa-angles-up"></i></span>
                                                </a>
                                            @endif
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
                @else
                    <div class="no-related-properties text-center py-5">
                        <p class="text-muted">No similar properties found in {{ $property['City'] }}.</p>
                    </div>
                @endif
            </div>
        </section>
        <!-- Related Properties End -->
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
                                        <i class="fa-solid fa-fax"></i>
                                    </div>
                                    <div class="content">
                                        <h3>Fax</h3>
                                        <p>905-695-0900</p>
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

        <!-- Property Action buttons -->
        <div class="rt-property-single-action-btn-sec">
            <a href="#">Contact Agent</a>
            <a href="#">book a showing</a>
        </div>
        <!-- Property Action buttons end -->
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .rt-book-tour-widget button.book-tour {
            background: var(--rt-button-color);
            color: #ffffff;
            border-radius: 50px;
            font-size: 16px;
            text-transform: uppercase;
            line-height: 40px;
            height: 42px;
            border: 1px solid #e8e8e8;
            outline: 0;
            box-shadow: none;
            width: 100%;
            font-weight: 400;
            padding-left: 18px;
            padding-right: 18px;
        }

        .amenities-card {
            display: flex;
            background: transparent;
            align-items: stretch;
        }

        .amenities-left {
            width: 225px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e6e6e6;
            overflow-y: auto;
            max-height: 480px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .places-type-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .places-type {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 15px;
            color: #333;
            cursor: pointer;
            transition: background 0.2s;
        }

        .places-type:hover {
            background: #f9fafb;
        }

        .places-type i {
            width: 20px;
            text-align: center;
            color: rgb(76 65 140) !important;
        }

        .amenity-toggle-input {
            width: 18px;
            height: 18px;
            accent-color: #rgb(76 65 140) !important;
            cursor: pointer;
        }

        .amenities-map-panel {
            flex: 1;
            min-height: 480px;
            /* border-radius: 10px; */
            overflow: hidden;
            border: 1px solid #e6e6e6;
            /* margin-left: 16px; */
        }

        #amenitiesMap {
            height: 100%;
            width: 100%;
        }

        @media (max-width: 900px) {
            .amenities-card {
                flex-direction: column;
            }

            .amenities-left {
                width: 100%;
                margin-bottom: 10px;
                max-height: 300px;
            }
        }
    </style>
@endsection

@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Pass correct property data to JavaScript -->
    <script>
        // Pass property data to JavaScript
        window.propertyData = {
            price: {{ $property['PriceForCalculator'] ?? ($property['ListPrice'] ?? 0) }},
            listPrice: {{ $property['ListPrice'] ?? 0 }},
            transactionType: "{{ $property['TransactionType'] }}",
            isLease: {{ $property['TransactionType'] === 'For Lease' ? 'true' : 'false' }},
            formattedPrice: "{{ $property['FormattedPrice'] }}",
            priceSuffix: "{{ $property['PriceSuffix'] ?? '' }}"
        };

        console.log('Property data passed to JS:', window.propertyData);
    </script>
    <script>
        $(document).ready(function() {
            const timeSelect = $('#reqt-time');
            const dateInput = $('#reqt-date');

            if (dateInput.val()) {
                timeSelect.prop('disabled', false);
                // Trigger change to reload if old date on error
                dateInput.trigger('change');
            }

            dateInput.on('change', function() {
                const selectedDate = $(this).val();
                if (!selectedDate) {
                    timeSelect.prop('disabled', 'disabled').html(
                        '<option value="">Select Date First</option>');
                    timeSelect.niceSelect('update');
                    return;
                }
                timeSelect.prop('disabled', false).html(
                    '<option value="">Loading available slots...</option>');
                timeSelect.niceSelect('update');

                $.ajax({
                    url: '{{ route('get-available-time-slots') }}?date=' + selectedDate,
                    type: 'GET',
                    success: function(response) {
                        timeSelect.html('<option value="">Select Time</option>');
                        if (response.success && response.timeSlots.length > 0) {
                            response.timeSlots.forEach(function(slot) {
                                // Use data attributes for time_slot_id if needed for display
                                timeSelect.append(
                                    `<option value="${slot.value}" data-time-slot-id="${slot.id}">${slot.text}</option>`
                                );
                            });
                        } else {
                            timeSelect.html(
                                '<option value="">No available slots for this date</option>'
                            );
                        }
                        timeSelect.niceSelect('update');
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr);
                        timeSelect.html('<option value="">Error loading slots</option>');
                        timeSelect.niceSelect('update');
                    }
                });
            });

            $('#tour-request-form').on('submit', function(e) {
                if (!dateInput.val() || timeSelect.val() === '' || timeSelect.prop('disabled') || !$(
                        '#reqt-consent').is(':checked')) {
                    e.preventDefault();
                    alert('Please fill all fields, select a date and time, and consent.');
                    return false;
                }
            });
        });
    </script>
@endsection
