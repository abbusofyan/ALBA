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
        Schema::create('banned_lists', function (Blueprint $table) {
            $table->id();
			$table->unsignedInteger('user_id');
			$table->string('reason');
			$table->string('evidence_filename');
			$table->string('duration_days')->nullable();
			$table->unsignedInteger('moderator');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_lists');
    }
};
