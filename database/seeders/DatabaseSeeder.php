<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CandidateCategory;
use App\Models\CourseHeader;
use App\Models\FinalResult;
use App\Models\Nationality;
use App\Models\SchoolPerformance;
use App\Models\ScoreResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
//         $this->call(UserSeeder::class);
//         $this->call(StateSeeder::class);
//         $this->call(SchoolCategorySeeder::class);
//        $this->call(TrainingSchoolSeeder::class);
//         $this->call(CourseModuleSeeder::class);
//         $this->call(CourseHeaderSeeder::Class);
//         $this->call(CandidateCategorySeeder::class);
//         $this->call(CertificateEvaluationSeeder::class);
         $this->call(NationalitySeeder::class);
//         $this->call(GradeSeeder::class);
//        $this->call(CandidateIndexingSeeder::class);
//         $this->call(ExamSeeder::class);
//        $this->call(FinalResultSeeder::class);
//        $this->call(SchoolPerformanceSeeder::class);
//        $this->call(ExamOffenceSeeder::class);
//        $this->call(ExamOffenderSeeder::class);
//          $this->call(CandidateSeeder::class);
//        $this->call(ScoreResultSeeder::class);
//        $this->call(CandidateIncourseSeeder::class);
    }
}
