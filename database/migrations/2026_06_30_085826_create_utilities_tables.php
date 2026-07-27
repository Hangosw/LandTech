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
        // 1. Drop amenities column from properties
        if (Schema::hasColumn('properties', 'amenities')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('amenities');
            });
        }

        // 2. Create utilities table
        Schema::create('utilities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('description', 500)->nullable();
            $table->enum('category', ['amenity', 'location', 'facility', 'security'])->default('amenity');
            $table->string('icon_name', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Create property_utilities junction table
        Schema::create('property_utilities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('utility_id');
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('utility_id')->references('id')->on('utilities')->onDelete('cascade');
            
            $table->unique(['property_id', 'utility_id'], 'unique_property_utility');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_utilities');
        Schema::dropIfExists('utilities');
        
        Schema::table('properties', function (Blueprint $table) {
            $table->string('amenities', 500)->nullable();
        });
    }
};
