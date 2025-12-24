<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
    {
        Schema::table('recyclings', function (Blueprint $table) {
            $table->unsignedBigInteger('bin_type_id')->nullable()->after('bin_id');

            // Optional: Add foreign key constraint
            $table->foreign('bin_type_id')->references('id')->on('bin_types')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('recyclings', function (Blueprint $table) {
            $table->dropForeign(['bin_type_id']);
            $table->dropColumn('bin_type_id');
        });
    }
};
