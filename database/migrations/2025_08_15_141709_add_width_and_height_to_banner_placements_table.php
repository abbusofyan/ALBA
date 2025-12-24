<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_placements', function (Blueprint $table) {
            $table->integer('width')->nullable()->after('name'); // adjust 'after' column
            $table->integer('height')->nullable()->after('width');
        });
    }

    public function down(): void
    {
        Schema::table('banner_placements', function (Blueprint $table) {
            $table->dropColumn(['width', 'height']);
        });
    }
};
