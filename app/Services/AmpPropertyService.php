<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AmpPropertyService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = env('AMP_API_BASE_URL', 'https://query.ampre.ca/odata/');
        $this->token = env('AMP_API_TOKEN');
    }

    public function getDistinctValues(string $field, string $filter): Collection
    {
        $query = [
            '$select' => $field,
            '$filter' => $filter,
            '$orderby' => $field,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->timeout(60)->get($this->baseUrl . 'Property', $query);

        if ($response->successful()) {
            return collect($response->json()['value'] ?? []);
        }

        return collect();
    }

    public function getLookupValues(string $lookupName): Collection
    {
        $query = [
            '$filter' => "LookupName eq '" . addslashes($lookupName) . "'",
            '$select' => 'LookupValue',
            '$orderby' => 'LookupValue',
        ];

        Log::info('Fetching lookup values', ['lookupName' => $lookupName, 'query' => $query]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])
            ->connectTimeout(30) // Separate connect timeout
            ->timeout(120)       // Total timeout (reduced from 240 to test)
            ->get($this->baseUrl . 'Lookup', $query);

        Log::info('Lookup response', ['status' => $response->status(), 'headers' => $response->headers(), 'time' => $response->handlerStats()['total_time'] ?? 'N/A']);

        if ($response->successful()) {
            return collect($response->json()['value'] ?? [])->pluck('LookupValue');
        }

        Log::warning('Lookup request failed', ['status' => $response->status(), 'body' => $response->body()]);
        return collect();
    }

    public function getPaginatedProperties(
        string $fields,
        int $perPage,
        int $page,
        string $filter,
        string $sortColumn,
        string $sortDirection
    ): array {
        $skip = ($page - 1) * $perPage;

        $query = [
            '$select' => $fields,
            '$filter' => $filter,
            '$orderby' => "{$sortColumn} {$sortDirection}",
            '$top' => $perPage,
            '$skip' => $skip,
            '$count' => 'true',
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->timeout(120)->get($this->baseUrl . 'Property', $query);

        if ($response->successful()) {
            $data = $response->json();
            $properties = $data['value'] ?? [];

            // Log detailed API response data
            Log::info('RAW API RESPONSE DATA', [
                'full_response' => $data,
                'properties_count' => count($properties),
                'total_count' => $data['@odata.count'] ?? 0,
                'first_5_properties' => array_slice($properties, 0, 5),
                'all_listing_keys' => collect($properties)->pluck('ListingKey')->take(10)->toArray(),
            ]);

            Log::info('Fetched paginated properties from API', [
                'count' => count($properties),
                'total' => $data['@odata.count'] ?? 0,
                'first_listing_key' => $properties[0]['ListingKey'] ?? null,
                'first_city' => $properties[0]['City'] ?? null,
                'first_price' => $properties[0]['ListPrice'] ?? null,
            ]);
            return [
                'data' => $properties,
                'total' => $data['@odata.count'] ?? 0,
            ];
        }

        Log::error('Failed to fetch paginated properties from API', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        return ['data' => [], 'total' => 0];
    }

    public function getProperties(string $fields, int $top, string $filter, int $skip = 0, string $orderBy = null): array
    {
        $query = [
            '$select' => $fields,
            '$filter' => $filter,
            '$top' => $top,
            '$skip' => $skip,
        ];

        if ($orderBy) {
            $query['$orderby'] = $orderBy;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->timeout(120)->get($this->baseUrl . 'Property', $query);

        if ($response->successful()) {
            return $response->json()['value'] ?? [];
        }

        return [];
    }

    public function getProperty(string $listingKey): array
    {
        if (empty($this->token)) {
            Log::error('AMP_API_TOKEN is missing or empty');
            return [];
        }

        $url = $this->baseUrl . "Property('{$listingKey}')";

        // Retry logic: Try up to 3 times with exponential backoff
        $maxRetries = 3;
        $retryDelay = 2;  // Start with 2 seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                    'User-Agent' => 'Laravel-App/1.0',  // Add user-agent if API requires it
                ])
                    ->connectTimeout(120)  // Separate connect timeout (30s)
                    ->timeout(180)         // Total timeout (90s, longer than before)
                    ->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info("Successfully fetched property: {$listingKey}", ['data_keys' => array_keys($data ?? [])]);
                    return $data ?? [];
                }

                // Log non-success responses (e.g., 404, 401)
                Log::warning("API request failed for property {$listingKey}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];  // Non-success but connected: return empty

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Connection failed for property {$listingKey} (attempt {$attempt}): " . $e->getMessage(), [
                    'url' => $url,
                    'attempt' => $attempt,
                ]);

                if ($attempt < $maxRetries) {
                    sleep($retryDelay);  // Wait before retry
                    $retryDelay *= 2;    // Exponential backoff (2s, 4s, 8s)
                } else {
                    // Final failure: Return empty array
                    return [];
                }
            } catch (\Exception $e) {
                Log::error("Unexpected error fetching property {$listingKey}: " . $e->getMessage());
                return [];
            }
        }

        return [];
    }

    public function getMediaByListingKey(string $listingKey, int $maxPerProperty = 20): array
    {
        // EXACT MATCH to working PropertyController: No ImageSizeDescription filter; use standard OData 'and' with spaces
        $filter = "ResourceName eq 'Property' and ResourceRecordKey eq '" . addslashes($listingKey) . "'";

        $query = [
            '$filter' => $filter,
            '$select' => 'ResourceRecordKey,MediaURL,Order',
            '$orderby' => 'Order asc',
            '$top' => $maxPerProperty
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->timeout(60)->get($this->baseUrl . 'Media', $query);

            if ($response->successful()) {
                $mediaRecords = $response->json()['value'] ?? [];
                Log::info('Media fetched successfully', [
                    'listingKey' => $listingKey,
                    'filter' => $filter,
                    'count' => count($mediaRecords)
                ]);
                return $mediaRecords;
            }

            Log::warning('getMediaByListingKey failed', [
                'listingKey' => $listingKey,
                'filter' => $filter,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('getMediaByListingKey exception', ['message' => $e->getMessage(), 'listingKey' => $listingKey]);
        }

        return [];
    }
}
