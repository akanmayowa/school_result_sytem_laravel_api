<?php

namespace Database\Seeders;

use App\Models\CourseModule;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseModuleSeeder extends Seeder
{

    public function run()
    {
        CourseModule::insert([
            ['course_key' => 'A1-WSTRCYCLNG002', 'description' => 'Waste Recycling', 'credits' => 4, 'serial_number' => 2,
             'delete_status' => 0, 'practical' => 0, 'header_key' => 'A1-HK', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
           ['course_key' => 'A1-PHR002', 'description' => 'Public Health Record', 'credits' => 3, 'serial_number' => 7,
            'delete_status' => 1, 'practical' => 1, 'header_key' => 'A5-HK','created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ]);
    }
}



