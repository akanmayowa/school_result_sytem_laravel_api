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
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateResultTwo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $candidates = ScoreMarkerTwo::whereNew(1)
            ->with('courseModules', 'school:id,school_code,school_name')
            ->take(100)
            ->orderBy('exam_id', 'desc')
            ->groupBy('candidate_index')
            ->get();
        if ($candidates) {
            foreach ($candidates as $candidate) {
                $school_code =
                    $candidate->school_code ??
                    CandidateIndexing::whereCandidateIndex(
                        $candidate->candidate_index
                    )->first()->school_code;
                $course_unit = $candidate->courseModules->credits;

                $course_header = $candidate->course_header;
                $courses = CourseModule::whereHeaderKey($course_header)->get();

                $total_credit = $courses->sum('credits');

                foreach ($courses as $course) {
                    $score_marker_2 = ScoreMarkerTwo::whereCandidateIndex(
                        $candidate->candidate_index
                    )
                        ->whereCourseKey($course->course_key)
                        ->where('exam_id', $candidate->exam_id)
                        ->orderBy('exam_id', 'desc')
                        ->first();
                    if ($score_marker_2) {
                        $score_marker_1 = ScoreMarkerOne::whereCandidateIndex(
                            $candidate->candidate_index
                        )
                            ->whereCourseKey($course->course_key)
                            ->whereExamId($score_marker_2->exam_id)
                            ->first();

                        $total_score_2 = $score_marker_2->total_score;
                        $total_score_1 = $score_marker_1 ? $score_marker_1->total_score : 0;

                        $course_average =
                            $total_score_1 > 0
                                ? ($total_score_1 + $total_score_2) / 2
                                : $total_score_2;

                        ScoreResult::updateOrCreate(
                            [
                                'candidate_index' => $candidate->candidate_index,
                                'course_header' => $candidate->course_header,
                                'year' => $candidate->exam_id,
                                'course_key' => $course->course_key,
                            ],
                            [
                                'school_code' => $school_code,
                                'course_average' => $course_average,
                                'course_unit' => $course_unit,
                            ]
                        );
                    }
                }

                $resultss = ScoreResult::with('course')
                    ->whereCandidateIndex($candidate->candidate_index)
                    ->whereCourseHeader($candidate->course_header)
                    ->orderBy('year', 'desc')
                    ->get();

                if (count($resultss) > 0) {
                    $score_per_unit = 0;

                    if (count($resultss) > 1) {
                        $rs = [];
                        foreach ($resultss as $rslt) {
                            $rs[] = [
                                'course_average' => $rslt->course_average,
                                'course_key' => $rslt->course_key,
                                'course_unit' => $rslt->course_unit,
                                'year' => $rslt->year,
                                'candidate_index' => $rslt->candidate_index,
                                'credits' => optional($rslt->course)->credits,
                            ];
                        }

                        $course = array_column($rs, 'course_key');
                        $year = array_column($rs, 'year');

                        array_multisort($course, SORT_DESC, $year, SORT_DESC, $rs);
                        $resultss = Others::unique_multidim_array($rs, 'course_key');
                    }

                    foreach ($resultss as $result) {
                        if ($result['course_average']) {
                            $score_per_unit +=
                                $result['course_average'] *
                                ($result['credits'] ?? $result['course_unit']);
                        }
                    }
                    $weighted_score = $score_per_unit / $total_credit;
                    $gpa = $weighted_score / $total_credit;
                    $waheb70 = 0.7 * $gpa;

                    FinalResult::updateOrCreate(
                        [
                            'candidate_index' => $candidate->candidate_index,
                            'course_header' => $candidate->course_header,
                            'year' => $candidate->exam_id,
                        ],
                        [
                            'school_code' => $school_code,
                            'total_credit' => $total_credit,
                            'weighted_score' => number_format($weighted_score, 2),
                            'gpa' => number_format($gpa, 2),
                            'waheb70' => number_format($waheb70, 2),
                        ]
                    );
                }

                $sc_result = ScoreResult::whereCandidateIndex($candidate->candidate_index)
                    ->whereCourseHeader($candidate->course_header)
                    ->where('year', $resultss[0]['year'])
                    ->get();
                $malpractise_candidate = ExamOffender::whereStatus(1)
                    ->whereCandidateIndex($candidate->candidate_index)
                    ->whereRegistrationDate($resultss[0]['year'])
                    ->exists();
                $sc_absent = 0;
                $sc_failed = 0;
                $sc_passed = 0;
                $sc_malpractise = 0;

                if ($malpractise_candidate) {
                    $sc_malpractise = 1;
                } else {
                    foreach ($sc_result as $res) {
                        if ($res->course_average == 0) {
                            $sc_absent += 1;
                        } elseif (number_format($res->course_average) < 40) {
                            $sc_failed += 1;
                        } elseif (number_format($res->course_average) >= 40) {
                            $sc_passed += 1;
                        }
                    }
                }

                $fail = $sc_failed > 0;
                $absent = $sc_absent > 0;

                $incourse_query = CandidateIncourse::whereCandidateIndex(
                    $candidate->candidate_index
                )->whereExamId($resultss[0]['year']);

                if ($candidate->course_header != 'A2') {
                    if (!$incourse_query->exists()) {
                        $no_incourse = true;
                    } else {
                        $no_incourse = false;
                    }
                } else {
                    $no_incourse = false;
                }

                SchoolPerformance::updateOrCreate(
                    [
                        'candidate_index' => $candidate->candidate_index,
                        'exam_id' => $resultss[0]['year'],
                        'course_header' => $candidate->course_header,
                    ],
                    [
                        'school_code' =>
                                $school_code ??
                                IndexedCandidate::whereCandidateIndex(
                                    $candidate->candidate_index
                                )->first()->school_code,
                        'passed' => $absent || $fail ? 0 : 1,
                        'failed' => $absent ? 0 : ($fail ? 1 : 0),
                        'no_incourse' => $no_incourse ? 1 : 0,
                        'exam_id' => $resultss[0]['year'],
                        'malpractice' => $sc_malpractise == 1 ? 1 : 0,
                        'absent' => $absent ? 1 : 0,
                        'new' => 1,
                    ]
                );

                $query = SchoolPerformance::whereExamId($resultss[0]['year'])
                    ->whereSchoolCode($school_code)
                    ->whereCourseHeader($candidate->course_header)
                    ->get();
                $failed = $query->where('failed', "1")->count();
                $passed = $query->where("passed", "1")->count();
                $malpractice = $query->where("malpractice", "1")->count();
                $no_incourse = $query->where("no_incourse", "1")->count();
                $absent = $query->where("absent", "1")->count();
                $registered = $query
                    ->where('school_code', $school_code)
                    ->where('course_header', $candidate->course_header)
                    ->where('exam_id', $resultss[0]['year'])
                    ->count();

                SchoolPerformanceOverall::updateOrCreate(
                    [
                        'school_code' => $school_code,
                        'course_header' => $candidate->course_header,
                    ],
                    [
                        'school_name' => $candidate->school->school_name,
                        'failed' => $failed,
                        'passed' => $passed,
                        'malpractice' => $malpractice,
                        'no_incourse' => $no_incourse,
                        'registered' => $registered,
                        'absent' => $absent,
                        'exam_id' => $resultss[0]['year'],
                    ]
                );

                ScoreMarkerTwo::whereCandidateIndex($candidate->candidate_index)->update([
                    'new' => 0,
                ]);
            }
        }
    }
}
