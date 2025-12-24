<?php
namespace App\Http\Controllers;

use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Traits\AddressFormatter;

class HomeController extends Controller
{
    use AddressFormatter;
    private $baseUrl = 'https://query.ampre.ca/odata/';

    private $gtaCities = [
        'Ajax',
        'Aurora',
        'Brampton',
        'Burlington',
        'Caledon',
        'East Gwillimbury',
        'Halton Hills',
        'King',
        'Markham',
        'Milton',
        'Mississauga',
        'Newmarket',
        'Oakville',
        'Oshawa',
        'Pickering',
        'Richmond Hill',
        'Toronto',
        'Uxbridge',
        'Vaughan',
        'Whitby',
        'Whitchurch-Stouffville',
        'Kitchener',
        'Waterloo',
        'Hamilton',
        'Cambridge',
        'Guelph',
        'London',
        'Windsor',
        'Kingston',
        'Ottawa',

        'North York',
        'Bradford',
        'Bradford West Gwillimbury',
    ];

    // Property types and subtypes to exclude
    private $excludedPropertyTypes = ['Commercial'];

    private $excludedPropertySubTypes = [
        'Commerical',
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
        'Upper level',
        'Vacant Land',
        'Vacant Land Condo',
    ];

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

    public function index()
    {
        // INSTANT LOAD: Just return cities and empty data
        // Properties and counts will load via AJAX
        Log::info('⚡ Index called - instant load mode');
        
        try {
            $cities = City::where('is_home_active', true)->get();
            
            return view('frontend.index', [
                'cities' => $cities,
                'properties' => [], // Will load via AJAX
                'cityCounts' => array_fill_keys($cities->pluck('city')->toArray(), 0),
                'propertySubTypeCounts' => [],
                'totalActiveProperties' => 0,
                'lazyLoad' => true, // Flag to trigger AJAX loading
            ]);
        } catch (\Exception $e) {
            Log::error('HomeController index error:', ['message' => $e->getMessage()]);
            
            return view('frontend.index', [
                'properties' => [],
                'cities' => [],
                'cityCounts' => [],
                'propertySubTypeCounts' => [],
                'totalActiveProperties' => 0,
                'lazyLoad' => false,
            ]);
        }
    }

    /**
     * AJAX endpoint for loading properties
     */
    public function getProperties()
    {
        $pageStart = microtime(true);
        Log::info('⏱️ [0.00s] AJAX: Properties fetch started');
        
        try {
            $cities = City::where('is_home_active', true)->get();
            $selectedCities = $cities->pluck('city')->toArray();
            $candidatesNeeded = count($selectedCities) * 5;
            $allTransactionTypes = ['For Sale', 'For Lease'];

            // Parallel property fetch
            $propStart = microtime(true);
            $responses = Http::pool(fn (\Illuminate\Http\Client\Pool $pool) => [
                'forSale' => $pool->as('forSale')->withHeaders([
                    'Authorization' => 'Bearer ' . env('AMP_API_TOKEN'),
                    'Accept' => 'application/json',
                ])->timeout(120)->get($this->baseUrl . 'Property', [
                    '$filter' => $this->buildPropertyFilter($selectedCities, 'For Sale'),
                    '$orderby' => 'ModificationTimestamp desc',
                    '$top' => min(1000, $candidatesNeeded * 3),
                    '$select' => 'ListingKey,UnitNumber,StreetNumber,StreetName,StreetSuffix,City,StateOrProvince,PostalCode,ListPrice,TransactionType,BedroomsTotal,BathroomsTotalInteger,LivingAreaRange,DaysOnMarket,ListingContractDate,PropertySubType,LotSizeArea,Latitude,Longitude,PublicRemarks,PropertyType,YearBuilt,UnparsedAddress',
                ]),
                'forLease' => $pool->as('forLease')->withHeaders([
                    'Authorization' => 'Bearer ' . env('AMP_API_TOKEN'),
                    'Accept' => 'application/json',
                ])->timeout(120)->get($this->baseUrl . 'Property', [
                    '$filter' => $this->buildPropertyFilter($selectedCities, 'For Lease'),
                    '$orderby' => 'ModificationTimestamp desc',
                    '$top' => min(1000, $candidatesNeeded * 3),
                    '$select' => 'ListingKey,UnitNumber,StreetNumber,StreetName,StreetSuffix,City,StateOrProvince,PostalCode,ListPrice,TransactionType,BedroomsTotal,BathroomsTotalInteger,LivingAreaRange,DaysOnMarket,ListingContractDate,PropertySubType,LotSizeArea,Latitude,Longitude,PublicRemarks,PropertyType,YearBuilt,UnparsedAddress',
                ]),
            ]);
            
            Log::info('⏱️ [' . round(microtime(true) - $pageStart, 2) . 's] Parallel fetch complete (took ' . round(microtime(true) - $propStart, 2) . 's)');

            $allPropertiesWithMedia = [];

            // Process For Sale
            if ($responses['forSale']->successful()) {
                $forSaleProperties = $responses['forSale']->json()['value'] ?? [];
                if (!empty($forSaleProperties)) {
                    $listingKeys = array_column($forSaleProperties, 'ListingKey');
                    $mediaMap = $this->fetchMedia($listingKeys, 1);
                    $allPropertiesWithMedia = array_merge(
                        $allPropertiesWithMedia,
                        $this->processPropertiesWithMedia($forSaleProperties, $mediaMap, $selectedCities, 5)
                    );
                }
            }

            // Process For Lease
            if ($responses['forLease']->successful()) {
                $forLeaseProperties = $responses['forLease']->json()['value'] ?? [];
                if (!empty($forLeaseProperties)) {
                    $listingKeys = array_column($forLeaseProperties, 'ListingKey');
                    $mediaMap = $this->fetchMedia($listingKeys, 1);
                    $allPropertiesWithMedia = array_merge(
                        $allPropertiesWithMedia,
                        $this->processPropertiesWithMedia($forLeaseProperties, $mediaMap, $selectedCities, 5)
                    );
                }
            }

            shuffle($allPropertiesWithMedia);
            $finalProperties = array_slice($allPropertiesWithMedia, 0, 12);

            Log::info('⏱️ [' . round(microtime(true) - $pageStart, 2) . 's] 🏁 Properties AJAX complete');

            return response()->json([
                'success' => true,
                'properties' => $finalProperties,
                'count' => count($finalProperties),
            ]);

        } catch (\Exception $e) {
            Log::error('AJAX properties error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'properties' => [],
            ], 500);
        }
    }

    /**
     * AJAX endpoint for loading counts
     */
    public function getCounts()
    {
        $pageStart = microtime(true);
        Log::info('⏱️ [0.00s] AJAX: Counts fetch started');
        
        try {
            $cities = City::where('is_home_active', true)->get();
            $selectedCities = $cities->pluck('city')->toArray();
            $allTransactionTypes = ['For Sale', 'For Lease'];

            $counts = $this->fetchPropertyCounts($allTransactionTypes, $selectedCities);

            Log::info('⏱️ [' . round(microtime(true) - $pageStart, 2) . 's] 🏁 Counts AJAX complete');

            return response()->json([
                'success' => true,
                'cityCounts' => $counts['cityCounts'],
                'propertySubTypeCounts' => $counts['propertySubTypeCounts'],
                'totalActiveProperties' => $counts['totalActiveProperties'],
            ]);

        } catch (\Exception $e) {
            Log::error('AJAX counts error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build property filter for API queries
     */
    private function buildPropertyFilter($cities, $transactionType)
    {
        $citiesFilter = "City in ('" . implode("','", array_map('addslashes', $cities)) . "')";
        $statusFilter = "StandardStatus eq 'Active'";
        $transactionFilter = "TransactionType eq '" . addslashes($transactionType) . "'";
        $typeFilter = "PropertyType ne 'Commercial'";

        // Exclude unwanted subtypes
        $subTypeExclusions = array_map(fn($t) => "PropertySubType ne '" . addslashes($t) . "'", $this->excludedPropertySubTypes);
        $subTypeFilter = !empty($subTypeExclusions) ? ' and ' . implode(' and ', $subTypeExclusions) : '';

        return "$citiesFilter and $statusFilter and $transactionFilter and $typeFilter$subTypeFilter";
    }

    /**
     * Process properties with media and filter by city
     */
    private function processPropertiesWithMedia($properties, $mediaMap, $selectedCities, $maxPerCity)
    {
        $results = [];
        $perCityCount = array_fill_keys($selectedCities, 0);

        foreach ($properties as $property) {
            $city = trim($property['City'] ?? '');
            if (!in_array($city, $selectedCities))
                continue;

            // Stop if we already have enough per city
            if ($perCityCount[$city] >= $maxPerCity)
                continue;

            $listingKey = $property['ListingKey'];
            if (empty($mediaMap[$listingKey]))
                continue;

            $processed = $this->processProperty($property, $mediaMap[$listingKey][0]);
            $results[] = $processed;
            $perCityCount[$city]++;
        }

        return $results;
    }

    private function fetchPropertiesWithMediaOptimized($cities, $limitTotal = 30, $transactionType = 'For Sale')
    {
        $httpClient = $this->getHttpClient();

        $citiesFilter = "City in ('" . implode("','", array_map('addslashes', $cities)) . "')";
        $statusFilter = "StandardStatus eq 'Active'";
        $transactionFilter = "TransactionType eq '" . addslashes($transactionType) . "'";
        $typeFilter = "PropertyType ne 'Commercial'";

        // Exclude unwanted subtypes
        $subTypeExclusions = array_map(fn($t) => "PropertySubType ne '" . addslashes($t) . "'", $this->excludedPropertySubTypes);
        $subTypeFilter = !empty($subTypeExclusions) ? ' and ' . implode(' and ', $subTypeExclusions) : '';

        $filter = "$citiesFilter and $statusFilter and $transactionFilter and $typeFilter$subTypeFilter";

        $response = $httpClient->get($this->baseUrl . 'Property', [
            '$filter' => $filter,
            '$orderby' => 'ModificationTimestamp desc',
            '$top' => min(1000, $limitTotal * 3), // Fetch extra to ensure enough have photos
            '$select' => 'ListingKey,UnitNumber,StreetNumber,StreetName,StreetSuffix,City,StateOrProvince,PostalCode,ListPrice,TransactionType,BedroomsTotal,BathroomsTotalInteger,LivingAreaRange,DaysOnMarket,ListingContractDate,PropertySubType,LotSizeArea,Latitude,Longitude,PublicRemarks,PropertyType,YearBuilt,UnparsedAddress',
        ]);

        if (!$response->successful()) {
            Log::error('Property fetch failed (optimized)', ['status' => $response->status(), 'body' => $response->body()]);
            return [];
        }

        $properties = $response->json()['value'] ?? [];

        if (empty($properties)) {
            return [];
        }

        // ONE media query for ALL listing keys
        $listingKeys = array_column($properties, 'ListingKey');
        $mediaMap = $this->fetchMedia($listingKeys, 1); // Only need 1 photo per property

        $results = [];
        $perCityCount = array_fill_keys($cities, 0);
        $maxPerCity = 5; // Safety cap per city

        foreach ($properties as $property) {
            $city = trim($property['City'] ?? '');
            if (!in_array($city, $cities))
                continue;

            // Stop if we already have enough per city
            if ($perCityCount[$city] >= $maxPerCity)
                continue;

            $listingKey = $property['ListingKey'];
            if (empty($mediaMap[$listingKey]))
                continue;

            $processed = $this->processProperty($property, $mediaMap[$listingKey][0]);
            $results[] = $processed;
            $perCityCount[$city]++;

            // Early exit if we have enough total
            if (count($results) >= $limitTotal) {
                break;
            }
        }

        Log::info("Fetched properties with media (optimized) for $transactionType", [
            'requested_cities' => count($cities),
            'candidates_fetched' => count($properties),
            'with_photos' => count($results),
            'per_city' => $perCityCount,
        ]);

        return $results;
    }

    private function fetchPropertiesWithMedia($cities, $limit = 20, $transactionType = 'For Sale')
    {
        $httpClient = $this->getHttpClient();

        // Build filter using same logic as PropertyController
        $citiesFilter = "City in ('" . implode("','", array_map('addslashes', $cities)) . "')";
        $statusFilter = "StandardStatus eq 'Active'";
        $transactionFilter = "TransactionType eq '" . addslashes($transactionType) . "'";

        $filter = "$citiesFilter and $statusFilter and $transactionFilter";

        try {
            $response = $httpClient->get($this->baseUrl . 'Property', [
                '$filter' => $filter,
                '$orderby' => 'ModificationTimestamp desc',
                '$top' => 500, // Fetch more to filter by media
                '$select' => 'ListingKey,StreetNumber,StreetName,StreetSuffix,City,StateOrProvince,PostalCode,ListPrice,TransactionType,BedroomsTotal,BathroomsTotalInteger,LivingAreaRange,DaysOnMarket,ListingContractDate,PropertySubType,LotSizeArea,Latitude,Longitude,PublicRemarks,PropertyType,YearBuilt,UnparsedAddress',
            ]);

            if (!$response->successful()) {
                Log::error('Property fetch failed', ['status' => $response->status()]);
                return [];
            }

            $properties = $response->json()['value'] ?? [];
            Log::info('Fetched ' . count($properties) . ' properties from API');

            if (empty($properties)) {
                return [];
            }

            // Get media for all properties
            $listingKeys = array_column($properties, 'ListingKey');
            $mediaMap = $this->fetchMedia($listingKeys, 1);

            // Filter properties that have media and process them
            $propertiesWithMedia = [];
            foreach ($properties as $property) {
                $listingKey = $property['ListingKey'];
                if (!empty($mediaMap[$listingKey])) {
                    $processedProperty = $this->processProperty($property, $mediaMap[$listingKey][0]);
                    $propertiesWithMedia[] = $processedProperty;

                    if (count($propertiesWithMedia) >= $limit) {
                        break;
                    }
                }
            }

            Log::info('Returning ' . count($propertiesWithMedia) . ' properties with media');
            return $propertiesWithMedia;

        } catch (\Exception $e) {
            Log::error('Property fetch error: ' . $e->getMessage());
            return [];
        }
    }

    private function fetchPropertiesByCities($cities, $propertiesPerCity = 5, $transactionType = 'For Sale')
    {
        $allProperties = [];
        $httpClient = $this->getHttpClient();

        foreach ($cities as $city) {
            $cityFilter = "City eq '" . addslashes($city) . "'";
            $statusFilter = "StandardStatus eq 'Active'";
            $transactionFilter = "TransactionType eq '" . addslashes($transactionType) . "'";

            $filter = "$cityFilter and $statusFilter and $transactionFilter";

            try {
                $response = $httpClient->get($this->baseUrl . 'Property', [
                    '$filter' => $filter,
                    '$orderby' => 'ModificationTimestamp desc',
                    '$top' => 20, // Reduced to optimize load time
                    '$select' => 'ListingKey,StreetNumber,StreetName,StreetSuffix,City,StateOrProvince,PostalCode,ListPrice,TransactionType,BedroomsTotal,BathroomsTotalInteger,LivingAreaRange,DaysOnMarket,ListingContractDate,PropertySubType,LotSizeArea,Latitude,Longitude,PublicRemarks,PropertyType,YearBuilt,UnparsedAddress',
                ]);

                if ($response->successful()) {
                    $properties = $response->json()['value'] ?? [];

                    if (!empty($properties)) {
                        // Get media for these properties
                        $listingKeys = array_column($properties, 'ListingKey');
                        $mediaMap = $this->fetchMedia($listingKeys, 1);

                        // Filter properties that have media and take only the required number
                        $cityProperties = [];
                        foreach ($properties as $property) {
                            $listingKey = $property['ListingKey'];
                            if (!empty($mediaMap[$listingKey])) {
                                $processedProperty = $this->processProperty($property, $mediaMap[$listingKey][0]);
                                $cityProperties[] = $processedProperty;

                                if (count($cityProperties) >= $propertiesPerCity) {
                                    break;
                                }
                            }
                        }

                        $allProperties = array_merge($allProperties, $cityProperties);
                        Log::info("Fetched " . count($cityProperties) . " properties with media for city: $city");
                    }
                } else {
                    Log::warning("Failed to fetch properties for city: $city", ['status' => $response->status()]);
                }

            } catch (\Exception $e) {
                Log::error("Error fetching properties for city $city: " . $e->getMessage());
            }
        }

        Log::info('Total properties fetched by cities: ' . count($allProperties));
        return $allProperties;
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
            $apiStart = microtime(true);
            // OPTIMIZATION: Use $select to fetch only needed fields (reduces payload size)
            $response = $httpClient->get("{$this->baseUrl}Media", [
                '$filter' => $filter,
                '$orderby' => 'Order asc',
                '$top' => 2000,
                '$select' => 'ResourceRecordKey,MediaURL,Order',
            ]);
            $apiTime = round(microtime(true) - $apiStart, 2);

            if (!$response->successful()) {
                Log::warning('Media fetch failed', ['status' => $response->status()]);
                return [];
            }

            $allMedia = $response->json()['value'] ?? [];
            Log::info('PHOTOS LOADED', [
                'keys_requested' => count($listingKeys),
                'photos_found' => count($allMedia),
                'sample_key' => $allMedia[0]['ResourceRecordKey'] ?? 'none',
                'api_time' => $apiTime . 's',
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

            // Skip obvious thumbnails
            if (
                str_contains($lowerUrl, 'thumbnail') ||
                str_contains($lowerUrl, '/small/') ||
                str_contains($lowerUrl, '/thumb/') ||
                str_contains($lowerUrl, '240x') ||
                str_contains($lowerUrl, '480x')
            ) {
                continue;
            }

            // Prefer no-watermark
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

    private function processProperty($property, $mediaUrl)
    {
        $daysOnMarket = $property['DaysOnMarket'] ?? null;
        if (is_null($daysOnMarket) && !empty($property['ListingContractDate'])) {
            $daysOnMarket = Carbon::parse($property['ListingContractDate'])->diffInDays(Carbon::now());
        }

        $fullAddress = $this->formatPropertyAddress($property);

        $latitude = isset($property['Latitude']) && is_numeric($property['Latitude']) && $property['Latitude'] !== 0 ? (float) $property['Latitude'] : null;
        $longitude = isset($property['Longitude']) && is_numeric($property['Longitude']) && $property['Longitude'] !== 0 ? (float) $property['Longitude'] : null;

        if (is_null($latitude) || is_null($longitude)) {
            // Fetch coordinates from City model dynamically if missing
            $cityParam = $property['City'] ?? '';
            $cityModel = City::where('city', $cityParam)->first();

            if ($cityModel) {
                $latitude = $cityModel->latitude;
                $longitude = $cityModel->longitude;
            }
        }

        return [
            'ListingKey' => $property['ListingKey'] ?? null,
            'StreetNumber' => $property['StreetNumber'] ?? '',
            'StreetName' => $property['StreetName'] ?? '',
            'StreetSuffix' => $property['StreetSuffix'] ?? '',
            'City' => trim($property['City'] ?? ''),
            'CityRegion' => '', // Add missing CityRegion field
            'StateOrProvince' => $property['StateOrProvince'] ?? '',
            'PostalCode' => $property['PostalCode'] ?? '',
            'ListPrice' => $property['ListPrice'] ?? 0,
            'FormattedPrice' => '$' . number_format($property['ListPrice'] ?? 0),
            'TransactionType' => $property['TransactionType'] ?? '',
            'BedroomsTotal' => (int) ($property['BedroomsTotal'] ?? 0),
            'BathroomsTotalInteger' => (int) ($property['BathroomsTotalInteger'] ?? 0),
            'LivingAreaRange' => $property['LivingAreaRange'] ?? 'N/A',
            'DaysOnMarket' => (int) ($daysOnMarket ?? 0),
            'ListingContractDate' => $property['ListingContractDate'] ?? null,
            'PropertySubType' => $property['PropertySubType'] ?? 'N/A',
            'LotSizeArea' => $property['LotSizeArea'] ?? 'N/A',
            'Latitude' => $latitude,
            'Longitude' => $longitude,
            'PublicRemarks' => $property['PublicRemarks'] ?? '',
            'PropertyType' => $property['PropertyType'] ?? '',
            'YearBuilt' => $property['YearBuilt'] ?? 'N/A',
            'MediaURL' => $mediaUrl ?? asset('dummy.png'),
            'MediaURLs' => [$mediaUrl],
            'HasValidMedia' => !empty($mediaUrl),
            'FullAddress' => $fullAddress,
        ];
    }

    private function fetchPropertyCounts($transactionTypes, $specificCities = null)
    {
        $httpClient = $this->getHttpClient();

        $selectedCities = $specificCities ?: [];

        $citiesFilter = "City in ('" . implode("','", array_map('addslashes', $selectedCities)) . "')";
        $statusFilter = "StandardStatus eq 'Active'";
        $propertyTypeFilter = "PropertyType ne 'Commercial'";

        // Build subtype exclusion filters
        $subTypeFilters = [];
        foreach ($this->excludedPropertySubTypes as $subType) {
            $subTypeFilters[] = "PropertySubType ne '" . addslashes($subType) . "'";
        }
        $subTypeExclusionFilter = implode(' and ', $subTypeFilters);

        if (is_array($transactionTypes)) {
            $transactionFilter = "(" . implode(" or ", array_map(fn($type) => "TransactionType eq '" . addslashes($type) . "'", $transactionTypes)) . ")";
        } else {
            $transactionFilter = "TransactionType eq '" . addslashes($transactionTypes) . "'";
        }

        $filter = "$citiesFilter and $statusFilter and $transactionFilter and $propertyTypeFilter and $subTypeExclusionFilter";

        $cityCounts = array_fill_keys($selectedCities, 0);
        $propertySubTypeCounts = [];
        $totalActiveProperties = 0;

        try {
            // OPTIMIZATION 1: Get total count first without fetching records
            $apiStart = microtime(true);
            $countResponse = $httpClient->get($this->baseUrl . 'Property', [
                '$filter' => $filter,
                '$count' => 'true',
                '$top' => 0,
            ]);
            $apiTime = round(microtime(true) - $apiStart, 2);

            if ($countResponse->successful()) {
                $totalActiveProperties = $countResponse->json()['@odata.count'] ?? 0;
                Log::info('✓ Total count (optimized)', [
                    'total' => $totalActiveProperties,
                    'api_time' => $apiTime . 's'
                ]);
            }

            // OPTIMIZATION 2: Fetch only City,PropertySubType for breakdown (not PropertyType)
            $pageSize = 1000;
            $skip = 0;

            while (true) {
                $response = $httpClient->get($this->baseUrl . 'Property', [
                    '$filter' => $filter,
                    '$select' => 'City,PropertySubType',
                    '$top' => $pageSize,
                    '$skip' => $skip,
                ]);

                if ($response->successful()) {
                    $records = $response->json()['value'] ?? [];

                    foreach ($records as $record) {
                        $city = trim($record['City'] ?? '');
                        $subType = trim($record['PropertySubType'] ?? '');

                        // Only count if it's one of our selected cities
                        if (in_array($city, $selectedCities)) {
                            $cityCounts[$city]++;
                        }

                        // Count property subtypes (only non-excluded ones)
                        if (!empty($subType) && !in_array($subType, $this->excludedPropertySubTypes)) {
                            $propertySubTypeCounts[$subType] = ($propertySubTypeCounts[$subType] ?? 0) + 1;
                        }
                    }

                    if (count($records) < $pageSize || $skip >= 100000) {
                        break;
                    }
                    $skip += $pageSize;
                    // usleep(100000);
                } else {
                    Log::error('Count fetch failed: Status ' . $response->status(), ['response' => $response->body(), 'filter' => $filter]);
                    break;
                }
            }
        } catch (\Exception $e) {
            Log::error('Count fetch error: ' . $e->getMessage(), ['filter' => $filter]);
        }

        // Define specific subtypes to display individually
        $specificSubTypes = [
            'Detached',
            'Semi-Detached',
            'Freehold Townhouse',
            'Condo Townhouse',
            'Condo Apartment',
            'Link',
            'Duplex',
            'Triplex',
            'Multiplex',
        ];

        // Separate specific subtypes and calculate "Other" count
        $displaySubTypeCounts = [];
        $otherCount = 0;

        foreach ($propertySubTypeCounts as $subType => $count) {
            if (in_array($subType, $specificSubTypes)) {
                $displaySubTypeCounts[$subType] = $count;
            } else {
                $otherCount += $count;
            }
        }

        // Add "Other" if there are any
        if ($otherCount > 0) {
            $displaySubTypeCounts['Other'] = $otherCount;
        }

        // Sort to maintain order
        ksort($displaySubTypeCounts);

        Log::info('✓ Counts complete', [
            'total' => $totalActiveProperties,
            'cities' => count($cityCounts),
            'subtypes' => count($displaySubTypeCounts),
        ]);

        return [
            'cityCounts' => $cityCounts,
            'propertySubTypeCounts' => $displaySubTypeCounts,
            'totalActiveProperties' => $totalActiveProperties,
        ];
    }

    public function neighbourhood()
    {
        try {
            $cities = City::where('is_neighbourhood_active', true)->get();
            $selectedCities = $cities->pluck('city')->toArray();

            ini_set('memory_limit', '4096M');

            // Fetch counts for all transaction types like home
            $counts = $this->fetchPropertyCounts(['For Sale', 'For Lease'], $selectedCities);
            Log::info('Neighbourhood Counts:', ['cityCounts' => $counts['cityCounts']]);

            return view('frontend.neighbourhood', [
                'properties' => [],
                'cities' => $cities,
                'cityCounts' => $counts['cityCounts'],
                'propertySubTypeCounts' => $counts['propertySubTypeCounts'],
                'totalActiveProperties' => $counts['totalActiveProperties'],
            ]);
        } catch (\Exception $e) {
            Log::error('HomeController neighbourhood error:', ['message' => $e->getMessage()]);
            return view('frontend.neighbourhood', [
                'properties' => [],
                'cityCounts' => array_fill_keys($selectedCities, 0),
                'propertySubTypeCounts' => [],
                'totalActiveProperties' => 0,
            ]);
        }
    }

    public function neighbourhoodDetails(City $city)
    {
        if (!$city->is_neighbourhood_active) {
            abort(404);
        }

        try {
            // Fetch properties for sale in this city
            $saleProperties = $this->fetchPropertiesWithMediaOptimized([$city->city], 20, 'For Sale');

            // Fetch properties for lease in this city
            $leaseProperties = $this->fetchPropertiesWithMediaOptimized([$city->city], 20, 'For Lease');

            // Combine and shuffle
            $relatedProperties = array_merge($saleProperties, $leaseProperties);
            shuffle($relatedProperties);

            return view('frontend.neighbourhood-details', compact('city', 'relatedProperties'));
        } catch (\Exception $e) {
            Log::error('HomeController neighbourhoodDetails error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return view('frontend.neighbourhood-details', [
                'city' => $city,
                'relatedProperties' => [],
            ]);
        }
    }
}