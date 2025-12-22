<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('listing_key');
            $table->string('transaction_type');
            $table->date('date');
            $table->unsignedBigInteger('slot_booking_id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('message')->nullable();
            $table->boolean('consent')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('slot_booking_id')->references('id')->on('slot_bookings')->onDelete('cascade');
            // Indexes
            $table->index(['date', 'listing_key']);
            $table->index('slot_booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_bookings');
    }
};
