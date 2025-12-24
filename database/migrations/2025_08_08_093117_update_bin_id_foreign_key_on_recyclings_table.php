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
		Schema::table('recyclings', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['bin_id']);

            // Make the column nullable
            $table->unsignedBigInteger('bin_id')->nullable()->change();

            // Add foreign key with onDelete('set null')
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::table('recyclings', function (Blueprint $table) {
            // Drop the modified foreign key
            $table->dropForeign(['bin_id']);

            // Make the column NOT nullable again
            $table->unsignedBigInteger('bin_id')->nullable(false)->change();

            // Re-add the original foreign key with cascade
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('cascade');
        });
    }
};
