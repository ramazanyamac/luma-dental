<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability_windows', function (Blueprint $table) {
            $table->id();

            $table->timestampTz('starts_at')->index();
            $table->timestampTz('ends_at');

            $table->timestamps();
        });

        DB::statement('
            ALTER TABLE availability_windows
            ADD CONSTRAINT availability_windows_valid_period
            CHECK (ends_at > starts_at)
        ');

        DB::statement("
            ALTER TABLE availability_windows
            ADD CONSTRAINT availability_windows_no_overlap
            EXCLUDE USING gist (
                tstzrange(starts_at, ends_at, '[)') WITH &&
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_windows');
    }
};
