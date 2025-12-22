<?php

namespace App\Http\Controllers;

use App\Mail\TourBookingMail;
use App\Models\SlotBooking;
use App\Models\TimeSlot;
use App\Models\City;
use App\Models\TourBooking;
use Carbon\Carbon;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\SellingRequest;
use App\Models\SellingRequestImage;
use App\Models\Wishlist;
use App\Traits\AddressFormatter;

class PropertyController extends Controller
{
    use AddressFormatter;

    private $baseUrl = 'https://query.ampre.ca/odata/';
    private $propertiesPerPage = 50;
    private $excludedPropertyTypes = ['Commercial'];
    private $excludedPropertySubTypes = [
        'Commercial',
        'Commercial Retail',
        'Industrial',
        'Investment',
        'Land',
        'Farm',
        'Locker',
        'Mobile Trailer',
        'Office',
        'Other',
        'Parking Space',
        'Room',
        'Sale of Business',
        'Shared Room',
        'Store W Apt/Office',
        'Upper Level',
        'Vacant Land',
        'Vacant Land Condo',
    ];

    private function getGtaCities()
    {
        return Cache::remember('active_cities', now()->addHours(24), function () {
            return City::where('status', true)->pluck('city')->toArray();
        });
    }

    private function getCityCoordinates()
    {
        return Cache::remember('city_coordinates', now()->addHours(24), function () {
            return City::where('status', true)
                ->get()
                ->mapWithKeys(function ($city) {
                    return [$city->city => ['latitude' => $city->latitude, 'longitude' => $city->longitude]];
                })
                ->toArray();
        });
    }

    private function getHttpClient()
    {
        $token = env('AMP_API_TOKEN');
        if (!$token) {
            throw new \Exception('AMP_API_TOKEN not set in .env file');
        }

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->timeout(120)->connectTimeout(10)->retry(3, 2000, function ($exception, $request) {
            if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                Log::warning('Request timed out, retrying...', ['url' => $request->url()]);
                return true;
            }
            if ($exception->response && $exception->response->status() === 429) {
                $retryAfter = $exception->response->header('X-Rate-Limit-Retry-After-Seconds') ?? 2;
                Log::warning('Rate limit hit, retrying after ' . $retryAfter . ' seconds');
                sleep($retryAfter);
                return true;
            }
            return false;
        });
    }

    private function geocodeAddress($address)
    {
        $cacheKey = 'geocode_' . md5($address);
        $cached = Cache::get($cacheKey);
        if ($cached) {
            Log::info('Geocode cache hit for address: ' . $address, $cached);
            return $cached;
        }

        try {
            sleep(1);
            $response = Http::withHeaders([
                'User-Agent' => 'RealEstateApp/1.0 (contact: ' . env('NOMINATIM_CONTACT_EMAIL', 'your-email@example.com') . ')',
                'Accept' => 'application/json',
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $address . ', Ontario, Canada',
                        'format' => 'json',
                        'limit' => 1,
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data)) {
                    $coords = [
                        'latitude' => (float) $data[0]['lat'],
                        'longitude' => (float) $data[0]['lon'],
                    ];
                    Cache::put($cacheKey, $coords, now()->addDays(30));
                    Log::info('Geocoded address: ' . $address, $coords);
                    return $coords;
                } else {
                    Log::warning('No geocoding results for address: ' . $address);
                    return null;
                }
            } else {
                Log::warning('Geocoding failed for address: ' . $address, [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Geocoding error for address: ' . $address . ' - ' . $e->getMessage());
            return null;
        }
    }

    private function buildPropertyFilter($transactionTypes, Request $request = null)
    {
        $reverseMapping = [
            'Freehold Townhouse' => 'Att/Row/Townhouse',
        ];

        $citiesFilter = "";
        $statusFilter = "StandardStatus eq 'Active'";

        if ($request && ($request->filled('selected_city') || $request->filled('city_filter') || $request->filled('search'))) {
            $cityName = $request->filled('selected_city') ? $request->selected_city :
                ($request->filled('search') ? $request->search : $request->city_filter);
            $citiesFilter = "City eq '" . addslashes($cityName) . "'";
        } else {
            $citiesFilter = "City in ('" . implode("','", array_map('addslashes', $this->getGtaCities())) . "')";
        }

        $transactionTypeFilter = "";
        if ($transactionTypes) {
            if (is_array($transactionTypes)) {
                $transactionTypeFilter = " and (TransactionType eq '" . implode("' or TransactionType eq '", array_map('addslashes', $transactionTypes)) . "')";
            } else {
                $transactionTypeFilter = " and TransactionType eq '" . addslashes($transactionTypes) . "'";
            }
        }

        $filter = "$citiesFilter and $statusFilter$transactionTypeFilter";

        Log::info('Base filter built with TransactionType', ['filter' => $filter, 'transactionTypes' => $transactionTypes]);

        if ($request instanceof Request) {
            $filters = [];

            if ($request->filled('search')) {
                $search = addslashes($request->search);
                $filters[] = "(contains(StreetName,'$search') or contains(City,'$search') or contains(PostalCode,'$search') or contains(UnparsedAddress,'$search'))";
            }

            $propertyType = $request->filled('adv_property_type') ? $request->adv_property_type : ($request->filled('property_type') ? $request->property_type : null);
            if ($propertyType && $propertyType !== '') {
                $apiPropertyType = isset($reverseMapping[$propertyType]) ? $reverseMapping[$propertyType] : $propertyType;

                if ($propertyType === 'Semi-Detached') {
                    $apiPropertyType = 'Semi-Detached ';
                }

                if (in_array($apiPropertyType, $this->excludedPropertySubTypes)) {
                    Log::warning('Attempted to filter by excluded property subtype: ' . $apiPropertyType);
                } elseif ($propertyType === 'Other') {
                    $otherSubTypes = $this->fetchPropertySubTypes()['other'] ?? [];
                    if (!empty($otherSubTypes)) {
                        $otherFilters = array_map(fn($subType) => "PropertySubType eq '" . addslashes($subType) . "'", $otherSubTypes);
                        $filters[] = "(" . implode(' or ', $otherFilters) . ")";
                    }
                } else {
                    $filters[] = "PropertySubType eq '" . addslashes($apiPropertyType) . "'";
                }
            }

            $bedrooms = $request->filled('adv_bedrooms') ? $request->adv_bedrooms : ($request->filled('bedrooms') ? $request->bedrooms : null);
            if ($bedrooms && $bedrooms !== '') {
                $filters[] = $bedrooms === '6+' ? "BedroomsTotal ge 6" : "BedroomsTotal eq " . (int) $bedrooms;
            }

            $bathrooms = $request->filled('adv_bathrooms') ? $request->adv_bathrooms : ($request->filled('bathrooms') ? $request->bathrooms : null);
            if ($bathrooms && $bathrooms !== '') {
                $filters[] = $bathrooms === '6+' ? "BathroomsTotalInteger ge 6" : "BathroomsTotalInteger eq " . (int) $bathrooms;
            }

            $minPriceStr = $request->filled('adv_min_price') ? $request->adv_min_price : ($request->filled('min_price') ? $request->min_price : null);
            $maxPriceStr = $request->filled('adv_max_price') ? $request->adv_max_price : ($request->filled('max_price') ? $request->max_price : null);
            $minPrice = $minPriceStr ? (int) str_replace(['$', ','], '', $minPriceStr) : null;
            $maxPrice = $maxPriceStr ? (int) str_replace(['$', ','], '', $maxPriceStr) : null;

            if ($minPrice !== null && $maxPrice !== null && $minPrice > 0 && $maxPrice > 0) {
                if ($minPrice <= $maxPrice) {
                    $filters[] = "ListPrice ge $minPrice and ListPrice le $maxPrice";
                }
            } elseif ($minPrice !== null && $minPrice > 0) {
                $filters[] = "ListPrice ge $minPrice";
            } elseif ($maxPrice !== null && $maxPrice > 0) {
                $filters[] = "ListPrice le $maxPrice";
            }

            if ($request->has('building_type') && !empty($request->building_type) && !$request->filled('property_type') && !$request->filled('adv_property_type')) {
                $types = array_slice(array_unique((array) $request->input('building_type')), 0, 10);
                if (!empty($types)) {
                    $validTypes = array_diff($types, $this->excludedPropertySubTypes);
                    if (!empty($validTypes)) {
                        $subTypeFilters = array_map(fn($t) => "PropertySubType eq '" . addslashes($t) . "'", $validTypes);
                        $filters[] = "(" . implode(' or ', $subTypeFilters) . ")";
                    }
                }
            }

            if ($request->has('square_feet') && !empty($request->square_feet)) {
                $ranges = array_slice(array_unique((array) $request->input('square_feet')), 0, 5);
                if (!empty($ranges)) {
                    $areaFilters = [];
                    foreach ($ranges as $range) {
                        if ($range == 'over-5000') {
                            $areaFilters[] = "LivingAreaRange ge 5000";
                        } elseif (strpos($range, '-') !== false) {
                            [$min, $max] = explode('-', $range);
                            $min = (int) $min;
                            $max = (int) $max;
                            if ($min > 0 && $max > 0 && $min <= $max) {
                                $areaFilters[] = "LivingAreaRange ge $min and LivingAreaRange le $max";
                            } elseif ($min > 0) {
                                $areaFilters[] = "LivingAreaRange ge $min";
                            } elseif ($max > 0) {
                                $areaFilters[] = "LivingAreaRange le $max";
                            }
                        }
                    }
                    if (!empty($areaFilters)) {
                        $filters[] = "(" . implode(' or ', $areaFilters) . ")";
                    }
                }
            }

            if ($request->has('days_on_market') && !empty($request->days_on_market)) {
                $selected = array_unique((array) $request->input('days_on_market'));
                $minDays = null;
                $daysMap = [
                    '1day' => 1,
                    '7days' => 7,
                    '14days' => 14,
                    '30days' => 30,
                    '90days' => 90,
                    '6months' => 180,
                    '12months' => 365,
                    '24months' => 730,
                    '36months' => 1095,
                ];
                foreach ($selected as $d) {
                    if ($d == 'any') {
                        continue;
                    }
                    if (isset($daysMap[$d])) {
                        if ($minDays === null || $daysMap[$d] < $minDays) {
                            $minDays = $daysMap[$d];
                        }
                    }
                }
                if ($minDays !== null) {
                    $date = Carbon::today()->subDays($minDays)->toDateString();
                    $filters[] = "ListingContractDate ge $date";
                    Log::debug('Days on Market filter applied', ['minDays' => $minDays, 'date' => $date, 'selected' => $selected]);
                }
            } else {
            }

            if ($request->has('ParkingTotal') && !empty($request->ParkingTotal)) {
                $spaces = array_unique((array) $request->input('ParkingTotal'));
                if (!empty($spaces)) {
                    $hasZero = in_array('0', $spaces);
                    $hasFourPlus = in_array('4+', $spaces);
                    $specificSpaces = array_filter($spaces, fn($s) => $s !== '0' && $s !== '4+');
                    $spaceFilters = [];
                    if ($hasZero) {
                        $spaceFilters[] = "ParkingTotal eq 0";
                    }
                    if ($hasFourPlus) {
                        $spaceFilters[] = "ParkingTotal ge 4";
                    }
                    if (!empty($specificSpaces)) {
                        $spaceFilters[] = "ParkingTotal in (" . implode(',', $specificSpaces) . ")";
                    }
                    if (!empty($spaceFilters)) {
                        $filters[] = "(" . implode(' or ', $spaceFilters) . ")";
                    }
                }
            }

            if (!empty($filters)) {
                $filter .= " and " . implode(' and ', $filters);
            }

            Log::info('Final filter with advanced filters', [
                'filter' => $filter,
                'request_params' => $request->all(),
                'filter_length' => strlen($filter),
            ]);
        }

        return $filter;
    }

    private function isPropertyExcluded($propertyType, $propertySubType, $transactionType = null)
    {
        $propertyType = $propertyType ?? '';
        $propertySubType = $propertySubType ?? '';

        $excludedSubTypesLower = array_map('strtolower', $this->excludedPropertySubTypes);
        if (in_array(strtolower($propertySubType), $excludedSubTypesLower)) {
            return true;
        }

        if (in_array($propertyType, $this->excludedPropertyTypes)) {
            if (is_array($transactionType)) {
                $isLease = in_array('For Lease', $transactionType) || in_array('For Sub-Lease', $transactionType);
            } else {
                $isLease = $transactionType === 'For Lease' || $transactionType === 'For Sub-Lease';
            }
            if (!$isLease) {
                return true;
            }
        }

        if (is_array($transactionType)) {
            $isLease = in_array('For Lease', $transactionType) || in_array('For Sub-Lease', $transactionType);
        } else {
            $isLease = $transactionType === 'For Lease' || $transactionType === 'For Sub-Lease';
        }
        if (!$isLease) {
            $commercialKeywords = ['Commercial', 'Business', 'Industrial', 'Investment', 'Retail', 'Office', 'Sale Of Business'];
            foreach ($commercialKeywords as $keyword) {
                if (
                    stripos($propertyType, $keyword) !== false ||
                    stripos($propertySubType, $keyword) !== false
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    private function fetchMaxPrice($transactionTypes)
    {
        $httpClient = $this->getHttpClient();
        $filter = $this->buildPropertyFilter($transactionTypes);
        try {
            $response = $httpClient->get($this->baseUrl . 'Property', [
                '$filter' => $filter,
                '$select' => 'ListPrice',
                '$top' => 1,
                '$orderby' => 'ListPrice desc',
            ]);

            if ($response->successful()) {
                $maxPrice = $response->json()['value'][0]['ListPrice'] ?? 240000;
                Log::info('Max ListPrice fetched:', ['maxPrice' => $maxPrice]);
                return $maxPrice;
            }
        } catch (\Exception $e) {
            Log::error('Max price fetch error: ' . $e->getMessage());
        }

        return 240000;
    }

    private function fetchMaxArea($transactionTypes)
    {
        $httpClient = $this->getHttpClient();
        $filter = $this->buildPropertyFilter($transactionTypes);

        try {
            // Fetch a good sample of properties
            $response = $httpClient->get($this->baseUrl . 'Property', [
                '$filter' => $filter,
                '$select' => 'LivingAreaRange',
                '$top' => 1000, // Get more properties for accurate max
                '$orderby' => 'LivingAreaRange desc',
            ]);

            if ($response->successful() && !empty($response->json()['value'])) {
                $properties = $response->json()['value'];
                $maxArea = 0;

                Log::info('Scanning ' . count($properties) . ' properties for max area');

                foreach ($properties as $property) {
                    $rangeStr = $property['LivingAreaRange'] ?? '';

                    // Skip empty or invalid values
                    if (empty($rangeStr) || $rangeStr === 'N/A' || $rangeStr === 'null') {
                        continue;
                    }

                    // Log for debugging
                    Log::debug('Processing LivingAreaRange:', ['range' => $rangeStr]);

                    // Parse the range string
                    $currentMax = $this->parseLivingAreaRange($rangeStr);

                    if ($currentMax > $maxArea) {
                        $maxArea = $currentMax;
                        Log::debug('New max area found:', ['max' => $maxArea, 'range' => $rangeStr]);
                    }
                }

                // If we found areas, add buffer
                if ($maxArea > 0) {
                    // Round up to nearest 500 and add 500 buffer
                    $maxArea = ceil($maxArea / 500) * 500 + 500;

                    // Don't go below 2500 based on your image
                    $maxArea = max(2500, $maxArea);

                    Log::info('Final max area calculated: ' . $maxArea);
                    return $maxArea;
                }
            }
        } catch (\Exception $e) {
            Log::error('Max area fetch error: ' . $e->getMessage());
        }

        Log::warning('Using default max area: 3000');
        return 1000000; // Default based on your image
    }

    private function parseLivingAreaRange($rangeStr)
    {
        $rangeStr = trim($rangeStr);

        Log::debug('Parsing LivingAreaRange:', ['original' => $rangeStr]);

        if (empty($rangeStr) || $rangeStr === 'N/A' || $rangeStr === 'null') {
            return 0;
        }

        // Handle different formats

        // 1. Check for range like "2000-2500"
        if (preg_match('/(\d+(?:,\d+)*)\s*-\s*(\d+(?:,\d+)*)/', $rangeStr, $matches)) {
            $min = (int) str_replace(',', '', $matches[1]);
            $max = (int) str_replace(',', '', $matches[2]);
            Log::debug('Range format detected:', ['min' => $min, 'max' => $max]);
            return max($min, $max);
        }

        // 2. Check for "Under X" or "< X"
        if (preg_match('/(?:Under|under|<)\s*(\d+(?:,\d+)*)/', $rangeStr, $matches)) {
            $value = (int) str_replace(',', '', $matches[1]);
            Log::debug('Under format detected:', ['value' => $value]);
            return $value;
        }

        // 3. Check for "X+" format
        if (preg_match('/(\d+(?:,\d+)*)\s*\+/', $rangeStr, $matches)) {
            $value = (int) str_replace(',', '', $matches[1]);
            Log::debug('Plus format detected:', ['value' => $value]);
            return $value;
        }

        // 4. Try to extract any number from the string
        if (preg_match('/(\d+(?:,\d+)*)/', $rangeStr, $matches)) {
            $value = (int) str_replace(',', '', $matches[1]);
            Log::debug('Number extracted:', ['value' => $value]);
            return $value;
        }

        Log::debug('No number found in LivingAreaRange:', ['range' => $rangeStr]);
        return 0;
    }

    private function fetchPropertySubTypes($transactionType = null)
    {
        $cacheKey = 'property_subtypes' . ($transactionType ? '_' . md5(is_array($transactionType) ? implode(',', $transactionType) : $transactionType) : '');
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($transactionType) {
            $defaultSubTypes = [
                'Detached',
                'Semi-Detached',
                'Freehold Townhouse',
                'Condo Townhouse',
                'Condo Apartment',
                'Link',
                'Duplex',
                'Triplex',
                'Multiplex',
                'Other'
            ];

            $httpClient = $this->getHttpClient();
            $baseFilter = "City in ('" . implode("','", array_map('addslashes', $this->getGtaCities())) . "') and StandardStatus eq 'Active'";

            if ($transactionType) {
                if (is_array($transactionType)) {
                    $transactionFilter = " and (TransactionType eq '" . implode("' or TransactionType eq '", array_map('addslashes', $transactionType)) . "')";
                } else {
                    $transactionFilter = " and TransactionType eq '" . addslashes($transactionType) . "'";
                }
                $baseFilter .= $transactionFilter;
            }

            try {
                Log::info('API CALL: fetchPropertySubTypes - Fetching property subtypes', [
                    'filter' => $baseFilter,
                    'top' => 1000,
                    'transactionType' => $transactionType
                ]);

                $response = $httpClient->get($this->baseUrl . 'Property', [
                    '$filter' => $baseFilter,
                    '$select' => 'PropertySubType',
                    '$top' => 1000,
                ]);

                Log::info('API RESPONSE: fetchPropertySubTypes', [
                    'status' => $response->status(),
                    'records_count' => $response->successful() ? count($response->json()['value'] ?? []) : 0
                ]);

                if ($response->successful()) {
                    $subTypes = array_filter(array_unique(array_column($response->json()['value'] ?? [], 'PropertySubType')));

                    $subtypeMapping = [
                        'Att/Row/Townhouse' => 'Freehold Townhouse',
                    ];

                    $subTypes = array_map(function ($subType) use ($subtypeMapping) {
                        return $subtypeMapping[$subType] ?? $subType;
                    }, $subTypes);

                    $filteredSubTypes = array_filter($subTypes, function ($subType) {
                        return !$this->isPropertyExcluded(null, $subType);
                    });

                    $merged = array_unique(array_merge($defaultSubTypes, array_diff($filteredSubTypes, $defaultSubTypes)));

                    Log::debug('Unique PropertySubType values from API after filtering:', ['subTypes' => $merged]);

                    $mainCategories = [
                        'Detached',
                        'Semi-Detached',
                        'Freehold Townhouse',
                        'Condo Townhouse',
                        'Condo Apartment',
                        'Link',
                        'Duplex',
                        'Triplex',
                        'Multiplex',
                        'Other'
                    ];

                    $otherCategories = [
                        'Detached Condo',
                        'Common Element Condo',
                        'Bungalow',
                        'Basement',
                        'Lower Level',
                        'Rural Residential',
                        'Co-op Apartment',
                        'Fourplex',
                        'Farm'
                    ];

                    $organizedMain = array_intersect($mainCategories, $merged);
                    $organizedOther = array_intersect($otherCategories, $merged);

                    if (in_array('Other', $mainCategories)) {
                        $organizedMain[] = 'Other';
                    }

                    $allTypes = array_unique(array_merge($organizedMain, $organizedOther));

                    return [
                        'main' => array_values(array_unique($organizedMain)),
                        'other' => array_values($organizedOther),
                        'all' => array_values($allTypes)
                    ];
                }
            } catch (\Exception $e) {
                Log::error('PropertySubType fetch error: ' . $e->getMessage());
            }

            $defaultMain = [
                'Detached',
                'Semi-Detached',
                'Freehold Townhouse',
                'Condo Townhouse',
                'Condo Apartment',
                'Link',
                'Duplex',
                'Triplex',
                'Multiplex',
                'Other'
            ];

            $defaultOther = [
                'Detached Condo',
                'Common Element Condo',
                'Bungalow',
                'Basement',
                'Lower Level',
                'Rural Residential',
                'Co-op Apartment',
                'Fourplex',
                'Farm'
            ];

            if ($transactionType) {
                if (is_array($transactionType)) {
                    $isLease = in_array('For Lease', $transactionType) || in_array('For Sub-Lease', $transactionType);
                } else {
                    $isLease = $transactionType === 'For Lease' || $transactionType === 'For Sub-Lease';
                }
                if ($isLease) {
                    $defaultMain = array_diff($defaultMain, ['Triplex', 'Fourplex']);
                    $defaultOther = array_diff($defaultOther, ['Detached Condo', 'Rural Residential', 'Farm']);
                }
            }

            $allDefaults = array_merge($defaultMain, $defaultOther);

            return [
                'main' => array_values($defaultMain),
                'other' => array_values($defaultOther),
                'all' => array_values($allDefaults)
            ];
        });
    }

    private function fetchMedia($listingKeys, $maxPerProperty = 1)
    {
        if (empty($listingKeys)) {
            return [];
        }

        $httpClient = $this->getHttpClient();
        $keysCsv = "'" . implode("','", array_map('addslashes', $listingKeys)) . "'";
        $filter = "ResourceRecordKey in ($keysCsv) and MediaCategory eq 'Photo' and ModificationTimestamp ge 2020-01-01T00:00:00Z";

        try {
            $response = $httpClient->get("{$this->baseUrl}Media", [
                '$filter' => $filter,
                '$orderby' => 'Order asc',
                '$top' => 2000,
            ]);

            if (!$response->successful()) {
                Log::warning('Media fetch failed', ['status' => $response->status()]);
                return [];
            }

            $allMedia = $response->json()['value'] ?? [];
            Log::info('PHOTOS LOADED', [
                'keys_requested' => count($listingKeys),
                'photos_found' => count($allMedia),
                'sample_key' => $allMedia[0]['ResourceRecordKey'] ?? 'none',
            ]);
        } catch (\Exception $e) {
            Log::error('Media fetch exception: ' . $e->getMessage());
            return [];
        }

        $mediaMap = [];
        foreach ($allMedia as $item) {
            $key = $item['ResourceRecordKey'] ?? null;
            $url = $item['MediaURL'] ?? null;
            if (!$key || !$url || !filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $lowerUrl = strtolower($url);
            if (str_contains($lowerUrl, 'thumbnail') || str_contains($lowerUrl, '/small/') || str_contains($lowerUrl, '/thumb/') || str_contains($lowerUrl, '240x') || str_contains($lowerUrl, '480x')) {
                continue;
            }

            if (str_contains($lowerUrl, 'nowatermark') || str_contains($lowerUrl, '-nw')) {
                $mediaMap[$key] = [$url];
                continue;
            }

            $mediaMap[$key] = $mediaMap[$key] ?? [];
            if (count($mediaMap[$key]) < $maxPerProperty) {
                $mediaMap[$key][] = $url;
            }
        }

        Log::info('FINAL PHOTOS RESULT', [
            'properties_with_photos' => count($mediaMap),
            'total_photos' => array_sum(array_map('count', $mediaMap)),
        ]);

        return $mediaMap;
    }

    private function fetchCounts($filter, $propertySubTypes = null)
    {
        $cacheKey = 'counts_' . md5($filter);

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($filter, $propertySubTypes) {

            try {
                $httpClient = $this->getHttpClient();

                $cityCounts = array_fill_keys($this->getGtaCities(), 0);
                $propertySubTypeCounts = array_fill_keys($propertySubTypes ?? [], 0);

                $uniqueFloorings = [];
                $uniqueParkingTotal = [];
                $uniqueParkingTypes = [];
                $uniquePoolTypes = [];
                $uniqueAmenities = [];
                $uniqueViewTypes = [];

                $flooringTypes = [];
                $parkingTotal = ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4+' => 0];
                $parkingTypes = [];
                $poolTypes = [];
                $amenities = [];
                $viewTypes = [];

                $totalActiveProperties = 0;
                $pageSize = 1000;
                $skip = 0;
                $maxPropertiesToFetch = 5000;
                $propertiesFetched = 0;

                while (true) {

                    Log::info('API CALL: fetchCounts - Fetching page', [
                        'filter' => $filter,
                        'top' => $pageSize,
                        'skip' => $skip,
                        'propertiesFetched' => $propertiesFetched
                    ]);

                    $response = $httpClient->get($this->baseUrl . 'Property', [
                        '$filter' => $filter,
                        '$select' => 'City,PropertySubType,InteriorFeatures,ParkingTotal,ParkingFeatures,GarageType,PoolFeatures,AssociationAmenities,CommunityFeatures,LaundryFeatures,View',
                        '$top' => $pageSize,
                        '$skip' => $skip,
                        '$count' => 'true',
                    ]);

                    Log::info('API RESPONSE: fetchCounts', [
                        'status' => $response->status(),
                        'records_count' => $response->successful() ? count($response->json()['value'] ?? []) : 0,
                        'total_count' => $response->successful() ? ($response->json()['@odata.count'] ?? 'unknown') : 'unknown'
                    ]);

                    if (!$response->successful()) {
                        Log::error('Count fetch failed: Status ' . $response->status(), [
                            'response' => $response->body(),
                            'filter' => $filter
                        ]);
                        break;
                    }

                    $records = $response->json()['value'] ?? [];
                    $totalActiveProperties = $response->json()['@odata.count'] ?? $totalActiveProperties + count($records);

                    foreach ($records as $record) {

                        // CITY
                        $city = trim($record['City'] ?? '');
                        if (isset($cityCounts[$city])) {
                            $cityCounts[$city]++;
                        }

                        // PROPERTY SUB-TYPE
                        $subType = trim($record['PropertySubType'] ?? 'N/A');
                        if (isset($propertySubTypeCounts[$subType])) {
                            $propertySubTypeCounts[$subType]++;
                        }

                        // FLOORING
                        $interiorFeatures = is_array($record['InteriorFeatures'])
                            ? $record['InteriorFeatures']
                            : (empty($record['InteriorFeatures']) ? [] : [$record['InteriorFeatures']]);

                        $hasFlooring = false;
                        foreach ($interiorFeatures as $feature) {
                            $feature = trim($feature);
                            if ($feature !== '') {
                                $uniqueFloorings[$feature] = true;
                                $flooringTypes[$feature] = ($flooringTypes[$feature] ?? 0) + 1;
                                $hasFlooring = true;
                            }
                        }
                        if (!$hasFlooring) {
                            $uniqueFloorings['Any'] = true;
                            $flooringTypes['Any'] = ($flooringTypes['Any'] ?? 0) + 1;
                        }

                        // PARKING TOTAL
                        $currentParkingTotal = (int) ($record['ParkingTotal'] ?? 0);
                        $parkingTotalKey = $currentParkingTotal >= 4 ? '4+' : (string) $currentParkingTotal;

                        $uniqueParkingTotal[$parkingTotalKey] = true;
                        $parkingTotal[$parkingTotalKey]++;

                        // PARKING TYPES
                        $parkingFeatures = is_array($record['ParkingFeatures'])
                            ? $record['ParkingFeatures']
                            : (empty($record['ParkingFeatures']) ? [] : [$record['ParkingFeatures']]);

                        $garageType = trim($record['GarageType'] ?? 'None');

                        $allParkingTypes = array_merge($parkingFeatures, [$garageType]);

                        foreach ($allParkingTypes as $pt) {
                            $pt = trim($pt);
                            if ($pt !== '') {
                                $uniqueParkingTypes[$pt] = true;
                                $parkingTypes[$pt] = ($parkingTypes[$pt] ?? 0) + 1;
                            }
                        }

                        if (empty($parkingFeatures) && $garageType === 'None') {
                            $uniqueParkingTypes['None'] = true;
                            $parkingTypes['None'] = ($parkingTypes['None'] ?? 0) + 1;
                        }

                        // POOL FEATURES
                        $poolFeatures = is_array($record['PoolFeatures'])
                            ? $record['PoolFeatures']
                            : (empty($record['PoolFeatures']) ? [] : [$record['PoolFeatures']]);

                        foreach ($poolFeatures as $pt) {
                            $pt = trim($pt);
                            if ($pt !== '') {
                                $uniquePoolTypes[$pt] = true;
                                $poolTypes[$pt] = ($poolTypes[$pt] ?? 0) + 1;
                            }
                        }

                        if (empty($poolFeatures)) {
                            $uniquePoolTypes['None'] = true;
                            $poolTypes['None'] = ($poolTypes['None'] ?? 0) + 1;
                        }

                        // AMENITIES
                        $assocAmenities = is_array($record['AssociationAmenities']) ? $record['AssociationAmenities'] : (empty($record['AssociationAmenities']) ? [] : [$record['AssociationAmenities']]);
                        $communityFeatures = is_array($record['CommunityFeatures']) ? $record['CommunityFeatures'] : (empty($record['CommunityFeatures']) ? [] : [$record['CommunityFeatures']]);
                        $laundryFeatures = is_array($record['LaundryFeatures']) ? $record['LaundryFeatures'] : (empty($record['LaundryFeatures']) ? [] : [$record['LaundryFeatures']]);

                        $allAmenities = array_unique(array_merge($assocAmenities, $communityFeatures, $laundryFeatures));

                        foreach ($allAmenities as $am) {
                            $am = trim($am);
                            if ($am !== '') {
                                $uniqueAmenities[$am] = true;
                                $amenities[$am] = ($amenities[$am] ?? 0) + 1;
                            }
                        }

                        // VIEW TYPES
                        $views = is_array($record['View']) ? $record['View'] : (empty($record['View']) ? [] : [$record['View']]);

                        foreach ($views as $v) {
                            $v = trim($v);
                            if ($v !== '') {
                                $uniqueViewTypes[$v] = true;
                                $viewTypes[$v] = ($viewTypes[$v] ?? 0) + 1;
                            }
                        }

                        if (empty($views)) {
                            $uniqueViewTypes['Clear'] = true;
                            $viewTypes['Clear'] = ($viewTypes['Clear'] ?? 0) + 1;
                        }
                    }

                    $propertiesFetched += count($records);

                    if (count($records) < $pageSize || $propertiesFetched >= $maxPropertiesToFetch) {
                        Log::info('Stopping count fetch', [
                            'propertiesFetched' => $propertiesFetched,
                            'maxPropertiesToFetch' => $maxPropertiesToFetch,
                            'recordsInLastPage' => count($records)
                        ]);
                        break;
                    }

                    $skip += $pageSize;
                }

                // FINAL OPTIONS
                $flooringOptions = array_keys($uniqueFloorings);
                $parkingTotalOptions = array_filter(['0', '1', '2', '3', '4+'], fn($space) => isset($uniqueParkingTotal[$space]));

                return [
                    'cityCounts' => $cityCounts,
                    'propertySubTypeCounts' => $propertySubTypeCounts,
                    'totalActiveProperties' => $totalActiveProperties,

                    'flooringTypes' => $flooringTypes,
                    'parkingTotal' => $parkingTotal,
                    'parkingTypes' => $parkingTypes,
                    'poolTypes' => $poolTypes,
                    'amenities' => $amenities,
                    'viewTypes' => $viewTypes,

                    'flooringOptions' => $flooringOptions,
                    'parkingTotalOptions' => $parkingTotalOptions,
                    'parkingTypeOptions' => array_keys($uniqueParkingTypes),
                    'poolTypeOptions' => array_keys($uniquePoolTypes),
                    'amenityOptions' => array_keys($uniqueAmenities),
                    'viewTypeOptions' => array_keys($uniqueViewTypes),
                ];

            } catch (\Exception $e) {
                Log::error('Count fetch error: ' . $e->getMessage(), ['filter' => $filter]);
                return [];
            }
        });
    }

    private function fetchPropertiesWithOptimizedPagination($filter, $batchSize = 50, $page = 1, $perPage = null, $transactionType = null, Request $request = null)
    {
        $perPage = $perPage ?? $this->propertiesPerPage;
        $httpClient = $this->getHttpClient();
        $skip = ($page - 1) * $perPage;

        Log::info("Starting server-side pagination for properties", [
            'page' => $page,
            'skip' => $skip,
            'perPage' => $perPage,
            'filter' => $filter,
        ]);

        $fetchLimit = $perPage * 3;
        $maxSkip = $skip;
        $allValidProperties = [];
        $currentSkip = $skip;
        $loopCount = 0;
        $maxLoops = 100;
        $hasComplexFilters = $this->hasComplexFilters($request ?? new Request());
        $endOfStream = false;

        if ($hasComplexFilters) {
            $currentSkip = 0;
            $propertiesToSkip = ($page - 1) * $perPage;
            $skippedCount = 0;
        } else {
            $propertiesToSkip = 0;
            $skippedCount = 0;
        }

        while (count($allValidProperties) < $perPage && $currentSkip < 50000) {
            $loopCount++;
            if ($loopCount > $maxLoops) {
                Log::warning("Pagination loop limit reached", ['page' => $page, 'collected' => count($allValidProperties)]);
                break;
            }

            $remainingToFetch = $perPage - count($allValidProperties);

            if ($hasComplexFilters) {
                $topToFetch = 500;
            } else {
                $topToFetch = min($remainingToFetch * 2, $fetchLimit);
            }

            Log::info('API CALL: fetchPropertiesWithOptimizedPagination - Fetching properties page', [
                'filter' => $filter,
                'top' => $topToFetch,
                'skip' => $currentSkip,
                'page' => $page,
                'perPage' => $perPage,
                'loop' => $loopCount,
                'propertiesToSkip' => $propertiesToSkip,
                'skippedSoFar' => $skippedCount
            ]);

            $query = [
                '$filter' => $filter,
                '$orderby' => 'ModificationTimestamp desc',
                '$top' => $topToFetch,
                '$skip' => $currentSkip,
            ];

            $response = $httpClient->get($this->baseUrl . 'Property', $query);

            if (!$response->successful()) {
                Log::error("Failed to fetch properties page", ['status' => $response->status(), 'page' => $page, 'skip' => $currentSkip]);
                break;
            }

            $properties = $response->json()['value'] ?? [];
            if (empty($properties)) {
                $endOfStream = true;
                break;
            }

            if (preg_match_all("/PropertySubType eq '([^']+)'/", $filter, $matches)) {
                $requestedSubTypes = $matches[1];
                $properties = array_filter($properties, function ($prop) use ($requestedSubTypes) {
                    $propSubType = strtolower($prop['PropertySubType'] ?? '');
                    return in_array($propSubType, array_map('strtolower', $requestedSubTypes));
                });
            }

            $properties = array_filter($properties, function ($prop) use ($transactionType) {
                return !$this->isPropertyExcluded($prop['PropertyType'] ?? '', $prop['PropertySubType'] ?? '', $transactionType);
            });

            $properties = $this->applyComplexFilters($properties, $request ?? new Request());

            if (empty($properties)) {
                $currentSkip += $topToFetch;
                continue;
            }

            if ($hasComplexFilters) {
                $countInBatch = count($properties);
                $skippedCount += $countInBatch;

                if ($skippedCount <= $propertiesToSkip) {
                    $currentSkip += $topToFetch;
                    continue;
                }

                $startIndex = $propertiesToSkip - ($skippedCount - $countInBatch);
                if ($startIndex > 0) {
                    $properties = array_slice($properties, $startIndex);
                }
            }

            if (count($allValidProperties) < $perPage) {
                $listingKeys = array_column($properties, 'ListingKey');
                $mediaMap = $this->fetchMedia($listingKeys, 1);

                $processedProperties = $this->processProperties($properties, $mediaMap);

                $allValidProperties = array_merge($allValidProperties, array_values($processedProperties));

                if (count($allValidProperties) >= $perPage) {
                    $allValidProperties = array_slice($allValidProperties, 0, $perPage);
                    break;
                }
            }

            $currentSkip += $topToFetch;

            if (count($response->json()['value'] ?? []) < $topToFetch) {
                $endOfStream = true;
                break;
            }
        }

        $countResponse = $httpClient->get($this->baseUrl . 'Property', [
            '$filter' => $filter,
            '$count' => 'true',
            '$top' => 0,
        ]);

        $totalCount = 0;
        if ($countResponse->successful()) {
            $totalCount = $countResponse->json()['@odata.count'] ?? 0;
        }

        $actualFilteredCount = $totalCount;

        if ($hasComplexFilters) {
            if ($endOfStream) {
                $actualFilteredCount = $skippedCount;
                Log::info("Exact filtered count found (End of Stream)", ['count' => $actualFilteredCount]);
            } else {
                $sampleSize = min(500, $totalCount);
                $sampleResponse = $httpClient->get($this->baseUrl . 'Property', [
                    '$filter' => $filter,
                    '$orderby' => 'ModificationTimestamp desc',
                    '$top' => $sampleSize,
                    '$skip' => 0,
                    '$select' => 'ListingKey,PropertyType,PropertySubType,InteriorFeatures,ParkingTotal,ParkingFeatures,GarageType,PoolFeatures,AssociationAmenities,CommunityFeatures,LaundryFeatures,View',
                ]);

                if ($sampleResponse->successful()) {
                    $sampleProperties = $sampleResponse->json()['value'] ?? [];
                    $filteredSample = $this->applyComplexFilters($sampleProperties, $request ?? new Request());

                    if (preg_match_all("/PropertySubType eq '([^']+)'/", $filter, $matches)) {
                        $requestedSubTypes = $matches[1];
                        $filteredSample = array_filter($filteredSample, function ($prop) use ($requestedSubTypes) {
                            $propSubType = strtolower($prop['PropertySubType'] ?? '');
                            return in_array($propSubType, array_map('strtolower', $requestedSubTypes));
                        });
                    }

                    $filteredSample = array_filter($filteredSample, function ($prop) use ($transactionType) {
                        return !$this->isPropertyExcluded($prop['PropertyType'] ?? '', $prop['PropertySubType'] ?? '', $transactionType);
                    });

                    $sampleFilteredCount = count($filteredSample);
                    $sampleTotalCount = count($sampleProperties);
                    if ($sampleTotalCount > 0) {
                        $filterRatio = $sampleFilteredCount / $sampleTotalCount;
                        $actualFilteredCount = (int) round($totalCount * $filterRatio);
                    } else {
                        $actualFilteredCount = 0;
                    }

                    Log::info("Estimated filtered count", ['estimate' => $actualFilteredCount, 'ratio' => $filterRatio ?? 0]);
                }
            }
        }

        if (empty($allValidProperties)) {
            return [
                'properties' => [],
                'clusteredProperties' => [],
                'totalCount' => $totalCount,
                'filteredCount' => 0,
                'estimatedTotalWithMedia' => $totalCount,
                'actualFilteredCount' => $actualFilteredCount,
                'hasMorePages' => false,
            ];
        }

        $clusteredProperties = $this->createAddressBasedClusters($allValidProperties);
        $filteredCount = count($allValidProperties);

        if ($hasComplexFilters) {
            $hasMorePages = ($page * $perPage) < $actualFilteredCount;
        } else {
            $hasMorePages = count($allValidProperties) === $perPage && $currentSkip < 50000;
        }

        Log::info("Pagination result", [
            'page' => $page,
            'propertiesOnPage' => $filteredCount,
            'totalCount' => $totalCount,
            'actualFilteredCount' => $actualFilteredCount,
            'hasComplexFilters' => $hasComplexFilters,
            'hasMorePages' => $hasMorePages,
            'finalSkip' => $currentSkip,
        ]);

        return [
            'properties' => $allValidProperties,
            'clusteredProperties' => array_values($clusteredProperties),
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
            'estimatedTotalWithMedia' => $totalCount,
            'actualFilteredCount' => $actualFilteredCount,
            'hasMorePages' => $hasMorePages,
        ];
    }

      private function fetchRelatedProperties($currentProperty, $limit = 20)
    {
        $httpClient = $this->getHttpClient();

        $city = addslashes($currentProperty['City']);

        $currentTransactionType = trim($currentProperty['TransactionType'] ?? '');
        $currentListPriceRaw = $currentProperty['ListPrice'] ?? 0;
        // Remove commas if present and cast to float
        $currentListPrice = floatval(str_replace(',', '', (string) $currentListPriceRaw));

        Log::info('Determining min price', [
            'original_type' => $currentProperty['TransactionType'] ?? 'null',
            'trimmed_type' => $currentTransactionType,
            'original_price' => $currentListPriceRaw,
            'parsed_price' => $currentListPrice
        ]);

        if ($currentTransactionType === 'For Sale' && $currentListPrice > 25000) {
            $minPrice = 25000;
        } else {
            $minPrice = 500;
        }

        $filterParts = [
            "City eq '$city'",
            "ListPrice ge $minPrice",
            "StandardStatus eq 'Active'",
            "ListingKey ne '" . addslashes($currentProperty['ListingKey']) . "'",
        ];

        if (in_array($currentTransactionType, ['For Lease', 'For Sub-Lease'])) {
            $filterParts[] = "(TransactionType eq 'For Lease' or TransactionType eq 'For Sub-Lease')";
        } else {
            $filterParts[] = "TransactionType eq '" . addslashes($currentTransactionType) . "'";
        }

        $filter = implode(' and ', $filterParts);

        Log::info('Fetching related properties with filter: ' . $filter);

        $response = $httpClient->get($this->baseUrl . 'Property', [
            '$filter' => $filter,
            '$select' => 'ListingKey,StreetNumber,StreetName,StreetSuffix,City,StateOrProvince,PostalCode,ListPrice,TransactionType,BedroomsTotal,BathroomsTotalInteger,LivingAreaRange,DaysOnMarket,ListingContractDate,PropertySubType,LotSizeArea,Latitude,Longitude,PublicRemarks,PropertyType,YearBuilt,InteriorFeatures,ParkingTotal,ParkingFeatures,GarageType,PoolFeatures,AssociationAmenities,CommunityFeatures,LaundryFeatures,View,UnparsedAddress,UnitNumber',
            '$orderby' => 'ModificationTimestamp desc',
            '$top' => 500,
        ]);

        if ($response->successful()) {
            $properties = $response->json()['value'] ?? [];
            Log::info('Fetched ' . count($properties) . ' potential related properties from ' . $city . ' with min price $' . $minPrice . ' for ' . $currentProperty['TransactionType']);

            if (empty($properties)) {
                return [];
            }

            $listingKeys = array_column($properties, 'ListingKey');
            $propertiesWithMedia = [];
            $mediaMap = [];
            $batchSize = 20;
            $listingKeysBatches = array_chunk($listingKeys, $batchSize);

            foreach ($listingKeysBatches as $batchKeys) {
                $batchMediaMap = $this->fetchMedia($batchKeys, 1);

                if (empty($batchMediaMap)) {
                    continue;
                }

                // Add to main media map to ensure it's available for processProperties
                $mediaMap = $mediaMap + $batchMediaMap;

                // Find properties in this batch that match the fetched media
                foreach ($properties as $property) {
                    $listingKey = $property['ListingKey'];
                    // Check if this property is in the current batch and has media
                    if (in_array($listingKey, $batchKeys) && !empty($batchMediaMap[$listingKey]) && !empty($batchMediaMap[$listingKey][0])) {
                        $propertiesWithMedia[] = $property;
                    }
                }

                if (count($propertiesWithMedia) >= $limit) {
                    break;
                }
            }

            $processedProperties = $this->processProperties(array_slice($propertiesWithMedia, 0, $limit), $mediaMap);

            Log::info('Returning ' . count($processedProperties) . ' related properties with media from ' . $city . ' for ' . $currentProperty['TransactionType']);

            return $processedProperties;
        } else {
            Log::warning('Failed to fetch related properties: ' . $response->status(), [
                'response' => $response->body(),
                'filter' => $filter,
            ]);
            return [];
        }
    }

    private function fetchSingleProperty($listingKey, $transactionType = null, $status = 'Active')
    {
        $httpClient = $this->getHttpClient();

        $filterParts = ["ListingKey eq '$listingKey'", "StandardStatus eq '$status'"];

        if ($transactionType) {
            $filterParts[] = "TransactionType eq '$transactionType'";
        }

        $filter = implode(' and ', $filterParts);

        $response = $httpClient->get($this->baseUrl . 'Property', [
            '$filter' => $filter,
            '$select' => 'ListingKey,StreetNumber,StreetName,StreetSuffix,City,StateOrProvince,PostalCode,ListPrice,TransactionType,BedroomsTotal,BathroomsTotalInteger,LivingAreaRange,LivingAreaRange,DaysOnMarket,ListingContractDate,PropertySubType,LotSizeArea,Latitude,Longitude,PublicRemarks,PropertyType,YearBuilt,InteriorFeatures,ParkingTotal,ParkingFeatures,GarageType,PoolFeatures,AssociationAmenities,CommunityFeatures,LaundryFeatures,View',
            '$top' => 1,
        ]);

        if ($response->successful() && !empty($response->json()['value'])) {
            $property = $response->json()['value'][0];
            $mediaMap = $this->fetchMedia([$property['ListingKey']], 20);
            $processedProperties = $this->processProperties([$property], $mediaMap);
            $property = $processedProperties[0];
            $relatedProperties = $this->fetchRelatedProperties($property, 20);
            $property['relatedProperties'] = $relatedProperties;

            if (is_null($property['Latitude']) || is_null($property['Longitude']) || $property['Latitude'] == 0 || $property['Longitude'] == 0) {
                $street = trim(($property['StreetNumber'] ?? '') . ' ' .
                    ($property['StreetName'] ?? '') . ' ' .
                    ($property['StreetSuffix'] ?? ''));
                $fullAddress = $street . ', ' . $property['City'] . ', ' .
                    $property['StateOrProvince'] . ' ' . ($property['PostalCode'] ?? '');

                $coords = $this->geocodeAddress($fullAddress);
                if ($coords && $coords['latitude'] != 0 && $coords['longitude'] != 0) {
                    $property['Latitude'] = $coords['latitude'];
                    $property['Longitude'] = $coords['longitude'];
                    Log::info('Geocoded coordinates for property: ' . $listingKey, $coords);
                } else {
                    $simpleAddress = trim($street . ', ' . $property['City'] . ', Ontario, Canada');
                    $coords = $this->geocodeAddress($simpleAddress);
                    if ($coords && $coords['latitude'] != 0 && $coords['longitude'] != 0) {
                        $property['Latitude'] = $coords['latitude'];
                        $property['Longitude'] = $coords['longitude'];
                        Log::info('Geocoded coordinates using simplified address for property: ' . $listingKey, $coords);
                    } elseif (isset($this->getCityCoordinates()[$property['City']])) {
                        $cityCoords = $this->getCityCoordinates()[$property['City']];
                        $randomOffsetLat = (rand(-50, 50) / 10000);
                        $randomOffsetLng = (rand(-50, 50) / 10000);
                        $property['Latitude'] = $cityCoords['latitude'] + $randomOffsetLat;
                        $property['Longitude'] = $cityCoords['longitude'] + $randomOffsetLng;
                        Log::info('Using randomized city coordinates for property: ' . $listingKey, [
                            'lat' => $property['Latitude'],
                            'lng' => $property['Longitude'],
                            'city' => $property['City'],
                        ]);
                    } else {
                        $property['Latitude'] = 43.6532;
                        $property['Longitude'] = -79.3832;
                        Log::warning('Using default Toronto coordinates for property: ' . $listingKey);
                    }
                }
            }

            Log::info('Fetched single property with related properties:', [
                'ListingKey' => $property['ListingKey'],
                'MediaURL' => $property['MediaURL'] ?? 'null',
                'MediaURLs' => $property['MediaURLs'],
                'Latitude' => $property['Latitude'],
                'Longitude' => $property['Longitude'],
                'City' => $property['City'],
                'TransactionType' => $property['TransactionType'],
                'RelatedPropertiesCount' => count($relatedProperties),
                'PropertyPrice' => $property['FormattedPrice'],
            ]);

            return $property;
        } else {
            Log::warning('No property found for ListingKey: ' . $listingKey, [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return null;
        }
    }

    public function getAvailableTimeSlots(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
            ]);

            $date = $request->date;

            $availableSlotBookings = SlotBooking::where('date', $date)
                ->where('is_booked', false)
                ->with('slot')
                ->get();

            $availableSlots = $availableSlotBookings->map(function ($booking) {
                return [
                    'id' => $booking->slot->id,
                    'value' => $booking->id,
                    'text' => $booking->slot->name . ' (' . $booking->slot->start_time . ' - ' . $booking->slot->end_time . ')',
                    'slot_booking_id' => $booking->id,
                ];
            })->unique('id');

            return response()->json([
                'success' => true,
                'timeSlots' => $availableSlots->values(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching available time slots: ' . $e->getMessage(), $request->all());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available time slots.',
            ], 500);
        }
    }

    public function storeTourBooking(Request $request)
    {
        try {
            $validated = $request->validate([
                'listing_key' => 'required|string',
                'transaction_type' => 'required|string',
                'date' => 'required|date|after_or_equal:today',
                'slot_booking_id' => 'required|integer|exists:slot_bookings,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'message' => 'nullable|string',
                'consent' => 'required|accepted',
            ]);

            Log::info('Validation passed', $validated);

            $slotBooking = SlotBooking::findOrFail($validated['slot_booking_id']);
            dd($slotBooking);

            Log::info('Slot booking found', $slotBooking->toArray());

            if ($slotBooking->is_booked) {
                Log::warning('Slot already booked', ['id' => $slotBooking->id]);
                return back()->withErrors(['slot_booking_id' => 'This time slot is no longer available.'])->withInput();
            }

            $tourBooking = TourBooking::create([
                'user_id' => Auth::user()->id,
                'listing_key' => $validated['listing_key'],
                'transaction_type' => $validated['transaction_type'],
                'date' => $validated['date'],
                'slot_booking_id' => $slotBooking->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'message' => $request->message,
                'consent' => true,
            ]);

            Log::info('Tour booking created', ['tour_booking' => $tourBooking->toArray()]);

            $slotBooking->update(['is_booked' => true]);

            Log::info('Slot booking updated', ['id' => $slotBooking->id, 'is_booked' => true]);

            $tourBooking->load(['slotBooking.slot']);

            Mail::to(env('MAIL_FROM_ADDRESS'))->send(new TourBookingMail($tourBooking, true));
            Mail::to($tourBooking->email)->send(new TourBookingMail($tourBooking, false));

            ToastMagic::success('Tour request scheduled successfully! We will contact you soon.');

            return redirect()->back();
        } catch (\Throwable $e) {
            Log::error('Error in storeTourBooking', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            ToastMagic::error('Failed to schedule tour. Please try again.');

            return back();
        }
    }


    public function sell(Request $request, $search = 'Mississauga')
    {
        try {
            $httpClient = $this->getHttpClient();

            // Use the route parameter for the searched city
            $searchedCity = $search;

            // Filter by the searched city only
            $citiesFilter = "City eq '" . addslashes($searchedCity) . "'";
            $statusFilter = "StandardStatus eq 'Closed'";
            $filter = "$citiesFilter and $statusFilter";

            $response = $httpClient->get($this->baseUrl . 'Property', [
                '$filter' => $filter,
                '$select' => 'ListingKey,City,ClosePrice,ListPrice,BedroomsTotal,BathroomsTotalInteger,LivingAreaRange,PropertySubType,UnparsedAddress,StreetNumber,StreetName,StreetSuffix,PostalCode,DaysOnMarket',
                '$orderby' => 'CloseDate desc',
                '$top' => 150,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to fetch closed properties', ['status' => $response->status()]);
                return view('frontend.sell', ['soldProperties' => [], 'gtaCities' => $this->getGtaCities(), 'searchedCity' => $searchedCity]);
            }

            $properties = $response->json()['value'] ?? [];

            if (empty($properties)) {
                return view('frontend.sell', ['soldProperties' => [], 'gtaCities' => $this->getGtaCities(), 'searchedCity' => $searchedCity]);
            }

            $listingKeys = array_column($properties, 'ListingKey');

            $mediaMap = [];
            $batchSize = 100;
            for ($i = 0; $i < count($listingKeys); $i += $batchSize) {
                $batchKeys = array_slice($listingKeys, $i, $batchSize);
                $batchMedia = $this->fetchMedia($batchKeys, 1);
                $mediaMap = array_merge($mediaMap, $batchMedia);
            }

            Log::info('Fetched media for ' . count($mediaMap) . ' sold properties across batches');

            $displayProperties = [];
            foreach ($properties as $property) {
                if (count($displayProperties) >= 20) {
                    break;
                }

                $listingKey = $property['ListingKey'];
                if (empty($mediaMap[$listingKey])) {
                    continue;
                }

                $mediaUrl = $mediaMap[$listingKey][0] ?? null;
                if (!$mediaUrl) {
                    continue;
                }

                $soldPrice = $property['ClosePrice'] ?? $property['ListPrice'] ?? 0;
                $address = trim(($property['StreetNumber'] ?? '') . ' ' .
                    ($property['StreetName'] ?? '') . ' ' .
                    ($property['StreetSuffix'] ?? ''));
                $FullAddress = $this->formatPropertyAddress($property);
                $location = $property['City'] . ', Ontario' . ($property['PostalCode'] ? ' ' . $property['PostalCode'] : '');
                $daysOnMarket = $property['DaysOnMarket'] ?? 0;
                $daysText = $this->formatDaysOnMarket($daysOnMarket);

                $displayProperties[] = [
                    'ListingKey' => $property['ListingKey'] ?? '',
                    'TransactionType' => 'For Sale',
                    'image' => $mediaUrl,
                    'price' => '$' . number_format($soldPrice),
                    'address' => $address ?: 'Address Not Available',
                    'location' => $location,
                    'bedrooms' => (int) ($property['BedroomsTotal'] ?? 0),
                    'bathrooms' => (int) ($property['BathroomsTotalInteger'] ?? 0),
                    'area' => ($property['LivingAreaRange'] ?? 'N/A') . ' sq ft (Living Area)',
                    'daysOnMarket' => $daysText,
                    'FullAddress' => $FullAddress,
                ];
            }

            Log::info('Displaying ' . count($displayProperties) . ' recently sold properties for ' . $searchedCity);

            return view('frontend.sell', [
                'soldProperties' => $displayProperties,
                'gtaCities' => $this->getGtaCities(),
                'searchedCity' => $searchedCity,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in sell method: ' . $e->getMessage(), ['exception' => $e]);

            return view('frontend.sell', [
                'soldProperties' => [],
                'gtaCities' => $this->getGtaCities(),
                'searchedCity' => $search,
            ]);
        }
    }

    private function formatDaysOnMarket($days)
    {
        if ($days == 1) {
            return '1 Day';
        }
        if ($days <= 7) {
            return $days . ' Days';
        }
        if ($days <= 30) {
            return ceil($days / 7) . ' Week' . (ceil($days / 7) > 1 ? 's' : '');
        }
        if ($days <= 365) {
            return ceil($days / 30) . ' Month' . (ceil($days / 30) > 1 ? 's' : '');
        }
        return ceil($days / 365) . ' Year' . (ceil($days / 365) > 1 ? 's' : '');
    }

    public function sellStore(Request $request)
    {
        $request->validate([
            'sell_property_address' => 'required',
            'sell_property_type' => 'required',
            'sell_property_sqft' => 'required|numeric',
            'sell_property_bedrooms' => 'required|numeric',
            'sell_property_bathrooms' => 'required|numeric',
            'sell_property_condition' => 'nullable',
            'sell_property_relocating' => 'nullable',
            'house_construct_year' => 'nullable|numeric',
            'sell_property_service' => 'nullable',
            'sell_property_mortgage_balance' => 'nullable',
            'sell_property_user_name' => 'required',
            'sell_property_user_email' => 'required|email',
            'sell_property_user_phone' => 'required',
            'files.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $sellingRequest = SellingRequest::create([
            'sell_property_address' => $request->sell_property_address,
            'sell_property_type' => $request->sell_property_type,
            'sell_property_sqft' => $request->sell_property_sqft,
            'sell_property_bedrooms' => $request->sell_property_bedrooms,
            'sell_property_bathrooms' => $request->sell_property_bathrooms,
            'sell_property_condition' => $request->sell_property_condition,
            'sell_property_relocating' => $request->sell_property_relocating,
            'house_construct_year' => $request->house_construct_year,
            'sell_property_service' => $request->sell_property_service,
            'sell_property_mortgage_balance' => $request->sell_property_mortgage_balance,
            'sell_property_user_name' => $request->sell_property_user_name,
            'sell_property_user_email' => $request->sell_property_user_email,
            'sell_property_user_phone' => $request->sell_property_user_phone,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('selling-request-images', 'public');
                SellingRequestImage::create([
                    'selling_request_id' => $sellingRequest->id,
                    'image_path' => $path,
                ]);
            }
        }

        ToastMagic::success('Your request has been submitted successfully!');

        return back();
    }

    private function applyComplexFilters($properties, Request $request)
    {
        if (empty($properties)) {
            return $properties;
        }

        $initialCount = count($properties);
        $filterStats = [
            'flooring' => ['checked' => 0, 'excluded' => 0],
            'parking_type' => ['checked' => 0, 'excluded' => 0],
            'pool_type' => ['checked' => 0, 'excluded' => 0],
            'amenities' => ['checked' => 0, 'excluded' => 0],
            'view_type' => ['checked' => 0, 'excluded' => 0],
            'area' => ['checked' => 0, 'excluded' => 0],
        ];

        $filtered = array_filter($properties, function ($property) use ($request, &$filterStats) {
            if (($request->has('flooring') && !empty($request->flooring)) || ($request->has('InteriorFeatures') && !empty($request->InteriorFeatures))) {
                $filterStats['flooring']['checked']++;
                $floorings = $request->has('InteriorFeatures') ? array_unique((array) $request->input('InteriorFeatures')) : array_unique((array) $request->input('flooring'));
                $interiorFeatures = is_array($property['InteriorFeatures']) ? $property['InteriorFeatures'] : (empty($property['InteriorFeatures']) ? [] : [$property['InteriorFeatures']]);
                $hasMatchingFlooring = false;
                foreach ($floorings as $flooring) {
                    foreach ($interiorFeatures as $feature) {
                        if (stripos($feature, $flooring) !== false) {
                            $hasMatchingFlooring = true;
                            break;
                        }
                    }
                    if ($hasMatchingFlooring) {
                        break;
                    }
                }
                if (!$hasMatchingFlooring) {
                    $filterStats['flooring']['excluded']++;
                    return false;
                }
            }

            if ($request->has('parking_type') && !empty($request->parking_type)) {
                $filterStats['parking_type']['checked']++;
                $parkingTypes = array_unique((array) $request->input('parking_type'));
                $parkingFeatures = is_array($property['ParkingFeatures']) ? $property['ParkingFeatures'] : (empty($property['ParkingFeatures']) ? [] : [$property['ParkingFeatures']]);
                $garageType = trim($property['GarageType'] ?? '');
                $hasMatchingParking = false;
                foreach ($parkingTypes as $type) {
                    foreach ($parkingFeatures as $feature) {
                        if (stripos($feature, $type) !== false) {
                            $hasMatchingParking = true;
                            break;
                        }
                    }
                    if (stripos($garageType, $type) !== false) {
                        $hasMatchingParking = true;
                    }
                    if ($hasMatchingParking) {
                        break;
                    }
                }
                if (!$hasMatchingParking) {
                    $filterStats['parking_type']['excluded']++;
                    return false;
                }
            }

            if ($request->has('pool_type') && !empty($request->pool_type)) {
                $filterStats['pool_type']['checked']++;
                $poolTypes = array_unique((array) $request->input('pool_type'));
                $poolFeatures = is_array($property['PoolFeatures']) ? $property['PoolFeatures'] : (empty($property['PoolFeatures']) ? [] : [$property['PoolFeatures']]);
                $hasMatchingPool = false;
                if (in_array('None', $poolTypes) && count($poolTypes) === 1) {
                    if (empty($poolFeatures)) {
                        $hasMatchingPool = true;
                    }
                } else {
                    $specificPoolTypes = array_filter($poolTypes, fn($p) => $p !== 'None');
                    foreach ($specificPoolTypes as $poolType) {
                        foreach ($poolFeatures as $feature) {
                            if (stripos($feature, $poolType) !== false) {
                                $hasMatchingPool = true;
                                break;
                            }
                        }
                        if ($hasMatchingPool) {
                            break;
                        }
                    }
                }
                if (!$hasMatchingPool) {
                    $filterStats['pool_type']['excluded']++;
                    return false;
                }
            }

            if ($request->has('amenities') && !empty($request->amenities)) {
                $filterStats['amenities']['checked']++;
                $amenities = array_slice(array_unique((array) $request->input('amenities')), 0, 3);
                $assocAmenities = is_array($property['AssociationAmenities']) ? $property['AssociationAmenities'] : (empty($property['AssociationAmenities']) ? [] : [$property['AssociationAmenities']]);
                $communityFeatures = is_array($property['CommunityFeatures']) ? $property['CommunityFeatures'] : (empty($property['CommunityFeatures']) ? [] : [$property['CommunityFeatures']]);
                $laundryFeatures = is_array($property['LaundryFeatures']) ? $property['LaundryFeatures'] : (empty($property['LaundryFeatures']) ? [] : [$property['LaundryFeatures']]);
                $allAmenities = array_unique(array_merge($assocAmenities, $communityFeatures, $laundryFeatures));
                $hasMatchingAmenity = false;
                foreach ($amenities as $amenity) {
                    foreach ($allAmenities as $feature) {
                        if (stripos($feature, $amenity) !== false) {
                            $hasMatchingAmenity = true;
                            break;
                        }
                    }
                    if ($hasMatchingAmenity) {
                        break;
                    }
                }
                if (!$hasMatchingAmenity) {
                    $filterStats['amenities']['excluded']++;
                    return false;
                }
            }

            if ($request->has('view_type') && !empty($request->view_type)) {
                $filterStats['view_type']['checked']++;
                $viewTypes = array_slice(array_unique((array) $request->input('view_type')), 0, 3);
                $views = is_array($property['View']) ? $property['View'] : (empty($property['View']) ? [] : [$property['View']]);
                $hasMatchingView = false;
                foreach ($viewTypes as $viewType) {
                    foreach ($views as $view) {
                        if (stripos($view, $viewType) !== false) {
                            $hasMatchingView = true;
                            break;
                        }
                    }
                    if ($hasMatchingView) {
                        break;
                    }
                }
                if (!$hasMatchingView) {
                    $filterStats['view_type']['excluded']++;
                    return false;
                }
            }

            $minArea = $request->filled('adv_min_area') ? (int) $request->adv_min_area :
                ($request->filled('min_area') ? (int) $request->min_area : null);
            $maxArea = $request->filled('adv_max_area') ? (int) $request->adv_max_area :
                ($request->filled('max_area') ? (int) $request->max_area : null);

            if ($minArea !== null || $maxArea !== null) {
                $filterStats['area']['checked']++;
                $areaRange = $property['LivingAreaRange'] ?? null;

                // Handle different LivingAreaRange formats
                $propMin = 0;
                $propMax = PHP_INT_MAX;

                if ($areaRange) {
                    $areaRange = trim($areaRange);
                    if (strpos($areaRange, '-') !== false) {
                        // Format like "3500-5000"
                        [$minPart, $maxPart] = explode('-', $areaRange, 2);
                        $propMin = (int) trim($minPart);
                        $propMax = (int) trim($maxPart);
                    } elseif (strpos($areaRange, '+') !== false) {
                        // Format like "3500+"
                        $propMin = (int) rtrim($areaRange, '+ ');
                        $propMax = PHP_INT_MAX;
                    } elseif (is_numeric($areaRange)) {
                        // Single number like "5000"
                        $propMin = $propMax = (int) $areaRange;
                    }
                }

                // Check if property area overlaps with filter range
                $effectiveMinArea = $minArea ?? 0;
                $effectiveMaxArea = $maxArea ?? PHP_INT_MAX;

                // Property overlaps if its range intersects with filter range
                $overlaps = !($propMax < $effectiveMinArea || $propMin > $effectiveMaxArea);

                if (!$overlaps) {
                    $filterStats['area']['excluded']++;
                    return false;
                }
            }

            return true;
        });

        $finalCount = count($filtered);

        if ($this->hasComplexFilters($request)) {
            $activeFilters = array_filter($filterStats, fn($stats) => $stats['checked'] > 0);
            if (!empty($activeFilters)) {
                Log::info("Complex filters applied - Summary", [
                    'initialCount' => $initialCount,
                    'finalCount' => $finalCount,
                    'excluded' => $initialCount - $finalCount,
                    'filterStats' => $activeFilters
                ]);
            }
        }

        return $filtered;
    }

    private function hasComplexFilters(Request $request)
    {
        $complexFilterKeys = ['InteriorFeatures', 'parking_type', 'pool_type', 'amenities', 'view_type'];
        foreach ($complexFilterKeys as $key) {
            if ($request->has($key) && !empty($request->input($key))) {
                return true;
            }
        }

        return false;
    }

    private function processProperties($properties, $mediaMap)
    {
        $subtypeMapping = [
            'Att/Row/Townhouse' => 'Freehold Townhouse',
        ];

        return array_map(function ($property) use ($mediaMap, $subtypeMapping) {
            $daysOnMarket = $property['DaysOnMarket'] ?? null;
            if (is_null($daysOnMarket) && !empty($property['ListingContractDate'])) {
                $daysOnMarket = Carbon::parse($property['ListingContractDate'])->diffInDays(Carbon::now());
            }

            // Determine PropertySubType first (before using it)
            $propertySubType = trim($property['PropertySubType'] ?? 'N/A');
            $propertySubType = $subtypeMapping[$propertySubType] ?? $propertySubType;

            // Custom address formatting for specific property subtypes
            $subtypesWithUnitNumber = ['Freehold Townhouse', 'Condo Townhouse', 'Condo Apartment'];

            if (in_array($propertySubType, $subtypesWithUnitNumber)) {
                // Format: UnitNumber-StreetNumber StreetName StreetSuffix, City, ON PostalCode
                $unitNumber = trim($property['UnitNumber'] ?? '');
                $streetNumber = trim($property['StreetNumber'] ?? '');
                $streetName = trim($property['StreetName'] ?? '');
                $streetSuffix = trim($property['StreetSuffix'] ?? '');
                $city = trim($property['City'] ?? '');
                $postalCode = trim($property['PostalCode'] ?? '');

                // Build street address with unit number
                $streetPart = '';
                if ($unitNumber && $streetNumber) {
                    $streetPart = $unitNumber . '-' . $streetNumber;
                } elseif ($streetNumber) {
                    $streetPart = $streetNumber;
                }

                if ($streetName) {
                    $streetPart .= ($streetPart ? ' ' : '') . $streetName;
                }

                if ($streetSuffix) {
                    $streetPart .= ($streetPart ? ' ' : '') . $streetSuffix;
                }

                // Build full address
                $fullAddress = $streetPart;
                if ($city) {
                    $fullAddress .= ($fullAddress ? ', ' : '') . $city . ', ON';
                }
                if ($postalCode) {
                    $fullAddress .= ($fullAddress ? ' ' : '') . $postalCode;
                }
            } else {
                // Use existing UnparsedAddress or fallback for other subtypes
                $street = trim(($property['StreetNumber'] ?? '') . ' ' .
                    ($property['StreetName'] ?? '') . ' ' .
                    ($property['StreetSuffix'] ?? ''));
                $fullAddress = $property['UnparsedAddress'] ??
                    $street . ', ' . $property['City'] . ', ' .
                    $property['StateOrProvince'] . ' ' . ($property['PostalCode'] ?? '');
            }

            $mediaUrls = $mediaMap[$property['ListingKey']] ?? [];
            $defaultImage = asset('dummy.png');
            $validMediaUrls = array_filter($mediaUrls, function ($url) use ($defaultImage) {
                return !empty($url) &&
                    $url !== $defaultImage &&
                    filter_var($url, FILTER_VALIDATE_URL);
            });

            if (empty($validMediaUrls)) {
                $mediaUrls = [];
                $hasValidMedia = false;
            } else {
                $mediaUrls = array_values($validMediaUrls);
                $hasValidMedia = true;
            }

            $mediaUrl = $mediaUrls[0] ?? null;

            $latitude = isset($property['Latitude']) && is_numeric($property['Latitude']) && $property['Latitude'] !== 0 ? (float) $property['Latitude'] : null;
            $longitude = isset($property['Longitude']) && is_numeric($property['Longitude']) && $property['Longitude'] !== 0 ? (float) $property['Longitude'] : null;

            if (is_null($latitude) || is_null($longitude)) {
                if (isset($this->getCityCoordinates()[$property['City']])) {
                    $cityCoords = $this->getCityCoordinates()[$property['City']];
                    $latitude = $cityCoords['latitude'] + (rand(-100, 100) / 10000);
                    $longitude = $cityCoords['longitude'] + (rand(-100, 100) / 10000);
                } else {
                    $latitude = null;
                    $longitude = null;
                }
            }

            $buildingAreaTotal = $property['LivingAreaRange'] ?? null;
            $buildingAreaUnits = $property['BuildingAreaUnits'] ?? 'feet²';
            $formattedArea = 'N/A';

            $processed = [
                'ListingKey' => $property['ListingKey'] ?? null,
                'StreetNumber' => $property['StreetNumber'] ?? '',
                'StreetName' => $property['StreetName'] ?? '',
                'StreetSuffix' => $property['StreetSuffix'] ?? '',
                'City' => trim($property['City'] ?? ''),
                'StateOrProvince' => $property['StateOrProvince'] ?? '',
                'PostalCode' => $property['PostalCode'] ?? '',
                'ListPrice' => $property['ListPrice'] ?? 0,
                'FormattedPrice' => '$' . number_format($property['ListPrice'] ?? 0),
                'TransactionType' => $property['TransactionType'] ?? '',
                'BedroomsTotal' => (int) ($property['BedroomsTotal'] ?? 0),
                'BathroomsTotalInteger' => (int) ($property['BathroomsTotalInteger'] ?? 0),
                'LivingAreaRange' => $buildingAreaTotal,
                'BuildingAreaUnits' => $buildingAreaUnits,
                'FormattedArea' => $formattedArea,
                'DaysOnMarket' => (int) ($daysOnMarket ?? 0),
                'ListingContractDate' => $property['ListingContractDate'] ?? null,
                'PropertySubType' => $propertySubType,
                'LotSizeArea' => $property['LotSizeArea'] ?? 'N/A',
                'Latitude' => $latitude,
                'Longitude' => $longitude,
                'PublicRemarks' => $property['PublicRemarks'] ?? '',
                'PropertyType' => $property['PropertyType'] ?? '',
                'YearBuilt' => $property['YearBuilt'] ?? 'N/A',
                'MediaURL' => $mediaUrl,
                'MediaURLs' => $mediaUrls,
                'HasValidMedia' => $hasValidMedia,
                'FullAddress' => $fullAddress,
                'NormalizedAddress' => $this->normalizeAddress($fullAddress),
                'InteriorFeatures' => $property['InteriorFeatures'] ?? [],
                'ParkingTotal' => (int) ($property['ParkingTotal'] ?? 0),
                'ParkingFeatures' => $property['ParkingFeatures'] ?? [],
                'GarageType' => $property['GarageType'] ?? 'None',
                'PoolFeatures' => $property['PoolFeatures'] ?? [],
                'AssociationAmenities' => $property['AssociationAmenities'] ?? [],
                'CommunityFeatures' => $property['CommunityFeatures'] ?? [],
                'LaundryFeatures' => $property['LaundryFeatures'] ?? [],
                'View' => $property['View'] ?? [],
                'UnitNumber' => $property['UnitNumber'] ?? null,
            ];

            $processed['MapPopup'] = $this->generateMapPopup($processed);

            return $processed;
        }, $properties);
    }

    private function generateMapPopup($property)
    {
        $imageUrl = $property['MediaURL'] ?? asset('dummy.png');
        $route = $property['TransactionType'] === 'For Lease' ? route('lease.details', $property['ListingKey']) : route('buy.details', $property['ListingKey']);

        return "
            <div class='property-popup' style='
                background: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                padding: 10px;
                max-width: 300px;
                font-family: Arial, sans-serif;
            '>
                <img src='$imageUrl' style='
                    width: 100%;
                    height: 150px;
                    object-fit: cover;
                    border-radius: 4px;
                    margin-bottom: 10px;
                ' onerror='this.src=\"{{ asset('dummy.png') }}\"'>
                <h4 style='
                    color: #1976d2;
                    font-size: 18px;
                    margin: 0 0 5px;
                '>{$property['FormattedPrice']}</h4>
                <p style='
                    font-weight: bold;
                    color: #333;
                    margin: 0 0 5px;
                '>{$property['FullAddress']}</p>
                <p style='
                    color: #666;
                    font-size: 14px;
                    margin: 0 0 10px;
                '>{$property['BedroomsTotal']} Beds • {$property['BathroomsTotalInteger']} Baths • {$property['LivingAreaRange']} feet²</p>
                <p style='
                    color: #666;
                    font-size: 14px;
                    margin: 0 0 10px;
                '>{$property['PropertySubType']}</p>
                <a href='$route' style='
                    display: inline-block;
                    background: #1976d2;
                    color: white;
                    padding: 8px 12px;
                    border-radius: 4px;
                    text-decoration: none;
                    font-size: 14px;
                '>View Details</a>
            </div>
        ";
    }

    public function propertyDetails(Request $request, $listingKey)
    {
        try {

            if ($request->routeIs('sold.details')) {
                $transactionType = null;
                $status = 'Closed';
            } else {
                $transactionType = $request->routeIs('buy.details') ? 'For Sale' : 'For Lease';
                $status = 'Active';
            }

            $property = $this->fetchSingleProperty($listingKey, $transactionType, $status);

            if (!$property) {
                Log::error('Property not found for propertyDetails: ' . $listingKey);
                if ($request->routeIs('sold.details')) {
                    $redirectRoute = 'sell';
                } else {
                    $redirectRoute = $request->routeIs('buy.details') ? 'buy' : 'lease';
                }
                return redirect()->route($redirectRoute)->with('error', 'Property not found.');
            }

            $timeSlots = TimeSlot::orderBy('start_time')->get();

            Log::info('Property data for propertyDetails:', [
                'property' => $property,
                'relatedPropertiesCount' => count($property['relatedProperties'] ?? [])
            ]);

            return view('frontend.property-details', [
                'property' => $property,
                'relatedProperties' => $property['relatedProperties'] ?? [],
                'gtaCities' => $this->getGtaCities(),
                'timeSlots' => $timeSlots,
                'cityCoordinates' => $this->getCityCoordinates(),
                'transaction_type' => $transactionType,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in propertyDetails: ' . $e->getMessage(), ['ListingKey' => $listingKey]);
            if ($request->routeIs('sold.details')) {
                $redirectRoute = 'sell';
            } else {
                $redirectRoute = $request->routeIs('buy.details') ? 'buy' : 'lease';
            }
            return redirect()->route($redirectRoute)->with('error', 'Failed to load property details.');
        }
    }

    public function properties(Request $request)
    {
        try {
            ini_set('memory_limit', '512M');

            $page = max(1, $request->input('page', 1));

            $transactionType = $request->routeIs('buy') ? 'For Sale' : ['For Lease', 'For Sub-Lease'];

            $filter = $this->buildPropertyFilter($transactionType, $request);

            $propertyData = $this->fetchPropertiesWithOptimizedPagination($filter, 50, $page, null, $transactionType, $request);

            $properties = $propertyData['properties'];
            $clusteredProperties = $propertyData['clusteredProperties'];
            $totalCount = $propertyData['totalCount'];
            $actualFilteredCount = $propertyData['actualFilteredCount'] ?? $propertyData['totalCount'];
            $filteredCount = $actualFilteredCount;
            $hasMorePages = $propertyData['hasMorePages'];

            $maxPrice = $this->fetchMaxPrice($transactionType);
            $maxArea = $this->fetchMaxArea($transactionType);

            $propertyTypesData = $this->fetchPropertySubTypes($transactionType);
            $counts = $this->fetchCounts($filter, $propertyTypesData['all'] ?? []);

            $propertySubTypes = $propertyTypesData['main'] ?? [];
            $allPropertyTypes = array_merge($propertyTypesData['main'] ?? [], $propertyTypesData['other'] ?? []);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'properties' => $properties,
                    'clusteredProperties' => $clusteredProperties,
                    'allPropertiesForClustering' => $properties,
                    'totalCount' => $totalCount,
                    'filteredCount' => $filteredCount,
                    'hasMorePages' => $hasMorePages,
                    'cityCounts' => $counts['cityCounts'],
                    'propertySubTypeCounts' => $counts['propertySubTypeCounts'],
                    'maxPrice' => $maxPrice,
                    'maxArea' => $maxArea,
                    'flooringOptions' => $counts['flooringOptions'],
                    'parkingTotalOptions' => $counts['parkingTotalOptions'],
                    'parkingTypeOptions' => $counts['parkingTypeOptions'],
                    'poolTypeOptions' => $counts['poolTypeOptions'],
                    'amenityOptions' => $counts['amenityOptions'],
                    'viewTypeOptions' => $counts['viewTypeOptions'],
                    'currentPage' => $page,
                ]);
            }

            $viewData = [
                'properties' => $properties,
                'clusteredProperties' => $clusteredProperties,
                'allPropertiesForClustering' => $properties,
                'totalCount' => $totalCount,
                'filteredCount' => $filteredCount,
                'hasMorePages' => $hasMorePages,
                'cityCounts' => $counts['cityCounts'],
                'propertySubTypeCounts' => $counts['propertySubTypeCounts'],
                'propertySubTypes' => $propertySubTypes,
                'allPropertyTypes' => $allPropertyTypes,
                'flooringTypes' => $counts['flooringTypes'],
                'parkingTotal' => $counts['parkingTotal'],
                'parkingTypes' => $counts['parkingTypes'],
                'poolTypes' => $counts['poolTypes'],
                'amenities' => $counts['amenities'],
                'viewTypes' => $counts['viewTypes'],
                'flooringOptions' => $counts['flooringOptions'],
                'parkingTotalOptions' => $counts['parkingTotalOptions'],
                'parkingTypeOptions' => $counts['parkingTypeOptions'],
                'poolTypeOptions' => $counts['poolTypeOptions'],
                'amenityOptions' => $counts['amenityOptions'],
                'viewTypeOptions' => $counts['viewTypeOptions'],
                'gtaCities' => $this->getGtaCities(),
                'maxPrice' => $maxPrice,
                'maxArea' => $maxArea,
                'cityCoordinates' => $this->getCityCoordinates(),
                'currentPage' => $page,
                'perPage' => $this->propertiesPerPage,
                'propertiesPerPage' => $this->propertiesPerPage,
                'transaction_type' => is_array($transactionType) ? $transactionType : $transactionType,
            ];

            return view('frontend.property-listing', $viewData);
        } catch (\Exception $e) {
            Log::error('Properties error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Failed to fetch properties'], 500);
            }

            return view('frontend.property-listing', [
                'properties' => [],
                'clusteredProperties' => [],
                'totalCount' => 0,
                'filteredCount' => 0,
                'hasMorePages' => false,
                'cityCounts' => array_fill_keys($this->getGtaCities(), 0),
                'propertySubTypeCounts' => [],
                'propertySubTypes' => [],
                'gtaCities' => $this->getGtaCities(),
                'maxPrice' => 240000,
                'maxArea' => 10000,
                'cityCoordinates' => $this->getCityCoordinates(),
                'currentPage' => 1,
                'perPage' => $this->propertiesPerPage,
                'propertiesPerPage' => $this->propertiesPerPage,
                'flooringOptions' => ['Any'],
                'parkingTotalOptions' => ['0'],
                'parkingTypeOptions' => ['None'],
                'poolTypeOptions' => ['None'],
                'amenityOptions' => [],
                'viewTypeOptions' => ['Clear'],
                'flooringTypes' => ['Any' => 0],
                'parkingTotal' => ['0' => 0],
                'parkingTypes' => ['None' => 0],
                'poolTypes' => ['None' => 0],
                'amenities' => [],
                'viewTypes' => ['Clear' => 0],
                'transaction_type' => 'For Sale',
            ]);
        }
    }

    private function createAddressBasedClusters($properties)
    {
        $clusters = [];

        foreach ($properties as $property) {
            $address = $this->normalizeAddress($property['FullAddress']);
            $city = trim($property['City']);

            if (!isset($clusters[$address])) {
                $clusters[$address] = [
                    'properties' => [],
                    'latitude' => $property['Latitude'],
                    'longitude' => $property['Longitude'],
                    'address' => $address,
                    'city' => $city,
                    'count' => 0,
                ];
            }

            $clusters[$address]['properties'][] = $property;
            $clusters[$address]['count']++;
        }

        foreach ($clusters as &$cluster) {
            if ($cluster['count'] > 1) {
                $avgLat = array_sum(array_column($cluster['properties'], 'Latitude')) / $cluster['count'];
                $avgLng = array_sum(array_column($cluster['properties'], 'Longitude')) / $cluster['count'];
                $cluster['latitude'] = $avgLat;
                $cluster['longitude'] = $avgLng;
            }
        }

        return $clusters;
    }

    private function normalizeAddress($address)
    {
        $address = preg_replace('/\b(Unit|Apt|Suite|#)\s*\d+/i', '', $address);
        $address = preg_replace('/\s+/', ' ', $address);
        $address = preg_replace('/,\s*,/', ',', $address);
        return trim($address, ' ,');
    }

    public function wishlist()
    {
        if (!Auth::check()) {
            session()->flash('showLoginPopup', true);
            return redirect()->route('buy');
        }

        $userId = Auth::id();
        $wishlistItems = Wishlist::where('user_id', $userId)->get();

        $properties = [];
        foreach ($wishlistItems as $item) {
            try {
                $property = $this->fetchSingleProperty($item->listing_key, $item->transaction_type);
                if ($property) {
                    $properties[] = $property;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch wishlist property: ' . $item->listing_key, [
                    'error' => $e->getMessage()
                ]);
                // Continue to next property even if one fails
                continue;
            }
        }

        return view('frontend.wishlist', compact('properties'));
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate([
            'listing_key' => 'required|string',
            'transaction_type' => 'required|string|in:For Sale,For Lease,For Sub-Lease',
        ]);

        if (!Auth::check()) {
            session()->flash('showLoginPopup', true);
            $redirectRoute = $request->routeIs('buy.details') ? 'buy' : 'lease';
            return redirect()->route($redirectRoute);
        }

        $userId = Auth::id();
        $listingKey = $request->listing_key;
        $transactionType = $request->transaction_type;

        $existing = Wishlist::where('user_id', $userId)
            ->where('listing_key', $listingKey)
            ->where('transaction_type', $transactionType)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from wishlist';
            $added = false;
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'listing_key' => $listingKey,
                'transaction_type' => $transactionType,
            ]);
            $message = 'Added to wishlist';
            $added = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'added' => $added,
        ]);
    }
}