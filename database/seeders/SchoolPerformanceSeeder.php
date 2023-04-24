<?php

namespace Database\Seeders;

use App\Models\SchoolPerformance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolPerformanceSeeder extends Seeder
{

    public function run()
    {
        SchoolPerformance::insert([
            [
                'school_code' => 'LASUTH', 'candidate_index' => 'A3-DD',
                'passed' => '1', 'absent' => '0', 'no_incourse' => '1',
                'failed' => '0', 'malpractice' => '1', 'exam_id' => '1018'
            ],
            [
                'school_code' => 'IMSU', 'candidate_index' => '3A5',
                'passed' => '0', 'absent' => '1', 'no_incourse' => '0',
                'failed' => '1', 'malpractice' => '1', 'exam_id' => '1018'
            ],
        ]);
    }
}
