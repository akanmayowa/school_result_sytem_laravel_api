<?php

namespace Database\Seeders;

use App\Models\ExamOffender;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamOffenderSeeder extends Seeder
{
    public function run()
    {
        ExamOffender::insert([
            ['candidate_index' => '3A5','course_header' => 'A1','registration_date' => Carbon::now(),
                'exam_date' => Carbon::now(), 'exam_offence_id' => '1', 'duration' => '2',
                'comment' => 'non for now','school_code' => 'IMSU','status' => '1'
            ],
            [
                'candidate_index' => 'A3-DD','course_header' => 'A1','registration_date' => Carbon::now(),
                'exam_date' => Carbon::now(), 'exam_offence_id' => '2', 'duration' => '3',
                'comment' => 'suspension recommended','school_code' => 'LASUTH','status' => '0'
            ]
        ]);
    }
}
