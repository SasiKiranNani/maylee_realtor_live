<?php

namespace App\Filament\Pages;

use App\Models\FakeProperty;
use App\Services\AmpPropertyService;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PropertyList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.property-list';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Property Listings';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Properties';

    public function getCities()
    {
        return \App\Models\City::where('status', true)
            ->pluck('city')
            ->sort()
            ->values()
            ->toArray();
    }

    // Exclusions for PropertyType
    const EXCLUDED_PROPERTY_TYPES = [
        'Commercial'
    ];

    // Exclusions for PropertySubType
    const EXCLUDED_PROPERTY_SUB_TYPES = [
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
        'Upper level',
        'Vacant Land',
        'Vacant Land Condo'
    ];

    // Exclusions for StandardStatus
    const EXCLUDED_STANDARD_STATUSES = [
        'Incomplete'
    ];

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ListingKey')
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('ListingKey')
                    ->label('MLS Key')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->ListingKey ?? $record['ListingKey'] ?? 'N/A'),
                TextColumn::make('City')->sortable()->searchable(),
                TextColumn::make('UnparsedAddress')->label('Address')->wrap(),
                TextColumn::make('ListPrice')
                    ->label('Price')
                    ->sortable()
                    ->money('USD'),
                TextColumn::make('PropertyType')->sortable(),
                TextColumn::make('PropertySubType')->sortable(),
                TextColumn::make('StandardStatus')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'Active',
                        'danger' => 'Expired',
                        'warning' => 'Pending',
                        'gray' => 'Closed',
                        'info' => 'Withdrawn',
                        'primary' => 'Coming Soon',
                    ]),
                TextColumn::make('TransactionType')->label('For')->sortable(),
            ])
            ->filters([
                SelectFilter::make('City')
                    ->label('City')
                    ->searchable()
                    ->multiple()
                    ->options(array_combine($this->getCities(), $this->getCities()))
                    ->placeholder('All Cities'),

                SelectFilter::make('PropertyType')
                    ->label('Property Type')
                    ->searchable()
                    ->multiple()
                    ->options(fn() => $this->getLookupOptions('PropertyType'))
                    ->placeholder('All Property Types'),

                SelectFilter::make('PropertySubType')
                    ->label('Property Sub Type')
                    ->searchable()
                    ->multiple()
                    ->options(fn() => $this->getLookupOptions('PropertySubType'))
                    ->placeholder('All Property Sub Types'),

                SelectFilter::make('StandardStatus')
                    ->label('Status')
                    ->multiple()
                    ->options(fn() => $this->getLookupOptions('StandardStatus'))
                    ->placeholder('All Statuses'),

                SelectFilter::make('TransactionType')
                    ->label('For')
                    ->multiple()
                    ->options(fn() => $this->getLookupOptions('TransactionType'))
                    ->placeholder('All Transaction Types'),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record): string => route('filament.admin.pages.property-view', ['id' => $record->ListingKey]))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([])
            ->defaultSort('ListPrice', 'desc')
            ->paginated([10, 20, 50, 100, 1000])
            ->query(\App\Models\User::query()->whereRaw('1 = 0'))
            ->defaultPaginationPageOption(10);
    }

    protected function paginateTableQuery(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $service = app(AmpPropertyService::class);

        // Use Filament's internal methods to get pagination state
        $currentPage = $this->getTablePage();
        $perPage = $this->getTableRecordsPerPage();

        // Get filter state from Filament
        $activeFilters = $this->tableFilters ?? [];
        $search = $this->getTableSearch();
        $sortColumn = $this->getTableSortColumn() ?? 'ListPrice';
        $sortDirection = $this->getTableSortDirection() ?? 'desc';

        // Log for debugging
        Log::info('Table State', [
            'page' => $currentPage,
            'perPage' => $perPage,
            'filters' => $activeFilters,
            'search' => $search,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection
        ]);

        $filter = $this->buildFilter($activeFilters, $search);
        $fields = 'ListingKey,City,UnparsedAddress,ListPrice,PropertyType,PropertySubType,StandardStatus,TransactionType';

        $result = $service->getPaginatedProperties(
            $fields,
            $perPage,
            $currentPage,
            $filter,
            $sortColumn,
            $sortDirection
        );

        Log::info('Property data received', [
            'count' => count($result['data']),
            'total' => $result['total'],
            'first_item' => $result['data'][0] ?? null,
            'all_listing_keys' => collect($result['data'])->pluck('ListingKey')->take(10)->toArray(),
            'all_cities' => collect($result['data'])->pluck('City')->take(10)->toArray(),
            'all_prices' => collect($result['data'])->pluck('ListPrice')->take(10)->toArray(),
        ]);

        $models = collect($result['data'])
            ->map(fn($item) => new FakeProperty($item))
            ->values();

        Log::info('Models created', [
            'count' => $models->count(),
            'first_model' => $models->first()?->toArray(),
            'first_model_listing_key_via_get' => $models->first()?->getKey(),
            'first_model_listing_key_via_attr' => $models->first()?->ListingKey,
            'first_model_listing_key_via_getattr' => $models->first()?->getAttribute('ListingKey'),
            'all_model_keys_via_pluck' => $models->pluck('ListingKey')->take(10)->toArray(),
            'all_model_keys_via_getkey' => $models->map(fn($m) => $m->getKey())->take(10)->toArray(),
        ]);

        return new LengthAwarePaginator(
            $models,
            $result['total'],
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );
    }

    private function getLookupOptions(string $lookupName): array
    {
        $service = app(AmpPropertyService::class);
        $options = $service->getLookupValues($lookupName)->unique()->sort()->values()->toArray();

        // Apply exclusions based on lookup name
        if ($lookupName === 'PropertyType') {
            $options = array_diff($options, self::EXCLUDED_PROPERTY_TYPES);
        } elseif ($lookupName === 'PropertySubType') {
            $options = array_diff($options, self::EXCLUDED_PROPERTY_SUB_TYPES);
        } elseif ($lookupName === 'StandardStatus') {
            $options = array_diff($options, self::EXCLUDED_STANDARD_STATUSES);
        }

        return array_combine($options, $options);
    }

    private function buildFilter(array $activeFilters, ?string $search): string
    {
        $filterConditions = [];

        // Hard exclusions for specified fields
        if (!empty(self::EXCLUDED_PROPERTY_TYPES)) {
            $escapedTypes = array_map(fn($v) => str_replace("'", "''", $v), self::EXCLUDED_PROPERTY_TYPES);
            $filterConditions[] = "not (PropertyType in ('" . implode("','", $escapedTypes) . "'))";
        }

        if (!empty(self::EXCLUDED_PROPERTY_SUB_TYPES)) {
            $escapedSubTypes = array_map(fn($v) => str_replace("'", "''", $v), self::EXCLUDED_PROPERTY_SUB_TYPES);
            $filterConditions[] = "not (PropertySubType in ('" . implode("','", $escapedSubTypes) . "'))";
        }

        if (!empty(self::EXCLUDED_STANDARD_STATUSES)) {
            $escapedStatuses = array_map(fn($v) => str_replace("'", "''", $v), self::EXCLUDED_STANDARD_STATUSES);
            $filterConditions[] = "not (StandardStatus in ('" . implode("','", $escapedStatuses) . "'))";
        }

        // Handle City filter
        $cityState = $activeFilters['City'] ?? [];
        $cities = $cityState['values'] ?? ($cityState['value'] ?? []);
        if (empty($cities)) {
            $cities = $this->getCities();
            Log::info('Applied default cities filter from database');
        } else {
            Log::info('Applied city filter', ['cities' => $cities]);
        }
        if (!is_array($cities)) {
            $cities = [$cities];
        }
        $escapedCities = array_map(fn($v) => str_replace("'", "''", $v), $cities);
        $filterConditions[] = "City in ('" . implode("','", $escapedCities) . "')";

        // Handle other filters
        $filterFields = ['PropertyType', 'PropertySubType', 'StandardStatus', 'TransactionType'];
        foreach ($filterFields as $field) {
            $state = $activeFilters[$field] ?? [];
            $values = $state['values'] ?? ($state['value'] ?? null);
            if (!empty($values)) {
                if (!is_array($values)) {
                    $values = [$values];
                }
                $escaped = array_map(fn($v) => str_replace("'", "''", $v), $values);
                if (count($values) === 1) {
                    $filterConditions[] = "{$field} eq '" . $escaped[0] . "'";
                } else {
                    $filterConditions[] = "{$field} in ('" . implode("','", $escaped) . "')";
                }
                Log::info("Applied {$field} filter", ['values' => $values]);
            }
        }

        // Handle search
        if ($search) {
            $escapedSearch = str_replace("'", "''", $search);
            $searchConditions = [
                "contains(ListingKey, '{$escapedSearch}')",
                "contains(City, '{$escapedSearch}')",
                "contains(UnparsedAddress, '{$escapedSearch}')",
                "contains(PropertyType, '{$escapedSearch}')"
            ];
            $filterConditions[] = '(' . implode(' or ', $searchConditions) . ')';
            Log::info('Applied search filter', ['search' => $search]);
        }

        $finalFilter = implode(' and ', $filterConditions);
        Log::info('Final OData filter', ['filter' => $finalFilter]);

        return $finalFilter;
    }
}
