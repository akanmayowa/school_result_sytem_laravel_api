<?php

namespace Database\Seeders;

use App\Models\CandidateIncourse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateIncourseSeeder extends Seeder
{

    public function run()
    {
        CandidateIncourse::insert([
            [
             'candidate_index' => '124',
            'course_header' => 'AA2',
            'school_code' => 'LASUTH',
            'first_semester_score' => 4,
            'second_semester_score' => 3,
            'third_semester_score' => 2,
            'operator_id' => '123',
            'total_score' => "55 ",
            'average_score' => "22",
            'exam_id' => "2",
            'new' => 1,
            ],
                [
                    'candidate_index' => '123',
                    'course_header' => 'A1.PMH',
                    'school_code' => 'IMSU',
                    'first_semester_score' => 4,
                    'second_semester_score' => 3,
                    'third_semester_score' => 2,
                    'operator_id' => '126',
                    'total_score' => "55 ",
                    'average_score' => "22",
                    'exam_id' => "2",
                    'new' => 1,
                ]
        ]
        );
    }
}
