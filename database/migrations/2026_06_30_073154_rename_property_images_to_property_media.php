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
        Schema::rename('property_images', 'property_media');
        Schema::table('property_media', function (Blueprint $table) {
            $table->renameColumn('image_url', 'file_url');
            $table->string('media_type', 20)->default('image')->after('property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_media', function (Blueprint $table) {
            $table->dropColumn('media_type');
            $table->renameColumn('file_url', 'image_url');
        });
        Schema::rename('property_media', 'property_images');
    }
};
