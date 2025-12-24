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
        Schema::create('user_seen_notifications', function (Blueprint $table) {
            $table->id();
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('notification_id');
			$table->datetime('seen_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_seen_notifications');
    }
};
