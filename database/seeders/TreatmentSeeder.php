<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        $treatments = [
            ['code' => 'general_checkup', 'duration_minutes' => 30],
            ['code' => 'cleaning', 'duration_minutes' => 30],
            ['code' => 'implants', 'duration_minutes' => 60],
            ['code' => 'whitening', 'duration_minutes' => 60],
            ['code' => 'orthodontics', 'duration_minutes' => 30],
            ['code' => 'veneers', 'duration_minutes' => 60],
            ['code' => 'root_canal', 'duration_minutes' => 60],
            ['code' => 'other', 'duration_minutes' => 30],
        ];

        foreach ($treatments as $index => $treatment) {
            Treatment::updateOrCreate(
                ['code' => $treatment['code']],
                [
                    'duration_minutes' => $treatment['duration_minutes'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
