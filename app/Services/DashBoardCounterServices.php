<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\CandidateIndexing;
use App\Models\CourseHeader;
use App\Models\CourseModule;
use App\Models\ExamOffender;
use App\Models\SchoolResit;
use App\Models\ScoreMarkerOne;
use App\Models\TrainingSchool;
use App\Traits\ResponsesTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashBoardCounterServices
{
    Use ResponsesTrait;

    public function getRecordTotalCountAndSum(): \Illuminate\Http\JsonResponse
    {
       $counter_data = [
        'candidate_index_counter' => DB::table('candidate_indexings')->distinct('candidate_index')->count(),
        'candidate_counter' => DB::table('candidates')->distinct('candidate_index')->count(),
        'training_school_counter' => DB::table('training_schools')->count(),
        'total_candidate_score' =>    DB::table('score_marker_ones')->sum('total_score'),
        'exam_offender' => DB::table('exam_offenders')->distinct('candidate_index')->count(),
        'resit_candidates_counter' =>  DB::table('school_resits')->distinct('candidate_index')->count(),
        'course_header_counter' => DB::table('course_headers')->count(),
        'course_module_counter' => DB::table('course_modules')->count(),
        ];

        Cache::forever('counter_data', $counter_data);
        $counter_data = Cache::get('counter_data');
       return $this->successResponse($counter_data,'Counter Data Retrieval SuccessFully' );
    }

    public function getRecordTotalCountAndSumV2(): \Illuminate\Http\JsonResponse
    {
        $counter_data = [
            'candidate_index_counter' =>DB::table('candidate_indexings')->where('school_code', auth()->user()->operator_id)->distinct('candidate_index')->count(),
            'candidate_counter' => DB::table('candidates')->where('school_code', auth()->user()->operator_id)->distinct('candidate_index')->count(),
            'training_school_counter' => DB::table('training_schools')->where('school_code', auth()->user()->operator_id)->count(),
            'total_candidate_score' =>  DB::table('score_marker_ones')->where('school_code', auth()->user()->operator_id)->sum('total_score'),
            'exam_offender' => DB::table('exam_offenders')->where('school_code', auth()->user()->operator_id)->distinct('candidate_index')->count(),
            'resit_candidates_counter' =>  DB::table('school_resits')->where('school_code', auth()->user()->operator_id)->distinct('candidate_index')->count(),
            'course_header_counter' => DB::table('course_headers')->count(),
            'course_module_counter' => DB::table('course_modules')->count(),
        ];

        Cache::forever('counter_data', $counter_data);
        $counter_data = Cache::get('counter_data');
        return $this->successResponse($counter_data,'Counter Data Retrieval SuccessFully' );
    }

}
