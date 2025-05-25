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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_name');
            $table->string('iso2');
            $table->string('iso3');
            $table->string('top_level_domain');
            $table->string('fips');
            $table->integer('iso_numeric');
            $table->integer('geo_name_id')->nullable();
            $table->integer('e164');
            $table->string('phone_code')->nullable();
            $table->string('continent');
            $table->string('capital');
            $table->string('time_zone_in_capital');
            $table->string('currency')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
