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
        Schema::table('guests', function (Blueprint $table) {
            $table->string('device_client')->nullable()->default(null);
            $table->string('brand_client')->nullable()->default(null);
            $table->string('model_client')->nullable()->default(null);
            $table->string('device_type')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['device_client', 'brand_client', 'model_client','device_type']);
        });
    }
};
