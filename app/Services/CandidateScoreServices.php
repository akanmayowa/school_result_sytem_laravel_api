<?php /** @noinspection ALL */

namespace App\Services;

use App\Models\CandidateIncourse;
use App\Models\CandidateIndexing;
use App\Models\CourseHeader;
use App\Models\CourseModule;
use App\Models\ScoreMarkerTwo;
use App\Models\TrainingSchool;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponsesTrait;
use App\Repositories\UserRepository;
use App\Repositories\ScoreMarkerTwoRepository;
use App\Repositories\ScoreMarkerOneRepository;
use App\Repositories\ExamRepository;
use App\Repositories\ExamOffenderRepository;
use App\Repositories\CourseModuleRepository;
use App\Repositories\CourseHeaderRepository;
use App\Repositories\CandidateIndexingRepository;
use App\Repositories\CandidateInCourseRepository;
use App\Models\ScoreMarkerOne;
use App\Models\GeneralLog;
use App\Helpers\GeneralLogs;
use App\Helpers\Activities;
use App\Models\ExamOffender;
use App\Models\Candidate;


class CandidateScoreServices
{
    use ResponsesTrait;
    public ? CourseHeaderRepository $course_header_repository = null;
    public ? CourseModuleRepository $course_module_repository = null;
    public ? CandidateIndexingRepository $candidate_indexing_repository = null;
    public ? ExamRepository $exam_repository = null;
    public ? ScoreMarkerOneRepository $score_marker_one_repository = null;
    public ? ScoreMarkerTwoRepository $score_marker_two_repository = null;
    public ? UserRepository $user_repository = null;
    public ? ExamOffenderRepository $exam_offender_repository = null;
    public ? CandidateInCourseRepository $candidate_in_course_repository = null;

    public function __construct(CourseHeaderRepository $course_header_repository,CourseModuleRepository $course_module_repository,
     CandidateIndexingRepository $candidate_indexing_repository, ExamRepository $exam_repository, ScoreMarkerOneRepository $score_marker_one_repository,
     ScoreMarkerTwoRepository $score_marker_two_repository,UserRepository $user_repository, ExamOffenderRepository $exam_offender_repository,CandidateInCourseRepository $candidate_in_course_repository
    )

    {
            $this->course_header_repository = $course_header_repository;
            $this->course_module_repository = $course_module_repository;
            $this->candidate_indexing_repository = $candidate_indexing_repository;
            $this->exam_repository = $exam_repository;
            $this->score_marker_one_repository = $score_marker_one_repository;
            $this->score_marker_two_repository = $score_marker_two_repository;
            $this->user_repository  = $user_repository;
            $this->exam_offender_repository = $exam_offender_repository;
            $this->candidate_in_course_repository = $candidate_in_course_repository;
            $this->candidate_indexing = new CandidateIndexing();
            $this->candidate = new Candidate();
            $this->score_marker_one = new ScoreMarkerOne();
            $this->score_marker_two  =  new ScoreMarkerTwo();
            $this->candidate_in_course =  new CandidateIncourse();
    }


    public function scoreEntry(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'course_header' => 'required|string',
            'school_code' => 'required|string',
            'exam_type' => 'required|integer',
            'year' => 'required|string',
        ]);

        if($validator->fails()){ return $this->errorResponse( $validator->errors()); }
        $month = $this->course_header_repository->courseHeaderSelectedWithMonth();
        $yr = str_split(request()->year)[2] . str_split(request()->year)[3];
        $exam_id = $month . $yr;
        $exam = $this->exam_repository->checkExamType(request()->exam_type);
        $course_modules = $this->course_module_repository->courseModuleWithCourseHeader();
        $registration_date = date('y-m', strtotime($exam_id));
        $registration_year = explode('-', $registration_date)[0];
        $registration_month = explode('-', $registration_date)[1];
//        $concatenated_registration_year = $registration_month . str_split($registration_year)[2] . str_split($registration_year)[3];
        $candidate_in_course = $this->candidate_indexing_repository->candidateIndexWithCourseHeaderV1($exam_id);
         $all_candidates = $this->candidate_indexing_repository->candidateIndexWithCourseHeaderV2();
          $candidates = [];
          $complete_candidate_info = [];
           foreach ($all_candidates as $candidate)
           {
             $candidates[] = Str::upper($candidate->candidate_index);
             $complete_candidate_info[] = Str::upper($candidate->candidate_index) . ' - ' . Str::upper($candidate->first_name) . ' - ' . Str::upper($candidate->last_name);
          }
           $data = [
                     'course_header' => request()->course_header,
                    'exam' => $exam,
                    'candidates' => $candidates,
                    'course_modules' => $course_modules,
                    'exam_id' => $exam_id,
                    'exam_type' => request()->exam_type,
                    'candidate_in_course' => $candidate_in_course,
                    'candidate_full' => $complete_candidate_info,
                    'school' => request()->school_code,
          ];
           return $this->successResponse($data, "Candidate Score Entry successfully");
    }

    public function createCandidateScoreEntry()
    {
        $validator = Validator::make(request()->all(), [
            'school_code' => 'required|string|exists:training_schools,school_code',
            'exam_type' => 'required|integer',
            'exam_date' => 'required|string',
            'course_header' => 'required|string|exists:course_headers,header_key',
            'subject_code' => 'required_if:exam_type,1,2|string|exists:course_modules,course_key',
            'scores' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors());
        }

        $operator_id = auth()->user()->operator_id;
        $exam_type = request()->exam_type;
        $course_header = request()->course_header;
        $school_code = request()->school_code;

        DB::beginTransaction();

        $examIDHelper = new \App\Helpers\ExamID();
        $exam_id = $examIDHelper->getExamId($course_header);

        if ($exam_type == 3) {
            //  Incourse
            foreach (array(request()->scores) as $score) {
                $first_semester_score = $score['firstSem'];
                $second_semester_score = $score['secondSem'];
                $third_semester_score = $score['thirdSem'];
                $candidate_index = $score['candIndex'];
                $total_score = (int)$first_semester_score + (int)$second_semester_score + (int)$third_semester_score;
                $average_score = $total_score / 3;
                CandidateIncourse::updateOrCreate([
                    'course_header' => $course_header,
                    'candidate_index' => $candidate_index,
                    'school_code' => $school_code,
                ], [
                    'first_semester_score' => $first_semester_score,
                    'second_semester_score' => $second_semester_score,
                    'third_semester_score' => $third_semester_score,
                    'operator_id' => $operator_id,
                    'total_score' => $total_score,
                    'average_score' => $average_score,
                    'exam_id' => $exam_id,
                    'new' => 1
                ]);

                $this->CollectionOfLogVersion2($candidate_index, $first_semester_score, $second_semester_score, $third_semester_score, "Candidate Score Entry Created Successfully");
            }

        } else {
            // Markers 1 & 2
            foreach (request()->scores as $score) {
                 $q1 = $score['q1'];
                 $q2 = $score['q2'];
                 $q3 = $score['q3'];
                 $q4 = $score['q4'];
                 $q5 = $score['q5'];
                $candidate_index = $score['candIndex'];
                $model = $exam_type == 1 ? ScoreMarkerOne::query() : ScoreMarkerTwo::query();
                $model->updateOrCreate([
                    'course_header' => $course_header,
                    'course_key' => request()->subject_code,
                    'candidate_index' => $candidate_index,
                    'school_code' => $school_code,
                ], [
                    'q1' => $q1,
                    'q2' => $q2,
                    'q3' => $q3,
                    'q4' => $q4,
                    'q5' => $q5,
                    'total_score' => (int) $q1 + (int) $q2 + (int) $q3 + (int) $q4 + (int)$q5,
                    'exam_id' => $exam_id,
                    'operator_id' => $operator_id,
                    'status' => 1,
                    'new' => 1
                ]);

                $this->CollectionOflog($q1, $q2, $q3, $q4, $q5, $candidate_index, "Candidate Score Entry Created Successfully");
            }
        }

        DB::commit();

        return $this->successResponse([], 'Candidate score entry created successfully');
    }

    public function CollectionOflog($q1,$q2,$q3,$q4,$q5,$candidate_index,$message)
    {
        $score_array = ['q1' => $q1, 'q2' => $q2, 'q3' => $q3, 'q4' => $q4, 'q5' => $q5];
//        Activities::scoreLog($score_array, ($candidate_index), auth()->user()->operator_id,
//            request()->course_header, request()->subject_code, request()->exam_type, request()->exam_date);

        return response()->json($message);
    }

    public function CollectionOfLogVersion2($candidate_index,$first_semester_score,$second_semester_score,$third_semester_score,$message)
    {
        $score_array = ['q1' => $first_semester_score, 'q2' => $second_semester_score, 'q3' => $third_semester_score, 'q4' => 0, 'q5' => 0];
//        Activities::scoreLog($score_array, trim($candidate_index), Auth::id(), request()->course_header, request()->subject_code, request()->exam_type, request()->exam_date);
        return response()->json($message);
    }

    public function updateCandidateScoresEntry()
    {
        switch (request()->exam_type) {
            case 1:
                $marker_one = (new ScoreMarkerOne())->where('candidate_index', request()->candidate_index)
                                                    ->where('course_key', request()->subject_code)
                                                    ->where('exam_id', request()->exam_date)
                                                    ->where('course_header', request()->course_header)
                                                    ->first();
                if(empty($marker_one)){
                    return $this->errorResponse("Candidate ScoreMarker One Info doesnt Exist");
                }
                $marker_one->total_score = request()->q1 + request()->q2 + request()->q3 + request()->q4 + request()->q5;
                $marker_one->q1 = request()->q1;
                $marker_one->q2 = request()->q2;
                $marker_one->q3 = request()->q3;
                $marker_one->q4 = request()->q4;
                $marker_one->q5 = request()->q5;
                $marker_one->operator_id = auth()->user()->operator_id;
                $marker_one->new = 1;
                $marker_one->save();
                $score_array = ['q1' => request()->q1, 'q2' => request()->q2, 'q3' => request()->q3, 'q4' => request()->q4, 'q5' => request()->q5];
                Activities::scoreLog($score_array, trim($marker_one->candidate_index), auth()->user()->operator_id, $marker_one->course_key, $marker_one->course_header, 'Score marker 1', $marker_one->exam_id);
                GeneralLogs::createLog('updated candidate\'s first marker score', $marker_one->candidate_index);
                return $this->successResponse($marker_one,"candidate Score Updated Successfully");
                break;
            case 2:
            $marker_two = (new ScoreMarkerTwo())
                                            ->where('candidate_index', request()->candidate_index)
                                            ->where('course_key', request()->subject_code)
                                            ->where('exam_id', request()->exam_date)
                                            ->where('course_header', request()->course_header)
                                            ->first();
                if(empty($marker_two)){
                    return $this->errorResponse("Candidate ScoreMarker Two Info doesnt Exist");
                }
                $marker_two->total_score = request()->q1 + request()->q2 + request()->q3 + request()->q4 + request()->q5;
                $marker_two->q1 = request()->q1;
                $marker_two->q2 = request()->q2;
                $marker_two->q3 = request()->q3;
                $marker_two->q4 = request()->q4;
                $marker_two->q5 = request()->q5;
                $marker_two->operator_id = auth()->user()->operator_id;
                $marker_two->new = 1;
                $marker_two->save();
                $score_array = ['q1' => request()->q1, 'q2' => request()->q2, 'q3' => request()->q3, 'q4' => request()->q4, 'q5' => request()->q5];
                Activities::scoreLog($score_array, trim($marker_two->candidate_index), auth()->user()->operator_id, $marker_two->course_key, $marker_two->course_header, 'Score marker 2', $marker_two->exam_id);
                GeneralLogs::createLog( 'updated candidate\'s second marker score', $marker_two->candidate_index);
                return $this->successResponse($marker_two,"candidate Score Updated Successfully");
                break;
            case 3:
                $marker_three = (new CandidateIncourse())
                                                    ->where('candidate_index', request()->candidate_index)
                                                    ->where('course_header', request()->course_header)
                                                    ->where('exam_id', request()->exam_date)
                                                    ->first();
                if(empty($marker_three)){
                    return $this->errorResponse("Candidate Incourse Score Info doesnt Exist");
                }
                $marker_three->total_score = request()->first_semester_score + request()->second_semester_score + request()->third_semester_score;
                $marker_three->first_semester_score = request()->first_semester_score;
                $marker_three->second_semester_score = request()->second_semester_score;
                $marker_three->third_semester_score = request()->third_semester_score;
                $marker_three->average_score = $marker_three->total_score / 3;
                $marker_three->operator_id =  auth()->user()->operator_id;
                $marker_three->save();
                $score_array = ['total_score' => request()->q1,
                    'first_semester_score' => request()->first_semester_score,
                    'second_semester_Score' => request()->second_semester_score,
                    'third_semester_score' => request()->third_semester_score,
                    'average_score' => $marker_three->total_score / 3,
                    'operator_id' => auth()->user()->operator_id,
                ];
                return $this->successResponse($marker_three,"candidate Score Updated Successfully");                break;
        }
    }

    public function TotalCandidateScore()
    {
        $total_candidate_score =  (new ScoreMarkerOne())->sum('total_score');
        return $this->successResponse($total_candidate_score, "Total Candidate Score");
    }

    public function TotalCandidateScoreVersion2(): JsonResponse
    {
        $total_candidate_score =  (new ScoreMarkerOne())->where('school_code', auth()->user()->name)->sum('total_score');
        return $this->successResponse($total_candidate_score, "Total Candidate Score");
    }

    public function fetchAllCandidateScoreForScoreMarkerOne()
    {
        if(request()->input('school_code') AND request()->input('course_header')
                                               AND  request()->input('exam_year') AND request()->input('subject_code'))
            {
            $year_spliited = str_split(request()->input('exam_year'));
            $exam_year  = $year_spliited[2] . $year_spliited[3];
            $candidate_v2 = Candidate::
                                    where('school_code', request()->input('school_code'))
                                    ->where('course_header', request()->input('course_header'))
                                    ->where('exam_id', 'like', '%' . $exam_year)
                                    ->with('candidateIndexForCandidate:first_name,last_name,candidate_index,school_code');

              $result_one = $this->score_marker_one
                                        ->where('school_code', request()->input('school_code'))
                                        ->where('course_key', request()->input('subject_code'))
                                      ->where('exam_id', 'LIKE','%'.$exam_year)
                                      ->with('CourseModuleForScoreMarkerOne');

              $result_two =  $this->score_marker_two
                                   ->where('school_code', request()->input('school_code'))
                                    ->where('course_key', request()->input('subject_code'))
                                  ->where('exam_id', 'LIKE','%'.$exam_year)
                                  ->with('CourseModuleForScoreMarkerTwo');

              $result_three = $this->candidate_in_course
                  ->where('school_code', request()->input('school_code'))
                  ->where('course_header', request()->input('course_header'))
                                    ->with('CourseModuleForCandidateInCourse');
            $data = [
                     'candidate_details'   =>   $candidate_v2->paginate(10),
                     'candidate_score_one' =>   $result_one->paginate(10),
                     'candidate_score_two' =>   $result_two->paginate(10),
                     'candidate_incourse'  =>   $result_three->paginate(10)
            ];
                return $this->successResponse($data, "Total Candidate Score Successfully Selected");
            }
        else{
            return $this->successResponse(' ', "Candidate Score Serach Filter Not Completed Yet !");
        }
    }

    public function fetchAllCandidateScoreForResult()
    {
        $year_spliited = str_split(request()->input('exam_year'));
        $exam_year  = $year_spliited[2] . $year_spliited[3];
        if(request()->input('candidate_index') AND request()->input('exam_year')) {
            if(request()->input('filter') == 1){
                $candidate_result = $this->candidate
                                                ->where('candidate_index', request()->input('candidate_index'))
//                                                ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                                                ->with(['candidateIndexForCandidate:first_name,last_name,middle_name,school_code,candidate_index']);
                $result = $this->score_marker_one
                                                ->where('candidate_index', request()->input('candidate_index'))
                                                ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                                                ->with('CourseModuleForScoreMarkerOne');
                $data = [
                       'candidate_detaiils' => $candidate_result->get(),
                       'candidate_Score' => $result->get()
                ];
                return $this->successResponse($data, "Total Candidate Result Successfully Selected");
            }

            if(request()->input('filter') == 2){
                $candidate_result = $this->candidate
                                                ->where('candidate_index', request()->input('candidate_index'))
                                                ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                                                ->with(['candidateIndexForCandidate']);
                $result = $this->score_marker_two->where('candidate_index', request()->input('candidate_index'))
                                                ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                                                ->with('CourseModuleForScoreMarkerTwo');
                $data = [
                    'candidate_detaiils' => $candidate_result->get(),
                    'candidate_Score' => $result->get()
                ];
                return $this->successResponse($data, "Total Candidate Result Successfully Selected");
            }

            if(request()->input('filter') == 3){
                $candidate_result = $this->candidate
                                                ->where('candidate_index', request()->input('candidate_index'))
                                                ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                                                ->with(['candidateIndexForCandidate']);
                $result = $this->candidate_in_course->where('candidate_index', request()->input('candidate_index'))
                                                ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                                                ->with('CourseModuleForCandidateInCourse');
                $data = [
                    'candidate_detaiils' => $candidate_result->get(),
                    'candidate_Score' => $result->get()
                ];
                return $this->successResponse($data, "Total Candidate Result Successfully Selected");
            }

            if(!request()->input('filter')) {
                $candidate_result = $this->candidate
                    ->where('candidate_index', request()->input('candidate_index'))
                    ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                    ->with(['candidateIndexForCandidate']);
                $resultOne = $this->score_marker_one
                    ->where('candidate_index', request()->input('candidate_index'))
                    ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                    ->with('CourseModuleForScoreMarkerOne');
                $resultTwo = $this->score_marker_two->where('candidate_index', request()->input('candidate_index'))
                    ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                    ->with('CourseModuleForScoreMarkerTwo');
                $resultIncourse = $this->candidate_in_course->where('candidate_index', request()->input('candidate_index'))
                    ->where('exam_id', 'LIKE','%'.$exam_year.'%')
                    ->with('CourseModuleForCandidateInCourse');
                $data = [
                    'candidate_detaiils' => $candidate_result->get(),
                    'candidate_score_one' => $resultOne->get(),
                    'candidate_score_two' => $resultTwo->get(),
                    'candidate_score_incourse' => $resultIncourse->get()
                ];

                return $this->successResponse($data, "Total Candidate Result Successfully Selected");
            }
        }
            return $this->successResponse("","No Candidate Score Avaliable For The Search Filter");
    }

    public function fetchAllCourseHeaderNotDeleted()
    {
        $course_headers =  (new CourseHeader)->where('delete_status', 'no')->get();
        return response()->json(array('data' => $course_headers, 'message' => 'Course Header Selected SuccessFully..'));
    }

}
