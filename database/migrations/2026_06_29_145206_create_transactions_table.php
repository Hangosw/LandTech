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
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('buyer_id');
            $table->unsignedInteger('seller_id');
            $table->date('transaction_date')->nullable();
            $table->bigInteger('transaction_price')->nullable();
            $table->enum('status', ['negotiating', 'deposit', 'completed', 'cancelled'])->default('negotiating');
            $table->timestamp('created_at')->useCurrent();

            // Foreign Keys
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('buyer_id')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
