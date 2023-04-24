<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{

    public function run()
    {
        Candidate::insert([
            [
                'school_code' =>'IMSU','candidate_index' => '3A5', 'course_header' => 'A1.PMH', 'exam_id' => '1098',
        'exam_date' => '2022-08-08 19:21:32','fresh' => '0','resist' => '1',
        'resist_after_absence' => '1','form_no' => '234/43', 'MPrevEntry' => '2', 'major' => 'science', 'operation_id' => '124',
        'registration_type' => 'fresh', 'reg_status'=>'pending', 'admin_comment'=>'No comment', 'school_comment'=> 'No comment', 'visible' => 0
            ],
                [
                   'school_code' =>'LASUTH','candidate_index' => 'A3-DD', 'course_header' => 'A2.SZ', 'exam_id' => '1098',
                    'exam_date' => '2022-08-08 19:21:32','fresh' => '0','resist' => '1',
                    'resist_after_absence' => '1','form_no' => '234/43', 'MPrevEntry' => '2', 'major' => 'science', 'operation_id' => '124',
                    'registration_type' => 'fresh', 'reg_status'=>'pending', 'admin_comment'=>'No comment', 'school_comment'=> 'No comment', 'visible' => 0
                ]
        ]);
    }
}
