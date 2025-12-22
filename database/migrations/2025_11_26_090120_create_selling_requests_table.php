<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('selling_requests', function (Blueprint $table) {
            $table->id();
            $table->string('sell_property_address');
            $table->string('sell_property_type');
            $table->integer('sell_property_sqft');
            $table->integer('sell_property_bedrooms');
            $table->integer('sell_property_bathrooms');
            $table->string('sell_property_condition');
            $table->string('sell_property_relocating');
            $table->integer('house_construct_year');
            $table->string('sell_property_service');
            $table->string('sell_property_mortgage_balance')->nullable();
            $table->string('sell_property_user_name');
            $table->string('sell_property_user_email');
            $table->string('sell_property_user_phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selling_requests');
    }
};
