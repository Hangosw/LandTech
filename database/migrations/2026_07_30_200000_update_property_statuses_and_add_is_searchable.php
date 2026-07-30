<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add is_searchable column if it doesn't exist
        if (!Schema::hasColumn('properties', 'is_searchable')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->boolean('is_searchable')->default(false)->after('status');
                $table->index('is_searchable', 'idx_is_searchable');
            });
        }

        // 2. Change status column definition to support new status keys
        // Note: DB::statement works for MySQL / SQLite.
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE properties MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'choduyet'");
        }

        // 3. Migrate existing status values in properties table to new unaccented keys
        DB::table('properties')->where('status', 'draft')->update(['status' => 'nhap']);
        DB::table('properties')->where('status', 'pending')->update(['status' => 'choduyet']);
        DB::table('properties')->whereIn('status', ['active', 'published'])->update(['status' => 'sansangchothue']);
        DB::table('properties')->whereIn('status', ['rented', 'sold'])->update(['status' => 'dachothue']);
        DB::table('properties')->where('status', 'hidden')->update(['status' => 'taman']);

        // Set default fallback for any unrecognized status
        DB::table('properties')->whereNotIn('status', [
            'nhap', 'choduyet', 'sansangchothue', 'dachothue', 'taman', 'hethantin', 'ngungkhaithac', 'bigovipham'
        ])->update(['status' => 'choduyet']);

        // 4. Automatically derive is_searchable values
        DB::table('properties')->update([
            'is_searchable' => DB::raw("CASE WHEN status = 'sansangchothue' THEN 1 ELSE 0 END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('properties', 'is_searchable')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropIndex('idx_is_searchable');
                $table->dropColumn('is_searchable');
            });
        }
    }
};
