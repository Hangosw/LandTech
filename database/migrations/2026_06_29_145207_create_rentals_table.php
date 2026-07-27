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
        Schema::create('rentals', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('renter_id');
            $table->unsignedInteger('owner_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->bigInteger('monthly_price')->nullable();
            $table->integer('duration_months')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamp('created_at')->useCurrent();

            // Foreign Keys
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('renter_id')->references('id')->on('users');
            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
