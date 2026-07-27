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
        Schema::create('calendar', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->unsignedInteger('property_id');
            $table->date('date');
            $table->enum('status', ['available', 'booked', 'blocked'])->default('available');

            // Unique key
            $table->unique(['property_id', 'date'], 'unique_date');

            // Foreign Key
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');

            // Indexes
            $table->index('property_id', 'idx_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar');
    }
};
