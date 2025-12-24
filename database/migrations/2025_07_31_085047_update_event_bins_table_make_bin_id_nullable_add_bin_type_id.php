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
		Schema::table('event_bins', function (Blueprint $table) {
            $table->unsignedBigInteger('bin_id')->nullable()->change();
            $table->unsignedBigInteger('bin_type_id')->nullable()->after('bin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::table('event_bins', function (Blueprint $table) {
            $table->unsignedBigInteger('bin_id')->nullable(false)->change();
            $table->dropColumn('bin_type_id');
        });
    }
};
