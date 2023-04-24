<?php

namespace Database\Seeders;

use App\Models\ScoreResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScoreResultSeeder extends Seeder
{

    public function run()
    {
        ScoreResult::insert([
        [
        'school_code' => 'IMSU',
        'candidate_index' => '3A5',
        'course_average' => '50',
        'course_key' => 'A1.PMH',
        'course_header' => 'A1.PMH',
        'course_unit' => '0','year' => '','new' => '0'
        ],
        [
            'school_code' => 'LASUTH',
            'candidate_index' => 'A3-DD',
            'course_average' => '60',
            'course_key' => 'AA2',
            'course_header' => 'AA2',
            'course_unit' => '0',
            'year' => '', 'new' => '0'
        ]]);
    }
}
