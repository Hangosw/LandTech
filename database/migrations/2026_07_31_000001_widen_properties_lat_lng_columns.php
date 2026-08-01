<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * DECIMAL(10,8) chỉ chứa tối đa ±99.99999999 — không đủ cho kinh độ VN (~105–109).
     * Đổi sang DECIMAL(11,8): tối đa ±999.99999999.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('lat', 11, 8)->nullable()->change();
            $table->decimal('lng', 11, 8)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('lat', 10, 8)->nullable()->change();
            $table->decimal('lng', 10, 8)->nullable()->change();
        });
    }
};
