<x-filament-panels::page>
    <div style="display:flex;flex-direction:column;gap:3rem;font-family:'Inter',sans-serif;
                color:var(--text-color,#111827);
                transition:background-color .3s,color .3s;">

        {{-- 🌟 HEADER --}}
        <div style="border-radius:1.25rem;overflow:hidden;
                    background:linear-gradient(120deg,#fff7ed,#fef9c3,#fef3c7);
                    box-shadow:0 8px 25px rgba(0,0,0,0.08);
                    padding:2.5rem 2rem;position:relative;">
            <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:1rem;">
                <h1 style="font-size:2.25rem;font-weight:800;color:#1f2937;margin:0;">
                    🏡 {{ $property['FullAddress'] ?? $property['UnparsedAddress'] ?? 'Property' }}
                </h1>

                {{-- 🏷️ Transaction Badge --}}
                @php
                    $transactionType = $property['TransactionType'] ?? 'For Sale';
                    $badgeColors = match (strtolower($transactionType)) {
                        'for sale' => ['#dcfce7', '#166534'],
                        'for rent' => ['#dbeafe', '#1e40af'],
                        'sold' => ['#fee2e2', '#991b1b'],
                        default => ['#fef3c7', '#92400e'],
                    };
                @endphp
                <span style="
                    background:{{ $badgeColors[0] }};
                    color:{{ $badgeColors[1] }};
                    font-weight:700;
                    padding:0.5rem 1.1rem;
                    font-size:0.9rem;
                    border-radius:9999px;
                    border:1px solid rgba(0,0,0,0.05);
                    box-shadow:0 2px 6px rgba(0,0,0,0.08);
                    letter-spacing:0.03em;">
                    {{ strtoupper($transactionType) }}
                </span>
            </div>

            {{-- DETAILS --}}
            <p style="margin-top:1rem;font-size:1rem;color:#374151;line-height:1.7;">
                📍 {{ $property['City'] ?? '-' }}, {{ $property['StateOrProvince'] ?? '' }} {{ $property['PostalCode'] ?? '' }}
                &nbsp;•&nbsp;
                <strong style="background:#fef3c7;color:#92400e;padding:0.25rem 0.6rem;border-radius:0.4rem;font-weight:700;">
                    MLS: {{ $property['ListingKey'] ?? '-' }}
                </strong>
                &nbsp;•&nbsp;
                {{ $property['PropertySubType'] ?? 'N/A' }}
                &nbsp;•&nbsp;
                Listed: {{ $property['ListingContractDate'] ?? 'N/A' }}
                &nbsp;•&nbsp;
                Days on Market: {{ $property['DaysOnMarket'] ?? 0 }}
            </p>

            {{-- PRICE --}}
            <div style="margin-top:1.5rem;text-align:right;">
                <div style="font-size:0.9rem;text-transform:uppercase;color:#6b7280;letter-spacing:0.05em;">List Price</div>
                <div style="font-size:2.75rem;font-weight:800;color:#b45309;text-shadow:1px 1px 3px rgba(0,0,0,0.1);">
                    {{ $property['FormattedPrice'] ?? '$0' }}
                </div>
            </div>
        </div>

        {{-- 🖼️ GALLERY --}}
        @php $imgs = array_values(array_filter($media ?? [])); @endphp
        @if (!empty($imgs))
            <div style="background:#fff;border-radius:1.25rem;padding:2rem;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
                <h2 style="font-size:1.5rem;font-weight:700;color:#1f2937;margin-bottom:1.5rem;
                           border-left:5px solid #facc15;padding-left:1rem;">📸 Photo Gallery</h2>
                <div class="masonry-grid">
                    @foreach ($imgs as $i => $src)
                        <a href="{{ $src }}" target="_blank" rel="noopener noreferrer"
                           style="display:inline-block;position:relative;margin-bottom:1rem;overflow:hidden;
                                  border-radius:1rem;transition:transform .3s,box-shadow .3s;">
                            <img src="{{ $src }}" alt="Photo {{ $i+1 }}" loading="lazy"
                                 style="width:100%;border-radius:1rem;object-fit:cover;transition:transform .4s;">
                            <div style="position:absolute;inset:0;
                                        background:linear-gradient(transparent,rgba(0,0,0,0.35));
                                        opacity:0;transition:opacity .3s;"></div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 🧾 KEY FACTS --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1.5rem;">
            @php
                $facts = [
                    ['🛏️ Beds', $property['BedroomsTotal'] ?? 'N/A'],
                    ['🛁 Baths', $property['BathroomsTotalInteger'] ?? 'N/A'],
                    ['📐 Sq Ft', $property['LivingAreaRange'] ?? 0],
                    ['🏗️ Year Built', $property['YearBuilt'] ?? 'N/A'],
                    ['🌳 Lot Size', $property['LotSizeArea'] ?? 'N/A'],
                    ['🚗 Parking', $property['ParkingTotal'] ?? 0],
                    ['🏠 Garage', $property['GarageType'] ?? 'None'],
                    ['🏊 Pool', !empty($property['Pool']) ? 'Yes' : 'No'],
                ];
            @endphp
            @foreach ($facts as [$label, $value])
                <div style="background:linear-gradient(145deg,#ffffff,#f9fafb);
                            border:1px solid #e5e7eb;border-radius:1rem;padding:1.5rem;
                            text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.05);
                            transition:transform .3s,box-shadow .3s;">
                    <div style="font-size:0.9rem;color:#6b7280;">{{ $label }}</div>
                    <div style="font-size:1.25rem;font-weight:700;color:#1f2937;">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        {{-- 📝 DESCRIPTION --}}
        @if (!empty($property['PublicRemarks']))
            <div style="background:#fff;border-radius:1.25rem;box-shadow:0 3px 10px rgba(0,0,0,0.05);padding:2rem;">
                <h3 style="font-size:1.4rem;font-weight:700;color:#1f2937;
                           border-left:5px solid #facc15;padding-left:1rem;margin-bottom:1.25rem;">📝 Description</h3>
                <p style="color:#374151;line-height:1.8;font-size:1rem;white-space:pre-wrap;">
                    {!! nl2br(e($property['PublicRemarks'])) !!}
                </p>
            </div>
        @endif
    </div>

    {{-- MASONRY --}}
    <style>
        .masonry-grid {
            column-count: 4;
            column-gap: 1rem;
        }
        .masonry-grid a:hover img { transform: scale(1.08); }
        .masonry-grid a:hover div { opacity: 1; }
        @media (max-width:1024px){.masonry-grid{column-count:3;}}
        @media (max-width:768px){.masonry-grid{column-count:2;}}
        @media (max-width:500px){.masonry-grid{column-count:1;}}
    </style>

    {{-- DARK MODE HANDLER --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.documentElement;
            const observer = new MutationObserver(() => {
                const dark = document.documentElement.classList.contains('dark');
                if (dark) {
                    document.body.style.setProperty('--page-bg', '#111827');
                    document.body.style.setProperty('--text-color', '#f3f4f6');
                } else {
                    document.body.style.setProperty('--page-bg', '#fafafa');
                    document.body.style.setProperty('--text-color', '#111827');
                }
            });
            observer.observe(root, { attributes: true, attributeFilter: ['class'] });
        });
    </script>
</x-filament-panels::page>
