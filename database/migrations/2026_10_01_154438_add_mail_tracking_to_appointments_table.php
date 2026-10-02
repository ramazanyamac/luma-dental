<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('mail_status', 20)->default('unknown');
            $table->timestampTz('mail_attempted_at')->nullable();
            $table->timestampTz('confirmation_sent_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['mail_status', 'mail_attempted_at', 'confirmation_sent_at']);
        });
    }
};
