<?php

use App\Absensi;
use Illuminate\Database\Seeder;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Absensi::create([
            'employee_id' => 1,
            'tanggal' => now()->toDateString(), // tanggal hari ini
            'jam' => '07:45:00', // sebelum jam 08
            'status' => 'Hadir',
        ]);

        Absensi::create([
            'employee_id' => 1,
            'tanggal' => now()->subDay()->toDateString(), // kemarin
            'jam' => '07:50:00',
            'status' => 'Hadir',
        ]);
    }
}
