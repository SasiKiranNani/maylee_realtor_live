<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
            $table->boolean('is_home_active')->default(false)->after('status');
            $table->boolean('is_neighbourhood_active')->default(false)->after('is_home_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['image', 'is_home_active', 'is_neighbourhood_active']);
        });
    }
};
