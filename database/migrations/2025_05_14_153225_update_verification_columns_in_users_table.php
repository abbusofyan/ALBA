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
		Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'verify_forgot_code')) {
                $table->dropColumn('verify_forgot_code');
            }
            if (Schema::hasColumn('users', 'verify_register_code')) {
                $table->dropColumn('verify_register_code');
            }

            $table->timestamp('verification_expires_at')->nullable()->after('verify_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['verification_expires_at']);

            $table->string('verify_forgot_code')->nullable();
            $table->string('verify_register_code')->nullable();
        });
    }
};
