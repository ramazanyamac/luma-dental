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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('cancellation_mail_status', 20)->default('unknown');
            $table->timestampTz('cancellation_mail_attempted_at')->nullable();
            $table->timestampTz('cancellation_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['cancellation_mail_status', 'cancellation_mail_attempted_at', 'cancellation_sent_at']);
        });
    }
};
