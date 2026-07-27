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
        Schema::create('user_oauth_providers', function (Blueprint $table) {
            $table->increments('id'); // INT PRIMARY KEY AUTO_INCREMENT
            $table->unsignedInteger('user_id'); // foreign key linking to users(id)
            $table->string('provider', 50); // 'google', 'zalo'
            $table->string('provider_id', 255); // ID from OAuth provider
            $table->string('provider_email', 255)->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Constraints & Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Unique combinations
            $table->unique(['user_id', 'provider'], 'unique_user_provider');
            $table->unique(['provider', 'provider_id'], 'unique_provider_id');

            // Indexes
            $table->index('user_id', 'idx_oauth_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_oauth_providers');
    }
};
