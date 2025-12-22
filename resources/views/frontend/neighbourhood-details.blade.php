@extends('layouts.frontend.index')

@section('contents')
    
    <section class="rt-about-banner-section d-flex align-items-center justify-content-center">
            <div class="container position-relative zindex-2">
                <h1 class="page-title city">Welcome To {{ $city->city }}</h1>
                
                <div class="hero-content">
                    <div class="hero-content-wrapper" data-aos="fade-down">
                        <div class="rt-btn-group mt-5">
                            <a href="{{ route('buy', ['search' => $city->city]) }}" class="rt-btn" target="_blank">Buy <span
                                    class="icon"><i class="fa-solid fa-house"></i></span></a>
                            <a href="{{ route('sell', ['search' => $city->city]) }}" class="rt-btn btn-pink" target="_blank">For Sale
                                <span class="icon"><i class="fa-solid fa-house"></i></span></a>
                            <a href="{{ route('lease', ['search' => $city->city]) }}" class="rt-btn btn-main" target="_blank">For Lease
                                <span class="icon"><i class="fa-solid fa-house"></i></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <section class="city-contents">
        <div class="container">
            <div class="city-contents">
                {!! $city->description !!}
            </div>
        </div>
    </section>

    <!-- Related Properties -->
    <section class="rt-related-properties sec-pad overflow-hidden">
        <div class="container">
            <div class="rt-section-title-wrap d-block w-100">
                <h2 class="main-title">Properties in {{ $city->city }}</h2>
            </div>

            @if (!empty($relatedProperties) && count($relatedProperties) > 0)
                <div class="swiper rt-property-carousel rt-carousel top-right-nav">
                    <div class="swiper-wrapper">
                        @foreach ($relatedProperties as $relatedProperty)
                                <div class="swiper-slide">
                                    <div class="rt-property-item neighbourhood-details">
                                        <div class="rt-property-header">
                                            <figure class="rt-propert-image">
                                                <img src="{{ $relatedProperty['MediaURL'] }}"
                                                    alt="{{ $relatedProperty['FullAddress'] ?? '' }}"
                                                    onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}'">
                                            </figure>
                                            <span class="property-type-lable">
                                                {{ $relatedProperty['TransactionType'] === 'For Sale'
                            ? 'Sell'
                            : ($relatedProperty['TransactionType'] === 'For Sub-Lease'
                                ? 'Sub-Lease'
                                : 'Lease') }}
                                            </span>

                                            <span class="property-wishlist"
                                                data-listing-key="{{ $relatedProperty['ListingKey'] ?? '' }}"
                                                data-transaction-type="{{ $relatedProperty['TransactionType'] ?? 'For Sale' }}">
                                                <i class="fa-regular fa-heart"></i>
                                            </span>
                                        </div>

                                        <div class="rt-property-body">
                                            <h2 class="property-price">{{ $relatedProperty['FormattedPrice'] ?? 'N/A' }}</h2>
                                            <h3 class="property-name">
                                                {{ $relatedProperty['StreetNumber'] ?? '' }}
                                                {{ $relatedProperty['StreetName'] ?? '' }}
                                                {{ $relatedProperty['StreetSuffix'] ?? '' }}
                                            </h3>
                                            <p class="property-location">{{ $relatedProperty['FullAddress'] ?? '' }}</p>

                                            <div class="property-meta">
                                                <ul class="aminity-list">
                                                    <li class="aminity-item">
                                                        <img src="{{ asset('frontend/assets/images/icons/bed.svg') }}">
                                                        <span>{{ $relatedProperty['BedroomsTotal'] ?? 'N/A' }} Bedrooms</span>
                                                    </li>
                                                    <li class="aminity-item">
                                                        <img src="{{ asset('frontend/assets/images/icons/bathroom.svg') }}">
                                                        <span>{{ $relatedProperty['BathroomsTotalInteger'] ?? 'N/A' }}
                                                            Bathrooms</span>
                                                    </li>
                                                    <li class="aminity-item">
                                                        <img src="{{ asset('frontend/assets/images/icons/measure-ruler.svg') }}">
                                                        <span>
                                                            @if (!empty($relatedProperty['LivingAreaRange']) && $relatedProperty['LivingAreaRange'] !== 'N/A')
                                                                {{ $relatedProperty['LivingAreaRange'] }}
                                                                sq ft (Living Area)
                                                            @else
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </li>
                                                    <li class="aminity-item">
                                                        <img src="{{ asset('frontend/assets/images/icons/clock.svg') }}">
                                                        <span>
                                                            @php
                                                                $days = $relatedProperty['DaysOnMarket'] ?? 0;
                                                            @endphp
                                                            @if ($days == 1)
                                                                1 Day
                                                            @elseif($days < 30)
                                                                {{ $days }} Days
                                                            @elseif($days < 365)
                                                                {{ floor($days / 30) }} Months
                                                            @else
                                                                {{ floor($days / 365) }} Years
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
                    <p class="text-muted">No properties found in {{ $city }}.</p>
                </div>
            @endif
        </div>
    </section>
    <!-- Related Properties End -->
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
                    <div class="rt-btn-group about mb-4 mt-4">
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
            <x-contact source="Neighbourhood Details" :city="$city->city" />
        </div>
    </section>
    <!-- Conatct Section End -->
@endsection
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .page-title.city {
        color: #fff;
        text-align: center;
    }
        .city-intro-section {
            background: linear-gradient(to right, #f8f9fa, #eef4ff);
        }

        .info-card {
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .icon-wrapper i {
            transition: color 0.3s ease;
        }

        .info-card:hover .icon-wrapper i {
            color: #0d6efd;
        }

        .city-highlights-section {
            background: linear-gradient(135deg, #0056b3, #001f4d);
        }

        .highlight-item i {
            flex-shrink: 0;
            color: #fff;
        }

        .city-contents {
            padding: 20px 0px;
            background: #ffffff;
            border-radius: 18px;
            font-family: "Segoe UI", sans-serif;
            color: #333;
            line-height: 1.75;
        }

        .city-contents h2,
        .city-contents h3 {
            font-size: 26px;
            margin-top: 35px;
            margin-bottom: 15px;
            color: #7f73bf;
            padding-left: 12px;
            font-weight: 700;
            /* text-align: center; */
        }

        .city-contents h2 strong,
        .city-contents h3 strong {
            border-left: 6px solid #7f73bf;
            padding-left: 20px;
        }

        .city-contents p {
            font-size: 17px;
            margin-bottom: 18px;
            color: #444;
            margin: 0 40px 0;
        }

        .city-contents ul {
            margin: 20px 25px 0;
            padding-left: 22px;
            list-style-type: none;
        }

        .city-contents ul li {
            margin-bottom: 15px;
            font-size: 17px;
            position: relative;
            padding-left: 12px;
            color: #333;
        }

        .city-contents ul li::before {
            content: "";
            position: absolute;
            left: -12px;
            top: 9px;
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, #4c418c, #6c5ce7);
            border-radius: 50%;
        }

        .city-contents strong {
            color: #000;
            font-weight: 600;
        }

        @media (max-width: 600px) {
            .city-contents {
                padding: 25px;
            }

            .city-contents h2,
            .city-contents h3 {
                font-size: 22px;
            }

            .city-contents p,
            .city-contents ul li {
                font-size: 16px;
            }
        }

        .rt-btn-group {
            justify-content: center;
        }
        .rt-btn-group.about {
            justify-content: flex-start;
        }

        .hero-content-wrapper {
            position: relative;
        }
    </style>
@endsection