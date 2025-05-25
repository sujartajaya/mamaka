<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            // $table->bigIncrements('id');
            // $table->uuid('uuid')->default(DB::raw('(UUID())'))->index();
            $table->uuid('id')->default(DB::raw('(UUID())'))->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('country_id')->constrained()->on('countries')->onDelete('cascade');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('mac_add')->nullable();
            $table->string('os_client')->nullable();
            $table->string('browser_client')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
