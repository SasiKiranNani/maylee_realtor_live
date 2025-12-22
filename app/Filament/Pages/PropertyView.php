<?php

namespace App\Filament\Pages;

use App\Services\AmpPropertyService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PropertyView extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Property Details';
    protected static string $view = 'filament.pages.property-view';
    protected static bool $shouldRegisterNavigation = false;

    public array $property = [];
    public array $media = [];

    public function getCities()
    {
        return \App\Models\City::where('status', true)
            ->pluck('city')
            ->sort()
            ->values()
            ->toArray();
    }


    public function mount(): void
    {
        $id = Request::query('id');
        if (!$id) {
            abort(404, 'Property ID not provided');
        }

        $service = app(AmpPropertyService::class);

        // Fetch property using your existing service
        $rawProperty = $service->getProperty($id) ?? [];

        if (empty($rawProperty)) {
            $this->property = ['error' => 'Unable to fetch property details at this time.'];
            return;
        }

        // PROCESS ALL PROPERTY DATA LIKE PROPERTYCONTROLLER DOES
        $this->property = $this->processPropertyData($rawProperty);

        // Fetch media
        $listingKey = $this->property['ListingKey'] ?? null;
        if ($listingKey) {
            try {
                $mediaRecords = $service->getMediaByListingKey($listingKey, 20);
                $this->media = $this->extractMediaUrls($mediaRecords);
            } catch (\Throwable $e) {
                Log::warning('Failed to get media for listing ' . $listingKey, ['err' => $e->getMessage()]);
                $this->media = [];
            }
        }

        // Handle photos merging
        $photos = $this->property['Photos'] ?? [];
        if (is_string($photos)) {
            $photos = array_filter(array_map('trim', explode(',', $photos)));
        }

        $allImages = array_merge($this->media, $photos);
        $uniqueImages = array_values(array_unique($allImages));
        $this->media = array_slice($uniqueImages, 0, 20);
    }

    private function processPropertyData(array $property): array
    {
        // Calculate DaysOnMarket like PropertyController
        $daysOnMarket = $property['DaysOnMarket'] ?? null;
        if (is_null($daysOnMarket) && !empty($property['ListingContractDate'])) {
            $daysOnMarket = Carbon::parse($property['ListingContractDate'])->diffInDays(Carbon::now());
        }

        // Build FullAddress like PropertyController
        $street = trim(($property['StreetNumber'] ?? '') . ' ' .
            ($property['StreetName'] ?? '') . ' ' .
            ($property['StreetSuffix'] ?? ''));

        $fullAddress = $property['UnparsedAddress'] ??
            $street . ', ' . $property['City'] . ', ' .
            $property['StateOrProvince'] . ' ' . ($property['PostalCode'] ?? '');

        // Handle coordinates like PropertyController
        $latitude = isset($property['Latitude']) && is_numeric($property['Latitude']) && $property['Latitude'] !== 0 ? (float) $property['Latitude'] : null;
        $longitude = isset($property['Longitude']) && is_numeric($property['Longitude']) && $property['Longitude'] !== 0 ? (float) $property['Longitude'] : null;

        if (is_null($latitude) || is_null($longitude)) {
            $city = \App\Models\City::where('city', $property['City'])->first();
            if ($city && $city->latitude && $city->longitude) {
                $latitude = $city->latitude + (rand(-100, 100) / 10000);
                $longitude = $city->longitude + (rand(-100, 100) / 10000);
            } else {
                $latitude = 43.6532; 
                $longitude = -79.3832;
            }
        }

        // FIX: Ensure numeric values for formatting
        $livingAreaRange = $property['LivingAreaRange'] ?? 'N/A';
       

        $listPrice = $property['ListPrice'] ?? 0;
        if (is_string($listPrice)) {
            $listPrice = (float) preg_replace('/[^\d.]/', '', $listPrice);
        }

        // Return ALL processed data like PropertyController
        return [
            // Basic Info
            'ListingKey' => $property['ListingKey'] ?? null,
            'StreetNumber' => $property['StreetNumber'] ?? '',
            'StreetName' => $property['StreetName'] ?? '',
            'StreetSuffix' => $property['StreetSuffix'] ?? '',
            'City' => trim($property['City'] ?? ''),
            'StateOrProvince' => $property['StateOrProvince'] ?? '',
            'PostalCode' => $property['PostalCode'] ?? '',
            'UnparsedAddress' => $property['UnparsedAddress'] ?? '',
            'UnitNumber' => $property['UnitNumber'] ?? null,

            // Financial (FIXED: Ensure numeric values)
            'ListPrice' => $listPrice,
            'FormattedPrice' => '$' . number_format($listPrice),
            'TransactionType' => $property['TransactionType'] ?? '',

            // Property Details (FIXED: Ensure numeric values)
            'BedroomsTotal' => (int) ($property['BedroomsTotal'] ?? 0),
            'BathroomsTotalInteger' => (int) ($property['BathroomsTotalInteger'] ?? 0),
            'LivingAreaRange' => $livingAreaRange,
            'PropertyType' => $property['PropertyType'] ?? '',
            'PropertySubType' => $property['PropertySubType'] ?? 'N/A',
            'LotSizeArea' => $property['LotSizeArea'] ?? 'N/A',
            'YearBuilt' => $property['YearBuilt'] ?? 'N/A',

            // Timing
            'DaysOnMarket' => (int) ($daysOnMarket ?? 0),
            'ListingContractDate' => $property['ListingContractDate'] ?? null,

            // Location
            'Latitude' => $latitude,
            'Longitude' => $longitude,
            'FullAddress' => $fullAddress,

            // Features & Amenities (ALL the data PropertyController has)
            'PublicRemarks' => $property['PublicRemarks'] ?? '',
            'InteriorFeatures' => $property['InteriorFeatures'] ?? [],
            'ParkingTotal' => (int) ($property['ParkingTotal'] ?? 0),
            'ParkingFeatures' => $property['ParkingFeatures'] ?? [],
            'GarageType' => $property['GarageType'] ?? 'None',
            'PoolFeatures' => $property['PoolFeatures'] ?? [],
            'AssociationAmenities' => $property['AssociationAmenities'] ?? [],
            'CommunityFeatures' => $property['CommunityFeatures'] ?? [],
            'LaundryFeatures' => $property['LaundryFeatures'] ?? [],
            'View' => $property['View'] ?? [],

            // Additional fields for display
            'GarageSpaces' => $property['GarageSpaces'] ?? 0,
            'Pool' => !empty($property['PoolFeatures']),
            'FireplaceYN' => $property['FireplaceYN'] ?? false,
            'Photos' => $property['Photos'] ?? [],

            // Your existing normalized fields
            'ExteriorFeatures' => $this->normalizeFieldValue($property['ExteriorFeatures'] ?? []),
            'WaterfrontFeatures' => $this->normalizeFieldValue($property['WaterfrontFeatures'] ?? []),
            'Heating' => $this->normalizeFieldValue($property['Heating'] ?? [], true),
            'Cooling' => $this->normalizeFieldValue($property['Cooling'] ?? [], true),
            'Roof' => $this->normalizeFieldValue($property['Roof'] ?? [], true),
        ];
    }

    private function normalizeFieldValue($value, bool $wrapScalar = false)
    {
        if (is_string($value)) {
            return array_filter(array_map('trim', explode(',', $value)));
        } elseif (!is_array($value)) {
            return $wrapScalar ? [$value] : [];
        }
        return $value;
    }

    private function extractMediaUrls(array $mediaRecords): array
    {
        $urls = [];
        foreach ($mediaRecords as $item) {
            if (is_object($item)) {
                $item = (array) $item;
            }
            $url = $item['MediaURL'] ?? $item['MediaUrl'] ?? $item['Url'] ?? null;
            if (!empty($url) && is_string($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $urls[] = $url;
            }
        }
        return array_values(array_unique(array_slice($urls, 0, 20)));
    }

    public function saveStatus(): void
    {
        Notification::make()
            ->success()
            ->title('Status updated')
            ->body('Property status saved.')
            ->send();
    }

    public function getInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($parts as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        return substr($initials, 0, 2);
    }
}
