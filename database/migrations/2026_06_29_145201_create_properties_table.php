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
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->unsignedInteger('user_id'); // foreign key INT
            
            // Basic Info
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('property_type', ['apartment', 'house', 'villa', 'land', 'shophouse', 'office', 'commercial']);
            
            // Address
            $table->string('address', 500);
            $table->string('district', 100)->nullable();
            $table->string('unit_number', 50)->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 10, 8)->nullable();
            
            // Room specs
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('area', 10, 2)->nullable();
            
            // Price
            $table->bigInteger('price');
            $table->bigInteger('price_per_sqm')->nullable();
            $table->bigInteger('monthly_price')->nullable();
            $table->integer('min_rent_period')->nullable();
            
            // Transaction type
            $table->enum('transaction_type', ['buy_sell', 'rent']);
            
            // Amenities & distance
            $table->string('amenities', 500)->nullable();
            $table->integer('distance_to_beach')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'active', 'sold', 'rented'])->default('draft');
            
            // Stats
            $table->integer('view_count')->default(0);
            $table->integer('contact_count')->default(0);
            
            $table->timestamps();

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users');

            // Indexes
            $table->index('transaction_type', 'idx_type');
            $table->index('property_type', 'idx_property_type');
            $table->index('status', 'idx_status');
            $table->index('price', 'idx_price');
            $table->index('monthly_price', 'idx_monthly_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
