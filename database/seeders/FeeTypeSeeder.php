<?php

namespace Database\Seeders;

use App\Models\FeeType;
use Illuminate\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeeType::firstOrCreate(
            ['name' => 'SPP Bulanan'],
            [
                'category' => 'SPP',
                'default_amount' => 250000,
                'is_recurring' => true,
                'recurrence' => 'bulanan',
                'is_active' => true,
            ]
        );

        FeeType::firstOrCreate(
            ['name' => 'Kegiatan Pramuka'],
            [
                'category' => 'kegiatan',
                'default_amount' => 50000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        FeeType::firstOrCreate(
            ['name' => 'Pendaftaran Siswa Baru'],
            [
                'category' => 'lain',
                'default_amount' => 1500000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );
    }
}
