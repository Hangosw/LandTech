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
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->unsignedInteger('property_id');
            $table->string('buyer_phone', 20);
            $table->string('buyer_name', 255)->nullable();
            $table->string('buyer_email', 255)->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'interested'])->default('new');
            $table->timestamp('created_at')->useCurrent();

            // Foreign Key
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');

            // Indexes
            $table->index('property_id', 'idx_property');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
