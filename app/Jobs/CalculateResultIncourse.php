<?php

namespace App\Jobs;


use App\Helpers\Others;
use App\Models\CandidateIncourse;
use App\Models\CandidateIndexing;
use App\Models\CourseModule;
use App\Models\ExamOffender;
use App\Models\FinalResult;
use App\Models\SchoolPerformance;
use App\Models\SchoolPerformanceOverall;
use App\Models\ScoreMarkerOne;
use App\Models\ScoreMarkerTwo;
use App\Models\ScoreResult;
use App\Models\TrainingSchool;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateResultIncourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $candidates = CandidateIncourse::whereNew(1)
            ->take(50)
            ->orderBy('exam_id')
            ->groupBy('candidate_index')
            ->get();
        if ($candidates) {
            foreach ($candidates as $candidate) {
                $pass = false;
                $fail = false;

                $final_result = FinalResult::whereCandidateIndex(
                    $candidate->candidate_index
                )
                    ->where('year', $candidate->exam_id)
                    ->first();

                $course_average = $candidate->average_score;
                $cgpa_30 = 0.3 * $course_average;

                if ($final_result) {
                    $cgpa = $cgpa_30 + number_format($final_result->waheb70, 2);
                    $cgpa >= 2.0 ? ($pass = true) : ($fail = true);

                    $malpractise_candidate = ExamOffender::whereStatus(1)
                        ->whereCandidateIndex($candidate->candidate_index)
                        ->whereRegistrationDate($candidate->exam_id)
                        ->exists();

                    SchoolPerformance::updateOrCreate([
                        'candidate_index' => $candidate->candidate_index,
                        'exam_id' => $candidate->exam_id,
                        'course_header' => $candidate->course_header
                    ], [
                        'school_code' => $candidate->school_code,
                        'passed' => $pass ? 1 : 0,
                        'failed' => $fail ? 1 : 0,
                        'no_incourse' => 0,
                        'exam_id' => $candidate->exam_id,
                        'malpractice' => $malpractise_candidate ? 1 : 0,
                    ]);

                    $query = SchoolPerformance::whereSchoolCode($candidate->school_code)
                        ->whereExamId($candidate->exam_id)
                        ->whereCourseHeader($candidate->course_header)
                        ->get();
                    $failed = $query->where('failed', "1")->count();
                    $passed = $query->where("passed", "1")->count();
                    $malpractice = $query->where("malpractice", "1")->count();
                    $no_incourse = $query->where("no_incourse", "1")->count();
                    $registered = CandidateIndexing::where('month_yr_reg', $candidate->exam_id)
                        ->whereSchoolCode($candidate->school_code)
                        ->whereCourseHeader($candidate->course_header)
                        ->count();
                    $absent = $registered - ($failed + $passed + $malpractice + $no_incourse);

                    SchoolPerformanceOverall::updateOrCreate(
                        [
                            'school_code' => $candidate->school_code,
                            'course_header' => $candidate->course_header,
                        ],
                        [
                            'school_name' => TrainingSchool::whereSchoolCode(
                                $candidate->school_code
                            )->first()->school_name,
                            'failed' => $failed,
                            'passed' => $passed,
                            'malpractice' => $malpractice,
                            'no_incourse' => $no_incourse,
                            'registered' => $registered,
                            'absent' => $absent,
                            'exam_id' => $candidate->exam_id,
                        ]
                    );

                    $candidate->new = 0;
                    $candidate->save();
                }
            }
        }
    }
}
