@extends('layouts.frontend.index')

@section('contents')
    <!-- Hero Slider -->
    <section class="swiper rt-hero-slider">
        <div class="swiper-wrapper hero">
            <div class="swiper-slide">
                <img src="{{ asset('frontend/assets/images/slides/slide-1.webp') }}">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('frontend/assets/images/slides/slide-2.webp') }}">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('frontend/assets/images/slides/slide-3.webp') }}">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('frontend/assets/images/slides/slide-4.webp') }}">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('frontend/assets/images/slides/slide-5.webp') }}">
            </div>
        </div>
        <div class="hero-content">
            <div class="hero-content-wrapper" data-aos="fade-up">
                <h2 class="main-title">Buy. <span class="rt-text-primary">Sell.</span> Lease. <span
                        class="rt-text-primary">Simplified.</span></h2>
                <p class="description">Whether buying, selling, or leasing, my dedication is to guide families across the
                    Greater Toronto Area, turning their dream homes into lasting realities with expert knowledge and a
                    heartfelt approach.</p>
                <div class="rt-btn-group mt-5">
                    <a href="{{ route('buy', ['search' => 'Mississauga']) }}" class="rt-btn" target="_blank" target="_blank">Buy <span class="icon"><i
                                class="fa-solid fa-house"></i></span></a>
                    <a href="{{ route('sell', ['search' => 'Mississauga']) }}" class="rt-btn btn-pink" target="_blank" target="_blank">Sell <span class="icon"><i
                                class="fa-solid fa-house"></i></span></a>
                    <a href="{{ route('lease', ['search' => 'Mississauga']) }}" class="rt-btn btn-main" target="_blank" target="_blank">Lease <span class="icon"><i
                                class="fa-solid fa-house"></i></span></a>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Slider End -->
    <!-- Hero Section -->
    <section class="rt-hero-section position-relative w-100 d-none">
        <div class="container">
            <div class="hero-content">
                <h3 class="sub-title d-none">Welcome To Realtor</h3>
                <h2 class="main-title">Buy. <span class="rt-text-primary">Sell.</span> Lease. <span
                        class="rt-text-primary">Simplified.</span></h2>
                <p class="description">No brokers. No middlemen. Just honest, professional guidance to help you buy your
                    dream home, sell at the right price, or lease with ease.</p>
                <div class="rt-btn-group mt-5">
                    <a href="{{ route('buy', ['search' => 'Mississauga']) }}" class="rt-btn" target="_blank">Buy <span class="icon"><i
                                class="fa-solid fa-house"></i></span></a>
                    <a href="{{ route('sell', ['search' => 'Mississauga']) }}" class="rt-btn btn-pink btn-outline" target="_blank">Sell <span class="icon"><i
                                class="fa-solid fa-house"></i></span></a>
                    <a href="{{ route('lease', ['search' => 'Mississauga']) }}" class="rt-btn btn-main" target="_blank">Lease <span class="icon"><i
                                class="fa-solid fa-house"></i></span></a>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->
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
    <!-- Recommended Properties -->
      <section class="rt-recommended-properties sec-pad overflow-hidden">
        <div class="container">
            <div class="rt-section-title-wrap d-block w-100">
                <h2 class="main-title">Explore Homes That Inspire Living</h2>
            </div>
            <div class="swiper rt-property-carousel rt-carousel top-right-nav">
                <div class="swiper-wrapper">
                    @foreach ($properties as $property)
                        <div class="swiper-slide">
                            <div class="rt-property-item index">
                                <div class="rt-property-header">
                                    <figure class="rt-propert-image">
                                        <img src="{{ $property['MediaURL'] ?? asset('frontend/assets/images/properties/property-2.jpg') }}"
                                            alt="Property Image">
                                    </figure>
                                    <span class="property-type-lable">{{ $property['TransactionType'] ?? 'Buy' }}</span>
                                    <span class="property-wishlist" data-listing-key="{{ $property['ListingKey'] ?? '' }}"
                                        data-transaction-type="{{ $property['TransactionType'] ?? 'For Sale' }}">
                                        <i class="fa-regular fa-heart"></i>
                                    </span>
                                </div>
                                <div class="rt-property-body">
                                    <h2 class="property-price">${{ number_format($property['ListPrice'] ?? 0) }}</h2>
                                     <p class="property-location">
                                        {{ $property['FullAddress'] ?? '' }}
                                    </p>
                                    <div class="property-meta">
                                        <ul class="aminity-list">
                                            <li class="aminity-item">
                                                <img src="{{ asset('frontend/assets/images/icons/bed.svg') }}">
                                                <span>{{ $property['BedroomsTotal'] ?? 'N/A' }} Bedrooms</span>
                                            </li>
                                            <li class="aminity-item">
                                                <img src="{{ asset('frontend/assets/images/icons/bathroom.svg') }}">
                                                <span>{{ $property['BathroomsTotalInteger'] ?? 'N/A' }} Bathrooms</span>
                                            </li>
                                            <li class="aminity-item">
                                                <img src="{{ asset('frontend/assets/images/icons/measure-ruler.svg') }}">
                                                <span>{{ $property['LivingAreaRange'] ?? 'N/A' }}
                                                    sq ft (Living Area)</span>
                                            </li>
                                            <li class="aminity-item">
                                                <img src="{{ asset('frontend/assets/images/icons/clock.svg') }}">
                                                <span>{{ $property['DaysOnMarket'] ?? 0 }} Days</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="property-footer">
                                    @php
                                        $transactionType = strtolower($property['TransactionType'] ?? 'for sale');
                                        $listingKey = $property['ListingKey'] ?? null;

                                        if (
                                            in_array($transactionType, [
                                                'for lease',
                                                'lease',
                                                'for sub-lease',
                                                'sublease',
                                                'sub-lease',
                                            ])
                                        ) {
                                            $exploreUrl = route('lease.details', $listingKey);
                                        } else {
                                            $exploreUrl = route('buy.details', $listingKey);
                                        }
                                    @endphp

                                    <a href="{{ $exploreUrl }}" class="btn-property-explore">
                                        Explore Property
                                        <span class="icon"><i class="fa-solid fa-angles-up"></i></span>
                                    </a>

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
    <!-- Recommended Properties End -->

    <!-- Mission Section -->
    <section class="rt-mission-section d-block w-100 position-relative">
        <div class="container text-center">
            <h1>Mission Statement</h1>
            <p>"To guide you through every stage of your real estate journey with transparency,
                integrity, and
                unwavering commitment. We prioritize your best interests, delivering exceptional results that empower
                your next chapter."</p>
            <h1>使命:</h1>
            <p>"以透明誠信與堅定承諾，引領您走過房地產旅程的每個階段。我們始終以您的權益為先，創造卓越成果，開啟您的人生新篇章"</p>
        </div>
        <div class="container text-center">
            <h1>Our Vision</h1>
            <p>"To be your lifelong real estate partner—recognized for transforming every
                transaction into a seamless,
                rewarding, and trusted experience."</p>
            <h1>願景:</h1>
            <p>"成為您終身信賴的房地產夥伴——將每次交易化為流暢無礙、回報豐厚且值得託付的體驗而備受認可"</p>
        </div>
    </section>
    <!-- Mission Section End -->
    
    <!-- Featured Location -->
    <section class="rt-featured-neighbourhood sec-pad overflow-hidden bg-white">
        <div class="container">
            <div class="rt-section-title-wrap d-block w-100 mb-5">
                <h2 class="main-title">Explore Communities where you Belong</h2>
            </div>
            <div class="swiper rt-location-carousel rt-carousel">
                <div class="swiper-wrapper">
                    @foreach ($cities as $cityItem)
                        @php
                            $isModel = is_object($cityItem);
                            $cityName = $isModel ? $cityItem->city : $cityItem;
                            $count = $cityCounts[$cityName] ?? 0;
                            $image = Storage::url($cityItem->image) ?? ($isModel && $cityItem->image);
                        @endphp
                        <div class="swiper-slide">
                            <a href="{{ route('neighbourhood-details', ['city' => $cityName]) }}">
                                <div class="rt-location-item">
                                    <div class="location-image">
                                        <figure>
                                            <img src="{{ $image }}" alt="{{ $cityName }}">
                                        </figure>
                                    </div>
                                    <div class="location-content">
                                        <div class="location-count">
                                            <h3 class="location-name position-relative">{{ $cityName }}</h3>
                                            <p class="property-count position-relative">
                                                <span class="count-no">{{ $count }}</span>
                                                {{ $count == 1 ? 'Property' : 'Properties' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
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
    <!-- Featured Location End -->

    <!-- Property Category -->
    <section class="rt-popular-searches-sec sec-pad overflow-hidden">
        <div class="container">
            <div class="rt-section-title-wrap d-block w-100 mb-5">
                <h2 class="main-title">Explore your Dream among popular Choices</h2>
            </div>

            <div class="swiper rt-category-carousel rt-carousel">
                <div class="swiper-wrapper">
                    @php
                        // Define icons for specific subtypes
                        $typeIcons = [
                            'Detached' => 'detached-home-icon.webp',
                            'Semi-Detached' => 'semi-dtached-home-icon.webp',
                            'Freehold Townhouse' => 'townhouse-icon.webp',
                            'Condo Townhouse' => 'townhouse-icon.webp',
                            'Condo Apartment' => 'apartment-icon.webp',
                            'Link' => 'detached-home-icon.webp',
                            'Duplex' => 'duplex-home-icon.webp',
                            'Triplex' => 'duplex-home-icon.webp',
                            'Multiplex' => 'duplex-home-icon.webp',
                            'Other' => 'default-icon.png',
                        ];
                    @endphp

                    @foreach ($propertySubTypeCounts as $subType => $count)
                        @php
                            // Prepare icon and display name
                            $icon = $typeIcons[$subType] ?? 'default-icon.png';
                            $displayName = $subType ?: 'Other';
                        @endphp

                        <div class="swiper-slide">
                            <div class="rt-category-item">
                                <a href="#">
                                    <div class="category-icon mb-2">
                                        <div class="icon mx-auto">
                                            <img src="{{ asset('frontend/assets/images/icons/' . $icon) }}" alt="">
                                        </div>
                                    </div>
                                    <div class="category-content text-center">
                                        <h3>{{ $displayName }}</h3>
                                        <p>{{ $count }} {{ $count == 1 ? 'Property' : 'Properties' }}</p>
                                    </div>
                                </a>
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

    <!-- Property Category End -->

    <!-- Testimonial -->
    <section class="rt-testimonial-sec testimonial-index sec-pad overflow-hidden bg-white">
        <div class="container">
            <div class="rt-section-title-wrap d-block w-100">
                <h2 class="text-center position-relative main-title animate-letters" style="color: var(--rt-accent-color);"><b
                        style="color: var(--rt-accent-color);">"</b>Your Goals. Our Guidance. Exceptional Results. <b
                        style="color: var(--rt-accent-color);">"</b></h2>
                <h2 class="text-center position-relative main-title mb-3 animate-letters" style="color: var(--rt-accent-color);"><b
                        style="color: var(--rt-accent-color);">"</b> 您的目標，我們的指引，成就非凡 <b style="color: var(--rt-accent-color);">"</b></h2>
            </div>
        </div>
    </section>
    <section class="rt-testimonial-sec sec-pad overflow-hidden bg-white">
        <div class="container">
            <div class="rt-section-title-wrap d-block w-100">
                <h2 class="main-title mt-5">Hear What Our Happy Home Owners Say</h2>
            </div>
             <x-testimonials />
        </div>
    </section>
    <!-- Testimonial End -->
   

    <!-- Conatct Section -->
    <section class="rt-home-contact-sec sec-pad position-relative overflow-hidden" id="rt-home-contact-sec" data-aos="fade-up">
        <div class="container">
            <x-contact source="Home" />
        </div>
    </section>
    <!-- Conatct Section End -->
@endsection
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        .service-card {
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .rebate-box {
            background: linear-gradient(90deg, #0d6efd, #4f9eff);
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .buyer-services-section h1 {
                font-size: 1.9rem;
            }
        }

        .selling-services-section {
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fb 100%);
        }

        .service-card {
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .pricing-box {
            border: 2px solid #e5e5f0;
            transition: 0.3s ease;
        }

        .pricing-box:hover {
            transform: translateY(-5px);
            border-color: #473d7f;
        }

        .rebate-box {
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
    </style>
@endsection
