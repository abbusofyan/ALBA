<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_placements', function (Blueprint $table) {
            $table->boolean('status')->default(1)->after('height'); // or after another column if you prefer
        });
    }

    public function down(): void
    {
        Schema::table('banner_placements', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
