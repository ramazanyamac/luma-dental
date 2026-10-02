<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();

            $table->foreignId('treatment_id')
                ->constrained()
                ->restrictOnDelete();

            $table->timestampTz('starts_at')->index();
            $table->timestampTz('ends_at');

            $table->string('name', 120);
            $table->string('email', 254);
            $table->string('phone', 30);

            $table->date('birth_date')->nullable();
            $table->string('gender', 30)->nullable();
            $table->string('reason', 1000)->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_new_patient')->default(false);
            $table->string('locale', 2)->default('en');
            $table->string('status', 20)->default('confirmed');

            $table->timestamps();
        });

        DB::statement('
            ALTER TABLE appointments
            ADD CONSTRAINT appointments_valid_period
            CHECK (ends_at > starts_at)
        ');

        DB::statement("
            ALTER TABLE appointments
            ADD CONSTRAINT appointments_valid_status
            CHECK (status IN ('confirmed', 'cancelled'))
        ");

        DB::statement("
            ALTER TABLE appointments
            ADD CONSTRAINT appointments_no_overlap
            EXCLUDE USING gist (
                tstzrange(starts_at, ends_at, '[)') WITH &&
            )
            WHERE (status = 'confirmed')
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
