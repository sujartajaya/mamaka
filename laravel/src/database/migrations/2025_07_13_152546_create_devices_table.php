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
        Schema::create('devices', function (Blueprint $table) {
            // $table->id();
            $table->uuid('id')->default(DB::raw('(UUID())'))->primary();
            $table->uuid('guest_id')->nullable();
            $table->string('mac_add')->nullable();
            $table->string('os_client')->nullable();
            $table->string('browser_client')->nullable();
            $table->timestamps();
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('mac_add');
            $table->dropColumn('os_client');
            $table->dropColumn('browser_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
        Schema::table('guests', function (Blueprint $table) {
            $table->string('mac_add')->nullable();
            $table->string('os_client')->nullable();
            $table->string('browser_client')->nullable();
        });
    }
};
