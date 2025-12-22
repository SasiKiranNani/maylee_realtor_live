<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages\CreateCity;
use App\Filament\Resources\CityResource\Pages\EditCity;
use App\Filament\Resources\CityResource\Pages\ListCities;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class CityResource extends Resource
{
    protected static ?string $model = City::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Cities';
    protected static ?string $navigationGroup = 'Properties';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('city')
                ->label('City (Canada)')
                ->searchable()
                ->required()
                ->options([])
                ->getSearchResultsUsing(function (string $search) {
                    $results = [];
                    foreach (static::searchCities($search) as $cleanCity => $data) {

                        // Encode city + coordinates as payload
                        $payload = base64_encode(json_encode([
                            'city' => $cleanCity,
                            'lat' => $data['lat'],
                            'lon' => $data['lon'],
                        ]));

                        $results[$payload] = $data['display_name'];
                    }
                    return $results;
                })
                ->getOptionLabelUsing(function ($value) {
                    if (!$value)
                        return '';
                    $decoded = json_decode(base64_decode($value), true);
                    return $decoded ? ($decoded['city'] . ', Ontario') : '';
                })
                ->reactive()
                ->afterStateHydrated(function ($component, $record) {
                    if ($record && $record->city && $record->latitude && $record->longitude) {
                        $component->state(base64_encode(json_encode([
                            'city' => $record->city,
                            'lat' => $record->latitude,
                            'lon' => $record->longitude,
                        ])));
                    }
                })
                ->afterStateUpdated(function ($state, callable $set) {

                    if (!$state) {
                        $set('latitude', null);
                        $set('longitude', null);
                        return;
                    }

                    $decoded = json_decode(base64_decode($state), true);

                    if ($decoded) {
                        // ALWAYS ensure West longitudes are negative
                        $lat = floatval($decoded['lat']);
                        $lon = floatval($decoded['lon']);

                        if ($lon > 0) {
                            $lon = -$lon;
                        }

                        $set('latitude', $lat);
                        $set('longitude', $lon);
                    }
                })
                ->dehydrateStateUsing(function ($state) {
                    if (!$state)
                        return null;
                    $decoded = json_decode(base64_decode($state), true);
                    return $decoded['city'] ?? null;
                }),

            Forms\Components\TextInput::make('latitude')
                ->readOnly()
                ->required(),

            Forms\Components\TextInput::make('longitude')
                ->readOnly()
                ->required(),

            Forms\Components\FileUpload::make('image')
                ->label('Image (h=264px × w=396 px)')
                ->image()
                ->directory('cities')
                ->visibility('public')
                ->columnSpanFull(),

            Forms\Components\RichEditor::make('description')
                ->columnSpanFull(),

            // Forms\Components\Toggle::make('is_home_active')
            //     ->label('Show on Home')
            //     ->default(false),

            // Forms\Components\Toggle::make('is_neighbourhood_active')
            //     ->label('Show on Neighbourhood')
            //     ->default(false),
        ]);
    }

    public static function searchCities(string $search): array
    {
        if (strlen(trim($search)) < 2)
            return [];

        $response = Http::withHeaders([
            'User-Agent' => 'SunseazCityAdmin/1.0 (' . env('NOMINATIM_CONTACT_EMAIL', 'company.sunseaz@gmail.com') . ')',
        ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => "$search, Ontario, Canada",
                    'countrycodes' => 'ca',
                    'format' => 'json',
                    'limit' => 15,
                    'addressdetails' => 1,
                ]);

        if (!$response->successful())
            return [];

        $options = [];

        foreach ($response->json() as $item) {

            // Pick correct city name
            $city = $item['address']['city']
                ?? $item['address']['town']
                ?? $item['address']['village']
                ?? null;

            if (!$city)
                continue;

            $cleanCity = trim(explode(',', $city)[0]);

            if (isset($options[$cleanCity])) {
                continue; // avoid duplicates
            }

            $lat = floatval($item['lat']);
            $lon = floatval($item['lon']);

            // Fix wrong positive longitudes
            if ($lon > 0)
                $lon = -$lon;

            $options[$cleanCity] = [
                'display_name' => $cleanCity . ', Ontario',
                'lat' => $lat,
                'lon' => $lon,
            ];
        }

        return $options;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('status'),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('city')->searchable(),
                Tables\Columns\ToggleColumn::make('is_home_active')->label('Home Active'),
                Tables\Columns\ToggleColumn::make('is_neighbourhood_active')->label('Neighbourhood Active'),
                Tables\Columns\TextColumn::make('latitude'),
                Tables\Columns\TextColumn::make('longitude'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }
}
