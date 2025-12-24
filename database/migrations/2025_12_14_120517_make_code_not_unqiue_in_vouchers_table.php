<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // 1️⃣ Drop FK (required by MySQL)
            $table->dropForeign(['reward_id']);

            // 2️⃣ Drop composite unique index
            $table->dropUnique('vouchers_reward_id_code_unique');

            // 3️⃣ Add normal index for FK
            $table->index('reward_id');

            // 4️⃣ Re-add FK
            $table->foreign('reward_id')
                ->references('id')
                ->on('rewards');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['reward_id']);
            $table->dropIndex(['reward_id']);

            $table->unique(['reward_id', 'code']);

            $table->foreign('reward_id')
                ->references('id')
                ->on('rewards');
        });
    }
};
