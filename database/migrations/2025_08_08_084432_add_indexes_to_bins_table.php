<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
	 public function up()
	 {
	     Schema::table('bins', function (Blueprint $table) {
	         $table->index('status');
			 $table->index('code');
	         $table->index('bin_type_id');
	         $table->index('postal_code');
	         $table->index('address');
	     });

	     Schema::table('waste_types', function (Blueprint $table) {
	         $table->index('name');
	     });

	     Schema::table('bin_type_waste', function (Blueprint $table) {
	         $table->index('bin_type_id');
	         $table->index('waste_type_id');
	     });
	 }

	 public function down()
	 {
	     Schema::table('bins', function (Blueprint $table) {
	         $table->dropIndex(['status']);
			 $table->dropIndex(['code']);
	         $table->dropIndex(['bin_type_id']);
	         $table->dropIndex(['postal_code']);
	         $table->dropIndex(['address']);
	     });

	     Schema::table('waste_types', function (Blueprint $table) {
	         $table->dropIndex(['name']);
	     });

	     Schema::table('bin_type_waste', function (Blueprint $table) {
	         $table->dropIndex(['bin_type_id']);
	         $table->dropIndex(['waste_type_id']);
	     });
	 }
};
