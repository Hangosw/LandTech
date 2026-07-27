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
        Schema::create('property_images', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->unsignedInteger('property_id');
            $table->string('image_url', 500);
            $table->integer('display_order')->nullable();
            $table->timestamp('created_at')->useCurrent(); // matching default

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
        Schema::dropIfExists('property_images');
    }
};
