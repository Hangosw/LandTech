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
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->unsignedInteger('property_id');
            $table->string('renter_phone', 20);
            $table->string('renter_name', 255)->nullable();
            $table->string('renter_email', 255)->nullable();
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('confirmed_at')->nullable();

            // Foreign Key
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');

            // Indexes
            $table->index('property_id', 'idx_property');
            $table->index('scheduled_date', 'idx_scheduled_date');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
