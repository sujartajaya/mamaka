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
        Schema::create('client_devices', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('mac_add')->nullable();
            $table->string('os_client')->nullable();
            $table->string('browser_client')->nullable();
            $table->string('device_client')->nullable()->default(null);
            $table->string('brand_client')->nullable()->default(null);
            $table->string('model_client')->nullable()->default(null);
            $table->string('device_type')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_devices');
    }
};
