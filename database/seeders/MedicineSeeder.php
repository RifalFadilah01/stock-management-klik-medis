<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chunkSize = 1000;
        $totalRecords = 10000;
        $iterations = $totalRecords / $chunkSize;

        for ($i = 0; $i < $iterations; $i++) {
            Medicine::factory()->count($chunkSize)->create();
        }
    }
}
