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
		Schema::table('bin_waste_types', function (Blueprint $table) {
            $table->dropColumn('waste_type');
            $table->unsignedInteger('waste_type_id')->after('bin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::table('bin_waste_types', function (Blueprint $table) {
            $table->dropColumn('waste_type_id');
            $table->string('waste_type')->after('bin_id');
        });
    }
};
