<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 6;
        while($i <= 23) {
            DB::table('office_hours')->insert([
                'start_hour' => $i,
                'end_hour' => $i+1,
            ]);

            $i++;
        }
    }
}
