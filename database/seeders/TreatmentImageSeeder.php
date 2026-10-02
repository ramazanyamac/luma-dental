<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Illuminate\Database\Seeder;

class TreatmentImageSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            'general_checkup' => '01_appointment.png',
            'cleaning' => '02_appointment.png',
            'implants' => '03_appointment.png',
            'whitening' => '04_appointment.png',
            'orthodontics' => '05_appointment.png',
            'veneers' => '06_appointment.png',
            'root_canal' => '07_appointment.png',
            'other' => '08_appointment.png',
        ];

        foreach ($images as $code => $filename) {
            Treatment::query()
                ->where('code', $code)
                ->update([
                    'image_path' => 'images/dental/appointment/' . $filename,
                ]);
        }
    }
}
