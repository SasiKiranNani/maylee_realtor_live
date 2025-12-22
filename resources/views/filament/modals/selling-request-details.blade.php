<div style="font-family:'Segoe UI',sans-serif; background:#f5f6fa; padding:35px;">

    <!-- HEADER PANEL -->
    <div style="
        background:linear-gradient(135deg,#4c418c,#5a4db0);
        padding:32px;
        border-radius:18px;
        color:white;
        margin-bottom:32px;
        position:relative;
        overflow:hidden;
    ">
        <div style="position:absolute; top:-40px; right:-40px; width:180px; height:180px; background:rgba(255,255,255,0.08); border-radius:50%;"></div>
        <div style="position:absolute; top:40px; right:20px; width:110px; height:110px; background:rgba(255,255,255,0.06); border-radius:50%;"></div>

        <h1 style="margin:0; font-size:30px; font-weight:700; letter-spacing:0.5px;">
            Selling Request Overview
        </h1>
        <p style="margin-top:6px; opacity:0.85; font-size:14px;">
            Submitted {{ $record->created_at->diffForHumans() }}
        </p>
    </div>


    <!-- TOP INFO CARDS (SUBTLE & OFFICIAL) -->
    <div style="display:flex; gap:22px; flex-wrap:wrap; margin-bottom:28px;">

        <!-- CARD -->
        <div style="
            background:white;
            flex:1;
            min-width:260px;
            border-radius:16px;
            padding:22px;
            border:1px solid #e5e6eb;
        ">
            <div style="font-size:12px; color:#6c5ce7; text-transform:uppercase; letter-spacing:0.8px;">Owner Name</div>
            <div style="font-size:20px; font-weight:600; margin-top:6px;">
                {{ $record->sell_property_user_name }}
            </div>
        </div>

        <div style="
            background:white;
            flex:1;
            min-width:260px;
            border-radius:16px;
            padding:22px;
            border:1px solid #e5e6eb;
        ">
            <div style="font-size:12px; color:#0097e6; text-transform:uppercase; letter-spacing:0.8px;">Email</div>
            <div style="font-size:18px; font-weight:600; margin-top:6px; word-break:break-word;">
                {{ $record->sell_property_user_email }}
            </div>
        </div>

        <div style="
            background:white;
            flex:1;
            min-width:260px;
            border-radius:16px;
            padding:22px;
            border:1px solid #e5e6eb;
        ">
            <div style="font-size:12px; color:#00a86b; text-transform:uppercase; letter-spacing:0.8px;">Phone</div>
            <div style="font-size:20px; font-weight:600; margin-top:6px;">
                {{ $record->sell_property_user_phone }}
            </div>
        </div>
    </div>



    <!-- PROPERTY DETAILS -->
    <div style="
        background:white;
        padding:30px;
        border-radius:20px;
        border:1px solid #e7e8ef;
        margin-bottom:30px;
    ">
        <h2 style="margin:0 0 20px 0; font-size:24px; color:#4c418c; font-weight:700;">
            Property Specifications
        </h2>

        <div style="display:flex; flex-wrap:wrap; gap:28px;">

            <div style="flex:1; min-width:210px;">
                <div style="color:#7d7d7d; font-size:14px;">Type</div>
                <div style="font-size:20px; font-weight:600;">
                    {{ $record->sell_property_type }}
                </div>
            </div>

            <div style="flex:1; min-width:210px;">
                <div style="color:#7d7d7d; font-size:14px;">Area Size</div>
                <div style="font-size:20px; font-weight:600; color:#00a86b;">
                    {{ $record->sell_property_sqft }} sqft
                </div>
            </div>

            <div style="flex:1; min-width:210px;">
                <div style="color:#7d7d7d; font-size:14px;">Bedrooms</div>
                <div style="font-size:20px; font-weight:600; color:#6c5ce7;">
                    {{ $record->sell_property_bedrooms }}
                </div>
            </div>

            <div style="flex:1; min-width:210px;">
                <div style="color:#7d7d7d; font-size:14px;">Bathrooms</div>
                <div style="font-size:20px; font-weight:600; color:#d67b1f;">
                    {{ $record->sell_property_bathrooms }}
                </div>
            </div>

        </div>
    </div>



    <!-- ADDRESS & DETAILS -->
    <div style="display:flex; flex-wrap:wrap; gap:28px; margin-bottom:30px;">

        <!-- ADDRESS -->
        <div style="
            background:white;
            flex:1;
            min-width:300px;
            padding:26px;
            border-radius:18px;
            border:1px solid #e5e6eb;
        ">
            <h3 style="margin:0 0 12px 0; color:#4c418c; font-size:20px; font-weight:700;">
                Full Address
            </h3>
            <p style="
                background:#f3f3f8;
                padding:14px;
                border-radius:12px;
                font-size:14px;
                line-height:1.6;
            ">
                {{ $record->sell_property_address }}
            </p>
        </div>


        <!-- CONDITION -->
        <div style="
            background:white;
            flex:1;
            min-width:300px;
            padding:26px;
            border-radius:18px;
            border:1px solid #e5e6eb;
        ">
            <h3 style="margin:0 0 14px 0; color:#4c418c; font-size:20px; font-weight:700;">
                Property Condition
            </h3>

            <p style="margin:4px 0;"><strong>Condition:</strong> {{ $record->sell_property_condition }}</p>
            <p style="margin:4px 0;"><strong>Relocating:</strong> {{ $record->sell_property_relocating }}</p>
            <p style="margin:4px 0;"><strong>Year Built:</strong> {{ $record->house_construct_year }}</p>
            <p style="margin:4px 0;"><strong>Services Interested:</strong> {{ $record->sell_property_service }}</p>

            @if($record->sell_property_mortgage_balance)
            <p style="margin:4px 0;"><strong>Mortgage Balance:</strong> ${{ number_format($record->sell_property_mortgage_balance) }}</p>
            @endif
        </div>
    </div>



    <!-- IMAGE GALLERY (ELEGANT + SUBTLE) -->
    <div style="
        background:white;
        padding:30px;
        border-radius:20px;
        border:1px solid #e4e5ee;
    ">
        <h2 style="margin:0 0 22px 0; font-size:24px; color:#4c418c; font-weight:700;">
            Property Images
        </h2>

        <div style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:22px;
        ">
            @foreach($record->images as $image)

            <a href="{{ Storage::disk('public')->url($image->image_path) }}" target="_blank"
               style="
                   display:block;
                   height:180px;
                   border-radius:14px;
                   overflow:hidden;
                   border:1px solid #ddd;
                   background:#fafafa;
                   transition:0.3s ease;
               "
               onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 4px 18px rgba(0,0,0,0.15)'"
               onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'"
            >
                <img src="{{ Storage::disk('public')->url($image->image_path) }}"
                     style="width:100%; height:100%; object-fit:cover;">
            </a>

            @endforeach
        </div>
    </div>

</div>
