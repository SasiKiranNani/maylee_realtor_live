@extends('layouts.frontend.index')

@section('contents')
    <div class="rt-page-title-breadcrumb">
        <div class="container">
            <h1 class="page-title">Wishlist</h1>
        </div>
    </div>
    <div class="page-content-wrapper sec-pad bg-white">
        <div class="container">
            <div class="row" id="wishlist-container">
                @if (count($properties) > 0)
                    @foreach ($properties as $property)
                        <div class="rt-property-item col-lg-4 col-md-6 col-sm-6 col-xs-12 my-4">
                            <div class="rt-property-header">
                                <figure class="rt-propert-image"
                                    style="height: 250px; text-align: center; align-items: center;">
                                    @if (!empty($property['MediaURL']) && filter_var($property['MediaURL'], FILTER_VALIDATE_URL))
                                        <img src="{{ $property['MediaURL'] }}" alt="Property Image"
                                            onerror="this.src='{{ asset('frontend/assets/images/properties/property-1.jpg') }}">
                                    @else
                                        <div class="media-placeholder">
                                            <i class="fa-solid fa-camera"></i>
                                            <span>{{ $property['MediaPlaceholder'] ?? 'Media Coming Soon' }}</span>
                                        </div>
                                    @endif
                                </figure>
                                <span class="property-wishlist" data-listing-key="{{ $property['ListingKey'] }}"
                                    data-transaction-type="{{ $property['TransactionType'] }}">
                                    <i class="fa-solid fa-trash"></i>
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
                                                feet²</span>
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
                    <div class="no-properties text-center" id="no-wishlist-message">
                        <p>No wishlist found. Please try again later.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

