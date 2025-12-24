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
		Schema::create('push_notifications', function (Blueprint $table) {
		    $table->id();
		    $table->string('title');
			$table->string('href')->nullable();
		    $table->text('body');
			$table->integer('status')->default(1);
			$table->boolean('send_now')->default(1);
		    $table->datetime('scheduled_at')->nullable(); // when to send
		    $table->timestamp('sent_at')->nullable(); // when actually sent
		    $table->timestamps();
		});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
