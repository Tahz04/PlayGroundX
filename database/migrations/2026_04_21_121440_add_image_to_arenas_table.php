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
        Schema::table('arenas', function (Blueprint $table) {
            // ✅ ĐÚNG: Thêm cột image vào bảng arenas
            $table->string('image')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arenas', function (Blueprint $table) {
            // ✅ ĐÚNG: Xóa cột image khi rollback
            $table->dropColumn('image');
        });
    }
};