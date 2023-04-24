<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exam;

class ExamSeeder extends Seeder
{

    public function run()
    {
        Exam::insert([
            ['type' => 'First Marker', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'Second Marker', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'Incourse Assessment', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
