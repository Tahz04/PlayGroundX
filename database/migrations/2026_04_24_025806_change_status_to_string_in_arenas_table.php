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
            $table->string('status')->default('active')->change();
        });
        
        // Convert existing boolean values to string
        DB::table('arenas')->where('status', '1')->update(['status' => 'active']);
        DB::table('arenas')->where('status', '0')->update(['status' => 'inactive']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arenas', function (Blueprint $table) {
            $table->boolean('status')->default(true)->change();
        });
    }
};
