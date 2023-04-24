<?php

namespace Database\Seeders;

use App\Models\FinalResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinalResultSeeder extends Seeder
{


    public function run()
    {
        FinalResult::insert([
            [
                'school_code' => "IMSU", 'candidate_index' => "3A5", 'course_header' => "A1",
                'total_credit' => '5',  'weighted_score' => '60',
                'gpa' => '3', 'waheb' => '50', 'year' => '2009'
            ],
            [

                'school_code' => "IMSU", 'candidate_index' => "A3-DD", 'course_header' => "A1",
                'total_credit' => '4',  'weighted_score' => '70',
                'gpa' => '3.5', 'waheb' => '80', 'year' => '2009'
            ],
        ]);
    }
}
