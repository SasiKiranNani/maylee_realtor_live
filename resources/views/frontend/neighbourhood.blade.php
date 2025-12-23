@extends('layouts.frontend.index')

@section('contents')
    <section class="rt-about-banner-section d-flex align-items-center justify-content-center">
            <div class="container position-relative zindex-2">
                <h1 class="text-center position-relative title page-title text-white">Neighbourhood</h1>
            </div>
        </section>
    <div class="page-content-wrapper sec-pad bg-white">
        <div class="container">
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
    </div>
    
    <div class="page-wrapper rt-property-listing-page d-block w-100 position-relative">
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
                <x-contact source="Neighbourhood" />
            </div>
        </section>
        <!-- Conatct Section End -->
    </div>
@endsection