<?php /** @noinspection ALL */

namespace App\Services;

use App\Helpers\ResultAnalysis;
use App\Models\Candidate;
use App\Models\CandidateIncourse;
use App\Models\CandidateIndexing;
use App\Models\CourseHeader;
use App\Models\CourseModule;
use App\Models\ExamOffender;
use App\Models\FinalResult;
use App\Models\SchoolPerformance;
use App\Models\SchoolResit;
use App\Models\ScoreResult;
use App\Models\State;
use App\Models\TrainingSchool;
use App\Repositories\CandidateIndexingRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\CourseHeaderRepository;
use App\Repositories\CourseModuleRepository;
use App\Repositories\ExamOffenderRepository;
use App\Repositories\FinalResultRepository;
use App\Repositories\SchoolResitRepository;
use App\Repositories\ScoreResultRepository;
use App\Repositories\StateRepository;
use App\Repositories\TrainingSchoolRepository;
use App\Traits\ResponsesTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Repositories\SchoolPerformanceRepository;
use DB;
use PhpParser\Node\Expr\Empty_;


class CandidateInformationRetrievalServices
{

    use ResponsesTrait;

    public function _construct(CandidateIndexingRepository $candidateIndexingRepository)
    {
        $this->candidateIndexingRepository = $candidateIndexingRepository;
        $this->candidateIndexing = new CandidateIndexing();
        $this->scoreResult = new ScoreResult();
    }

    public function retrievalOfACandidateInformationAsAFresher()
    {
        if (request()->input('exam_year') && request()->input('school_code') && request()->input('course_header')) {
            $course_header_information = (new CourseHeaderRepository(new CourseHeader()))->selectCourseHeader();

            if (empty($course_header_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }

            $check_if_school_exist = (new TrainingSchool())->where('school_code', request()->input('school_code'))->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $year_splitting = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $verified_candidates = (new CandidateIndexing())->select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id', 'first_name', 'last_name', 'middle_name')
                ->with('schoolResists')
                ->where('school_code', request()->input('school_code'))
                ->where('course_header', request()->input('course_header'))
                ->where('validate', '=', 'yes')
                ->where('visible', '=', 1)
                ->where('exam_id', 'like', '%' . $year_splitting)
                ->orderBy('candidate_index', 'asc')
                ->groupBy('candidate_index')
                ->get();


            if (empty($verified_candidates)) {
                return $this->errorResponse('Invalid Candidate Selected');
            }

            $data = [
                'exam_year' => strtoupper(date('F Y', strtotime('01-' . $course_header_information->month . '-' . request()->input('exam_year')))),
                'course_header_information' => $course_header_information,
                'school_information' => $check_if_school_exist->first(),
                'verified_candidates' => $verified_candidates,
                'serial_number' => 0
            ];
            return $this->successResponse($data, "Search Records Found Successfully");
        } else {
            return $this->successResponse([], "Search Filter Records Not Found");
        }
    }

    public function retrievalOfACandidateInformationAsAFresher_II()
    {
        if (request()->input('exam_year') && request()->input('course_header')) {
            $course_header_information = (new CourseHeaderRepository(new CourseHeader()))->selectCourseHeader();

            if (empty($course_header_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }

            $check_if_school_exist = (new TrainingSchool())->where('school_code', auth()->user()->operator_id)->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $year_splitting = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $verified_candidates = (new CandidateIndexing())->select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id')->with('schoolResists')
                ->where('school_code', auth()->user()->operator_id)
                ->where('course_header', request()->input('course_header'))
                ->orderBy('candidate_index', 'asc')
                ->where('validate', '=', 'yes')
                ->where('visible', '=', 0)
                ->where('exam_id', 'like', '%' . $year_splitting)
                ->groupBy('candidate_index')
                ->get();

            if (empty($verified_candidates)) {
                return $this->errorResponse('Invalid Candidate Selected');
            }

            $data = [
                'exam_year' => strtoupper(date('F Y', strtotime('01-' . $course_header_information->month . '-' . request()->input('exam_year')))),
                'course_header_information' => $course_header_information,
                'school_information' => $check_if_school_exist->first(),
                'verified_candidates' => $verified_candidates,
                'serial_number' => 0
            ];
            return $this->successResponse($data, "Search Records Found Successfully");
        } else {
            return $this->successResponse([], "Search Filter Records Not Found");
        }
    }

    public function retrievalOfAnIndexedCandidateInformation()
    {
        if (request()->input('school_code') && request()->input('course_header') && request()->input('exam_year')) {
            $year_splitting = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $course_information = (new CourseHeaderRepository(new CourseHeader()))->selectCourseHeader();
            $course_module = CourseModule::where('header_key', request()->input('course_header'))->get();
            if (empty($course_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }
            $check_if_school_exist = (new TrainingSchool())->with('state')->where('school_code', request()->input('school_code'))->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }
            $candidates_information = (new candidateIndexing)->select('id', 'candidate_index', 'first_name', 'last_name', 'middle_name', 'school_code', 'course_header', 'exam_id')->where('school_code', '=', request()->input('school_code'))
                ->where('course_header', '=', request()->input('course_header'))
                ->where('exam_id', 'like', '%' . $year_splitting)
                ->where('index_date', 'like', '%' . $year_splitting)
                ->groupBy('candidate_index')
                ->get();
//                ->where('visible', '=', 1)

            if (empty($candidates_information)) {
                return $this->errorResponse('Candidate Data Not Available For The Selected School and Course Header');
            }

            $data = [
                'candidates_information' => $candidates_information,
                'school_information' => $check_if_school_exist,
                'course_information' => $course_information,
                'serial_number' => 0,
                'exam_year' => request()->input('exam_year'),
                'course_module' => $course_module
            ];
            return $this->successResponse($data);
        }
        return $this->successResponse("", " Search Filter Records Not Found");
    }

    public function retrievalOfAnIndexedCandidateStatisticsInformation()
    {
        $course_header = request()->input('course_header');
        $exam_year = request()->input('exam_year');

        if ($course_header && $exam_year) {
            $year_splitting = str_split($exam_year)[2] . str_split($exam_year)[3];
            $course_information = (new CourseHeaderRepository(
                new CourseHeader()
            ))->selectCourseHeader();
            $course_module = CourseModule::where('header_key', $course_header)->get();
            if (empty($course_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }
            $candidates_information = CandidateIndexing::with('trainingSchools')
                ->select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id')
                ->where('course_header', '=', $course_header)
                ->where('exam_id', 'like', '%' . $year_splitting)
                ->groupBy('candidate_index')
                ->get()
                ->sortBy('school_code')
                ->groupBy('school_code');

             if (empty($candidates_information)) {
               return $this->errorResponse(
                 'Data Not Available For The Selected School and Course Header'
               );
             }

            $data = [
                'candidates_information' => $candidates_information,
                'course_information' => $course_information,
                'serial_number' => 0,
                'exam_year' => $exam_year,
                'course_module' => $course_module,
            ];

            return $this->successResponse($data);
        }
        return $this->successResponse("", " Search Filter Records Not Found");
    }

    public function retrievalOfAnIndexedCandidateInformation_II()
    {
        if (request()->input('course_header') && request()->input('exam_year')) {
            $year_splitting = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $course_information = (new CourseHeaderRepository(new CourseHeader()))->selectCourseHeader();
            $course_module = CourseModule::where('header_key', request()->input('course_header'))->get();
            if (empty($course_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }
            $check_if_school_exist = (new TrainingSchool())->with('state')->where('school_code', auth()->user()->operator_id)->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }
            $candidates_information = (new candidateIndexing)
                ->select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id')
                ->where('school_code', '=', auth()->user()->operator_id)
                ->where('course_header', '=', request()->input('course_header'))
                ->where('visible', '=', 1)
                ->where('exam_id', 'like', '%' . $year_splitting)
                ->orderBy('candidate_index', 'asc')
                ->groupBy('candidate_index')
                ->get();
            if (empty($candidates_information)) {
                return $this->errorResponse('Candidate Data Not Available For The Selected School and Course Header');
            }

            $data = [
                'candidates_information' => $candidates_information,
                'school_information' => $check_if_school_exist,
                'course_information' => $course_information,
                'serial_number' => 0,
                'exam_year' => request()->input('exam_year'),
                'course_module' => $course_module
            ];
            return $this->successResponse($data);
        }
        return $this->successResponse("", " Search Filter Records Not Found");
    }

    public function retrievalOfAResitingCandidateInformation()
    {
        if (request()->input('school_code') && request()->input('course_header') && request()->input('exam_year')) {
            $year = str_split(request()->exam_year, 2)[1];
            $course_header_information = (new CourseHeader())->where('header_key', request()->input('course_header'))->get();
            $course_module = CourseModule::where('header_key', request()->input('course_header'))->get();
            if (empty($course_header_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }
            $check_if_school_exist = (new TrainingSchool())->with('state')->where('school_code', request()->input('school_code'))->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $school_resit = (new SchoolResit())
                ->where('school_code', request()->input('school_code'))
                ->where('resit_header', request()->input('course_header'))
                ->where('exam_date', 'like', '%' . $year)
                ->with(['candidateIndexForResit'])
                ->groupBy(['candidate_index', 'exam_date'])
                ->get();

            if (empty($school_resit)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $data = [
                'course_header_information' => $course_header_information,
                'school_information' => $check_if_school_exist,
                'school_resit' => $school_resit,
                'course_details' => $course_module,
                'exam_year' => request()->input('exam_year'),
                'serial_number' => 0,
            ];
            return $this->successResponse($data, "Candidate Resisting Data Retrieved Successfully!");
        }
        return $this->successResponse("", " Search Filter Records Not Found");
    }

    public function retrievalOfAResitingCandidateInformation_II()
    {
        if (request()->input('course_header') && request()->input('exam_year')) {
            $year = str_split(request()->exam_year, 2)[1];
            $course_header_information = (new CourseHeader())->where('header_key', request()->input('course_header'))->get();
            $course_module = CourseModule::where('header_key', request()->input('course_header'))->get();
            if (empty($course_header_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }
            $check_if_school_exist = (new TrainingSchool())->with('state')->where('school_code', auth()->user()->operator_id)->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $school_resit = (new SchoolResit())
                ->where('school_code', auth()->user()->operator_id)
                ->where('resit_header', request()->input('course_header'))
                ->where('exam_date', 'like', '%' . $year)
                ->with(['candidateIndexForResit'])
                ->orderBy('candidate_index', 'asc')
                ->groupBy(['candidate_index', 'exam_date'])
                ->get();

            if (empty($school_resit)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $data = [
                'course_header_information' => $course_header_information,
                'school_information' => $check_if_school_exist,
                'school_resit' => $school_resit,
                'course_details' => $course_module,
                'exam_year' => request()->input('exam_year'),
                'serial_number' => 0,
            ];
            return $this->successResponse($data, "Candidate Resisting Data Retrieved Successfully!");
        }
        return $this->successResponse("", " Search Filter Records Not Found");
    }

    public function retrievalOfACandidateProjectStudiedInformation()
    {
        if (request()->input('school_code') && request()->input('exam_year')) {
            $splitted_year = str_split(request()->exam_year, 2);

            $check_if_school_exist = (new TrainingSchool())->where('school_code', request()->input('school_code'))->with('state')->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $candidates = (new Candidate())
                ->select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id')
                ->where('school_code', request()->input('school_code'))
                ->where('course_header', 'B5')
                ->where('exam_id', 'like', '%' . $splitted_year[1])
                ->where(function ($query) {
                    $query
                        ->where(function ($q) {
                            $q
                                ->where('registration_type', '=', 'fresh')
                                ->whereHas('candidateIndexing', function ($c) {
                                    $c->whereValidate('yes');
                                });
                        })
                        ->orWhere('registration_type', '=', 'resitall');
                })
                ->with('candidateIndexForCandidate')
                ->orderBy('candidate_index', 'asc')
                ->groupBy('candidate_index')
                ->get();

            if (empty($candidates)) {
                return $this->errorResponse('Candidate Data Not Available For The Selected School and Course Header');
            }

            $data = [
                'candidate' => $candidates,
                'exam_date' => request()->input('exam_year'),
                'school_information' => $check_if_school_exist,
                'serial_number' => 0,
                'exam_date' => "06/" . $splitted_year[1]
            ];
            return $this->successResponse($data, "Candidate Project of Study Data Retrieved Successfully!");
        } else {
            return $this->successResponse([], "Search Information Not Found !");
        }
    }

    public function retrievealOfACandidateSchoolPerformanceInformation()
    {
        if (request()->input('exam_year') && request()->input('course_header')) {
            $splitted_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $course_information = CourseHeader::where('header_key', request()->input('course_header'))->first();

            if (empty($course_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }

            $candidates = (new Candidate())
                ->select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id')
                ->where('school_code', request()->input('school_code'))
                ->where('course_header', request()->input('course_header'))
                ->where('exam_id', 'like', '%' . $splitted_year[1])
                ->orderBy('candidate_index', 'asc')
                ->groupBy('candidate_index', 'exam_id', 'course_header')
                ->get();

            if (empty($candidates)) {
                return $this->errorResponse('Candidate Data Not Available For The Selected School and Course Header');
            }

            $performance = DB::table('school_performance_overalls')
                ->where('exam_id', 'like', '%' . $splitted_year)
                ->where('course_header', request()->input('course_header'))
                ->orderBy('school_name')
                ->get();

            if (empty($performance)) {
                return $this->errorResponse('Performance Data Not Available For The Selected School and Course Header');
            }
            $data = [
                'candidates' => $candidates,
                'performance' => $performance,
                'course_header_information' => $course_information,
                'serial_number' => 1,
                'exam_year' => request()->input('exam_year'),
//                    'exam_year' => strtoupper(date('F Y', strtotime('01-' . $course_information->month . '-' . request()->input('exam_year')))),
            ];
            return $this->successResponse($data, 'Candidate School Perfomance Data Retrived Successfully!');
        } else {
            return $this->successResponse([], "Search Information Not Found !");
        }

    }

    public function retrievealOfAllSchoolExamPerformanceInformation()
    {
        if (request()->input('exam_year') && request()->input('course_header')) {
            $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $course_header = request()->input('course_header');
            $course_header_information = CourseHeader::where('header_key', $course_header)->first();
            if (empty($course_header_information)) {
                return $this->errorResponse('Invalid Course Header');
            }

            $registered_candidate = TrainingSchool::orderBy('school_code')->select('school_name')->withCount(['candidateForTrainingSchool'
            => function ($q) use ($course_header, $exam_year) {
                    $q->where('course_header', $course_header)
                        ->where('exam_id', 'like', '%' . $exam_year);
                }])->get();
            return $this->successResponse([$registered_candidate,
                'course_header' => $course_header_information,
                'exam_year' => request()->input('exam_year')]);
        }
        return $this->successResponse([], "Search filter Result Not Found");
    }

    public function candidateResultAnalysis()
    {
        if (request()->input('school_code') && request()->input('course_header') && request()->input('exam_year')
            && request()->input('result_type')) {
            $year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $school = (new TrainingSchoolRepository(new TrainingSchool()))->selectSchoolCode();

            if (empty($school)) {
                return $this->errorResponse('Invalid Training School ');
            }

            $school_name = $school->school_name;
            $state = State::where('id', $school->state_id)->first();
            $state_name = $state->name ?? null;
            $course_header_information = (new CourseHeader())->where('header_key', request()->course_header)->first();
            if (empty('course_headar')) {
                return $this->errorResponse('Invalid Course Header');
            }
            $course_description = $course_header_information->description;
            if (request()->result_type == 'waheb') {
                return $this->resultAnalysisForWahebV1($course_header_information, $year, $course_description, $state_name, $school_name);
            } else if (request()->result_type == 'waheb2') {
                return $this->resultAnalysisForWahebV2($course_header_information, $year, $course_description, $school_name);
            } elseif (request()->result_type == 'school') {
                return $this->resultAnalysisFiltedForSchool($course_header_information, $year, $course_description, $school_name);
            } elseif (request()->result_type == 'pass') {
                return $this->resultAnalysisFiltedForOnlyPassedCandidate($course_header_information, $year, $course_description);
            } else {
                return response()->json(['message' => 'Result Type Not Found, Please Select Between [waheb,waheb2,school,pass]'], 201);
            }
        }
        return $this->successResponse([], 'Search Filter Result Not Found');
    }

    public function resultAnalysisForWahebV1($course_header_information, $year, $course_description, $state_name, $school_name)
    {
        $course_keys = (new CourseModule())->where('header_key', request()->course_header)->get();
        if (empty($course_keys)) {
            return $this->successResponse('Course Key Empty');
        }

        if (request()->input('exam_year') == 2021 && request()->input('course_header') == 'AA2') {
            $filtered = $course_keys->filter(function ($course) {
                return $course->course_key != 'AA2.EVT399';
            });
            $course_keys = $filtered->all();
        }
        $header_items = [];
        foreach ($course_keys as $key) {
            $course_key = explode('.', $key->course_key)[1];

            $header_items[] = [
                'key' => $key->course_key,
                'course_key' => $course_key,
                'credits' => $key->credits
            ];
        };

        $final_results = FinalResult::with(['results' => function ($builder) use ($year, $course_header_information) {
            $builder->orderBy('year', 'DESC')->where('year', '=', $course_header_information->month . $year);
        }])
            ->where('school_code', request()->input('school_code'))
            ->where('course_header', request()->input('course_header'))
            ->where('year', '=', $course_header_information->month . $year)
            ->orderBy('candidate_index')
            ->groupBy('candidate_index')
            ->groupBy('course_header')
            ->get();

        $school_code = request()->input('school_code');
        $course_header = request()->input('course_header');
        $school_information = (new TrainingSchool())->where('school_code', $school_code)->with('state')->get();
        $course_header_details = (new CourseHeader())->where('header_key', $course_header)->get();

        $data = [
            'header_items' => $header_items,
            'final_results' => $final_results,
            'course_description' => $course_description,
            'school_name' => $school_name,
            'state_name' => $state_name,
            'exam_year' => request()->input('exam_year'),
        ];
        return $this->successResponse([$data ?? ' ', 'school_information' => $school_information ?? ' ', 'course_header_details' => $course_header_details ?? ' '], "Result Anaysis Retrieved Successfully! ");
    }

    public function resultAnalysisForWahebV2($course_header_information, $year, $school_name, $state_name)
    {
        $school_code = request()->input('school_code');
        $course_header = request()->input('course_header');
        $course_keys_details = (new CourseModule())->where('header_key', $course_header)->get();
        $school_information = (new TrainingSchool())->select("school_code", "index_code", "state_code", "school_name")
            ->where('school_code', $school_code)->with('state')->get();
        $course_header_details = (new CourseHeader())->where('header_key', $course_header)->get();
        $scores = FinalResult::where('course_header', $course_header)
            ->with(['results' => function ($query) use ($course_header, $year, $course_header_information) {
                $query->where('course_header', $course_header)
                    ->where('year', '=', $course_header_information->month . $year);
            },
                'incourse' => function ($query) use ($school_code, $year) {
                    $query->where('school_code', $school_code)
                        ->where('exam_id', 'like', '%' . $year);
                }])
            ->where('school_code', $school_code)
            ->where('year', '=', $course_header_information->month . $year)
            ->orderBy('candidate_index', 'asc')
            ->groupBy('candidate_index')
            ->get();

        if (empty($scores)) {
            $message = "Final Result Doesnt Exist For Selected Course Header and Exam Year";
            return response()->json(['message' => $message], 201);
        }

        foreach ($scores as $score) {
            $failed_courses = [];
            $resultsss = $score->results;
            if (count($resultsss) > 1) {
                $re = [];

                foreach ($resultsss as $res) {
                    $re[] = [
                        'course_average' => $res->course_average,
                        'course_key' => $res->course_key,
                        'course_unit' => $res->course_unit,
                        'year' => $res->year,
                        'candidate_index' => $res->candidate_index
                    ];
                }

                $courses = array_column($re, 'course_key');
                $years = array_column($re, 'year');
                $new_arr = array_multisort($courses, SORT_DESC, $years, SORT_DESC, $re);
                $resultsss = ResultAnalysis::CheckIfItemExistInAnArray($re, 'course_key');
            }


            foreach ($resultsss as $result) {
                if (number_format($result['course_average']) < 40) {
                    $failed_courses[] = explode('.', $result['course_key'])[1];
                }
            }

            if (!$score->incourse || $score->course_header == 'A2' || $score->course_header == 'A3' || $score->course_header == 'A7') {
                $candidate_index = $score->candidate_index;
                $first = 2.7;
                $second = 2.7;
                $third = 2.7;
                $cgpa = 2.7;
                $cgpa30 = 0.3 * $cgpa;
            } else {
                $candidate_index = $score->candidate_index;
                $first = $score->incourse->first_sem_score;
                $second = $score->incourse->second_sem_score;
                $third = $score->incourse->third_sem_score;
                $cgpa = $score->incourse->average_score;
                $cgpa30 = 0.3 * $cgpa;
            }
            $wahebgpa = $score->gpa;
            $waheb70 = 0.7 * $wahebgpa;
            $waheb30_70 = $cgpa30 + $waheb70;
            $diploma_class = '';
            if ($waheb30_70 >= 2.0) {
                $diploma_class = 'PASS';
            }
            if ($waheb30_70 >= 2.5) {
                $diploma_class = 'LOWER CREDIT';
            }
            if ($waheb30_70 >= 3.0) {
                $diploma_class = 'UPPER CREDIT';
            }
            if ($waheb30_70 >= 3.5) {
                $diploma_class = 'DISTINCTION';
            }


            if (count($failed_courses)) {
                $diploma_class = count($failed_courses) < 3 ? 'REFERRED IN: ' . implode(', ', $failed_courses) : 'REFERRED IN ALL PAPERS';
            }

            if (ExamOffender::whereStatus(1)->whereCandidateIndex($candidate_index)->where('registration_date', 'like', '%' . $year)->exists()) {
                $diploma_class = 'MALPRACTICE';
            }

            $results[] = [
                'candidate_index' => $candidate_index,
                'first' => $first,
                'second' => $second,
                'third' => $third,
                'cgpa' => number_format($cgpa, 2),
                'cgpa30' => number_format($cgpa30, 2),
                'wahebgpa' => number_format($wahebgpa, 2),
                'waheb70' => number_format($waheb70, 2),
                'waheb30_70' => number_format($waheb30_70, 2),
                'diploma_class' => $diploma_class,
                'exam_year' => $score->year,
                'course_description' => $course_keys_details,
            ];

        }
        return response()->json(['data' => $results ?? ' ', 'course_header_information' => $course_header_details ?? ' ', 'school_information' => $school_information ?? ' '], 201);
    }

    public function resultAnalysisFiltedForSchool($course_header_query, $year, $course_description, $school_name)
    {
        $course_header = request()->input('course_header');
        $course_header_details = (new CourseHeader())->where('header_key', $course_header)->first();
        $year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
        $school_code = request()->input('school_code');

        $school_results = FinalResult::with([
            'candidate:id,candidate_index,first_name,last_name,middle_name',
            'results' => function ($query) use (
                $course_header,
                $year,
                $course_header_query
            ) {
                $query
                    ->orderBy('course_key', 'asc')
                    ->whereRaw('BINARY course_header = ?', $course_header)
                    ->where('year', '=', $course_header_query->month . $year);
            },
            'offences' => function ($query) use ($course_header, $year, $school_code) {
                $query
                    ->with('singleOffence')
                    ->where('registration_date', 'like', '%' . $year)
                    ->whereSchoolCode($school_code)
                    ->whereCourseHeader($course_header)
                    ->whereStatus(1);
            },
        ])
            ->leftJoin('candidate_incourses as wic', function ($builder) use ($year) {
                $builder
                    ->on('wic.candidate_index', 'final_results.candidate_index')
                    ->where('exam_id', 'like', '%' . $year);
            })
            ->where('final_results.school_code', $school_code)
            ->where('final_results.course_header', '=', $course_header)
            ->where('final_results.year', '=', $course_header_query->month . $year)
            ->select(
                'final_results.*',
                'wic.first_semester_score',
                'wic.second_semester_score',
                'wic.third_semester_score',
                'wic.average_score'
            )
            ->orderBy('final_results.candidate_index')
            ->groupBy('final_results.candidate_index')
            ->get();

        $total_registered = count($school_results);

        $course_list = CourseModule::whereHeaderKey($course_header)->pluck(
            'course_key'
        );

        $r_count = [];
        $abs_count = 0;
        $malpractice_count = [];
        $x = 0;
        $acs = [];
        $total_passed = 0;

        $total_malpractice = 0;
        $total_referred = 0;

        $candidates = [];
        $sn = 0;
        foreach ($school_results as $school_result) {
            $rcount = [];
            $abscount = [];
            $f = 0;
            $failed_list = 0;
            $failed_courses = [];

            if (count($school_result->offences) > 0) {
                $total_malpractice++;
            } else {
                //  failed courses
                foreach ($course_list as $list) {
                    if (!$school_result->results->firstWhere('course_key', $list)) {
                        $abscount[] = $list;
                    }

                    if (
                        $school_result->results->firstWhere('course_key', $list) &&
                        $school_result->results->firstWhere('course_key', $list)
                            ->course_average < 40
                    ) {
                        $failed_courses[] = $list;
                        $failed_list += 1;
                    }
                }
            }

            $description = null;

            $exam_offender = $school_result->offences;

            if (count($exam_offender) > 0) {
                $exam_offender = $exam_offender[0];
                if ($exam_offender->exam_offence_id == '05') {
                    $description = optional($exam_offender->singleOffence)->description;
                } else {
                    $description = optional($exam_offender->singleOffence)->description . '. ' . optional($exam_offender->singleOffence)->punishment;
                }
            } else {
                if (count($abscount) > 0) {
                    if (count($abscount) === $course_list) {
                        $description = 'ABSENT IN ALL PAPERS';
                    } else {
                        $description = 'ABSENT IN ' . join(', ', $abscount);
                    }

                    $abs_count += 1;
                }

                if (count($failed_courses) > 0) {
                    if (count($failed_courses) > 3) {
                        $description =
                            ($description ? "$description AND " : '') . 'REFERRED IN ALL PAPERS';
                    } else {
                        $description =
                            ($description ? "$description AND " : '') .
                            'REFERRED IN ' .
                            JOIN(', ', $failed_courses);
                    }

                    $total_referred++;
                }

                if (count($failed_courses) == 0 && count($abscount) == 0) {
                    $first = $school_result->first_semester_score ?? 2.7;
                    $second = $school_result->second_semester_score ?? 2.7;
                    $third = $school_result->third_semester_score ?? 2.7;
                    $cgpa = $school_result->average_score ?? 2.7;
                    $cgpa30 = 0.3 * $cgpa;
                    $wahebgpa = $school_result->gpa;
                    $waheb70 = 0.7 * $wahebgpa;
                    $waheb30_70 = $cgpa30 + $waheb70;

                    if ($waheb30_70 >= 2.0) {
                        $total_passed += 1;

                        switch (true) {
                            case $waheb30_70 >= 3.5:
                                $description = 'DISTINCTION';
                                break;
                            case $waheb30_70 >= 3.0:
                                $description = 'UPPER CREDIT';
                                break;
                            case $waheb30_70 >= 2.5:
                                $description = 'LOWER CREDIT';
                                break;
                            default:
                                $description = "PASS";
                        }
                    }
                }
            }

            $candidates[] = [
                'candidate_index' => $school_result->candidate_index,
                'name' =>
                    $school_result->candidate->last_name .
                    ' ' .
                    $school_result->candidate->first_name .
                    ' ' .
                    $school_result->candidate->middle_name,
                'description' => $description,
            ];
        }

        $total_absent = $abs_count;

        $percent_passed = ($total_passed * 100) / $total_registered;
        $percent_referred = ($total_referred * 100) / $total_registered;
        $percent_absent = ($total_absent * 100) / $total_registered;
        $percent_malpractice = ($total_malpractice * 100) / $total_registered;

        $stats = [
            'passed' => $total_passed,
            'referred' => $total_referred,
            'absent' => $total_absent,
            'passed_percent' => number_format($percent_passed, 2),
            'referred_percent' => number_format($percent_referred, 2),
            'absent_percent' => number_format($percent_absent, 2),
            'total_registered' => $total_registered,
            'malpractice' => $total_malpractice,
            'malpractice_percent' => number_format($percent_malpractice, 2),
        ];

        $school_details = TrainingSchool::with('state')
            ->whereSchoolCode($school_code)
            ->first();
        $exam_period = $year;

        if (count($school_results) > 0) {
            $exam_period_array = str_split($school_results[0]->year);
            $exam_period =
                $exam_period_array[0] .
                $exam_period_array[1] .
                '/' .
                $exam_period_array[2] .
                $exam_period_array[3];
        }

        return $this->successResponse(['stats' => $stats, 'exam_date' => $exam_period, 'candidates' => $candidates, 'course_description' => $course_description, 'school_details' => $school_details], "Result Analysis Based On Waheb School Retrieved Successfully!");
    }

    public function resultAnalysisFiltedForOnlyPassedCandidate($course_header_query, $year, $course_description)
    {
        $course_header = request()->input('course_header');
        $course_header_details = (new CourseHeader())->where('header_key', $course_header)->first();
        $year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
        $school_code = request()->input('school_code');

        $school_results = FinalResult::with([
            'candidate:id,candidate_index,first_name,last_name,middle_name',
            'results' => function ($query) use (
                $course_header,
                $year,
                $course_header_query
            ) {
                $query
                    ->orderBy('course_key', 'asc')
                    ->whereRaw('BINARY course_header = ?', $course_header)
                    ->where('year', '=', $course_header_query->month . $year);
            },
            'offences' => function ($query) use ($course_header, $year, $school_code) {
                $query
                    ->with('singleOffence')
                    ->where('registration_date', 'like', '%' . $year)
                    ->whereSchoolCode($school_code)
                    ->whereCourseHeader($course_header)
                    ->whereStatus(1);
            },
        ])
            ->leftJoin('candidate_incourses as wic', function ($builder) use ($year) {
                $builder
                    ->on('wic.candidate_index', 'final_results.candidate_index')
                    ->where('exam_id', 'like', '%' . $year);
            })
            ->where('final_results.school_code', $school_code)
            ->where('final_results.course_header', '=', $course_header)
            ->where('final_results.year', '=', $course_header_query->month . $year)
            ->select(
                'final_results.*',
                'wic.first_semester_score',
                'wic.second_semester_score',
                'wic.third_semester_score',
                'wic.average_score'
            )
            ->orderBy('final_results.candidate_index')
            ->groupBy('final_results.candidate_index')
            ->get();

        $total_registered = count($school_results);

        $course_list = CourseModule::whereHeaderKey($course_header)->pluck(
            'course_key'
        );

        $r_count = [];
        $abs_count = 0;
        $malpractice_count = [];
        $x = 0;
        $acs = [];
        $total_passed = 0;

        $total_malpractice = 0;
        $total_referred = 0;

        $candidates = [];
        $sn = 0;
        foreach ($school_results as $school_result) {
            $rcount = [];
            $abscount = [];
            $f = 0;
            $failed_list = 0;
            $failed_courses = [];

            if (count($school_result->offences) > 0) {
                $total_malpractice++;
            } else {
                //  failed courses
                foreach ($course_list as $list) {
                    if (!$school_result->results->firstWhere('course_key', $list)) {
                        $abscount[] = $list;
                    }

                    if (
                        $school_result->results->firstWhere('course_key', $list) &&
                        $school_result->results->firstWhere('course_key', $list)
                            ->course_average < 40
                    ) {
                        $failed_courses[] = $list;
                        $failed_list += 1;
                    }
                }
            }

            $description = null;

            if (count($abscount) > 0) {
                if (count($abscount) === $course_list) {
                    $description = 'ABSENT IN ALL PAPERS';
                } else {
                    $description = 'ABSENT IN ' . join(', ', $abscount);
                }

                $abs_count += 1;
            }

            if (count($failed_courses) > 0) {
                if (count($failed_courses) > 3) {
                    $description =
                        ($description ? "$description AND " : '') . 'REFERRED IN ALL PAPERS';
                } else {
                    $description =
                        ($description ? "$description AND " : '') .
                        'REFERRED IN ' .
                        JOIN(', ', $failed_courses);
                }

                $total_referred++;
            }

            if (count($failed_courses) == 0 && count($abscount) == 0) {
                $first = $school_result->first_semester_score ?? 2.7;
                $second = $school_result->second_semester_score ?? 2.7;
                $third = $school_result->third_semester_score ?? 2.7;
                $cgpa = $school_result->average_score ?? 2.7;
                $cgpa30 = 0.3 * $cgpa;
                $wahebgpa = $school_result->gpa;
                $waheb70 = 0.7 * $wahebgpa;
                $waheb30_70 = $cgpa30 + $waheb70;

                if ($waheb30_70 >= 2.0) {
                    $total_passed += 1;

                    switch (true) {
                        case $waheb30_70 >= 3.5:
                            $description = 'DISTINCTION';
                            break;
                        case $waheb30_70 >= 3.0:
                            $description = 'UPPER CREDIT';
                            break;
                        case $waheb30_70 >= 2.5:
                            $description = 'LOWER CREDIT';
                            break;
                        default:
                            $description = "PASS";
                    }

                    $candidates[] = [
                        'candidate_index' => $school_result->candidate_index,
                        'name' =>
                            $school_result->candidate->last_name .
                            ' ' .
                            $school_result->candidate->first_name .
                            ' ' .
                            $school_result->candidate->middle_name,
                        'description' => $description,
                    ];
                }
            }
        }

        $total_absent = $abs_count;

        $percent_passed = ($total_passed * 100) / $total_registered;
        $percent_referred = ($total_referred * 100) / $total_registered;
        $percent_absent = ($total_absent * 100) / $total_registered;
        $percent_malpractice = ($total_malpractice * 100) / $total_registered;

        $stats = [
            'passed' => $total_passed,
            'referred' => $total_referred,
            'absent' => $total_absent,
            'passed_percent' => number_format($percent_passed, 2),
            'referred_percent' => number_format($percent_referred, 2),
            'absent_percent' => number_format($percent_absent, 2),
            'total_registered' => $total_registered,
            'malpractice' => $total_malpractice,
            'malpractice_percent' => number_format($percent_malpractice, 2),
        ];

        $school_details = TrainingSchool::with('state')
            ->whereSchoolCode($school_code)
            ->first();
        $exam_period = $year;

        if (count($school_results) > 0) {
            $exam_period_array = str_split($school_results[0]->year);
            $exam_period =
                $exam_period_array[0] .
                $exam_period_array[1] .
                '/' .
                $exam_period_array[2] .
                $exam_period_array[3];
        }

        return $this->successResponse(['stats' => $stats, 'exam_date' => $exam_period, 'candidates' => $candidates, 'course_description' => $course_description, 'school_details' => $school_details], "Result Analysis For Only Passed Candidate Retrieved Successfully");
    }

    public function candidateResultAnalysis_II()
    {
        if (request()->input('course_header') && request()->input('exam_year')
            && request()->input('result_type')) {
            $year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $school = (new TrainingSchool())->where('school_code', auth()->user()->operator_id)->first();

            if (empty($school)) {
                return $this->errorResponse('Invalid Training School ');
            }

            $school_name = $school->school_name;
            $state = State::where('id', $school->state_id)->first();

            $state_name = $state->name;
            $course_header_information = (new CourseHeader())->where('header_key', request()->course_header)->first();
            if (empty('course_headar')) {
                return $this->errorResponse('Invalid Course Header');
            }
            $course_description = $course_header_information->description;
            if (request()->result_type == 'waheb') {
                return $this->resultAnalysisForWahebV1_II($course_header_information, $year, $course_description, $state_name, $school_name);
            } else if (request()->result_type == 'waheb2') {
                return $this->resultAnalysisForWahebV2_II($course_header_information, $year, $course_description, $school_name);
            } elseif (request()->result_type == 'school') {
                return $this->resultAnalysisFiltedForSchool_II($course_header_information, $year, $course_description, $school_name);
            } elseif (request()->result_type == 'pass') {
                return $this->resultAnalysisFiltedForOnlyPassedCandidate_II($course_header_information, $year, $course_description);
            } else {
                return response()->json(['message' => 'Result Type Not Found, Please Select Between [waheb,waheb2,school,pass]'], 201);
            }
        }
        return $this->successResponse([], 'Search Filter Result Not Found');
    }

    public function resultAnalysisForWahebV1_II($course_header_information, $year, $course_description, $state_name, $school_name)
    {
        $course_keys = (new CourseModule())->where('header_key', request()->course_header)->get();
        if (empty($course_keys)) {
            return $this->successResponse('Course Key Empty');
        }

        if (request()->input('exam_year') == 2021 && request()->input('course_header') == 'AA2') {
            $filtered = $course_keys->filter(function ($course) {
                return $course->course_key != 'AA2.EVT399';
            });
            $course_keys = $filtered->all();
        }
        $header_items = [];
        foreach ($course_keys as $key) {
            $course_key = explode('.', $key->course_key)[1];
            $header_items[] = [
                'key' => $key->course_key,
                'course_key' => $course_key,
                'credits' => $key->credits
            ];
        };

        $final_results = FinalResult::with(['results' => function ($builder) use ($year, $course_header_information) {
            $builder->orderBy('year', 'DESC')->where('year', '=', $course_header_information->month . $year);
        }])
            ->where('school_code', auth()->user()->operator_id)
            ->where('course_header', request()->input('course_header'))
            ->where('year', '=', $course_header_information->month . $year)
            ->orderBy('candidate_index')
            ->groupBy('candidate_index')
            ->groupBy('course_header')
            ->get();

        $school_code = auth()->user()->operator_id;
        $course_header = request()->input('course_header');
        $school_information = (new TrainingSchool())->where('school_code', $school_code)->with('state')->get();
        $course_header_details = (new CourseHeader())->where('header_key', $course_header)->get();

        $data = [
            'header_items' => $header_items,
            'final_results' => $final_results,
            'course_description' => $course_description,
            'school_name' => $school_name,
            'state_name' => $state_name,
            'exam_year' => request()->input('exam_year'),
        ];

        return $this->successResponse([$data ?? ' ', 'school_information' => $school_information ?? ' ', 'course_header_details' => $course_header_details ?? ' '], "Result Anaysis Retrieved Successfully! ");
    }

    public function resultAnalysisForWahebV2_II($course_header_information, $year, $school_name, $state_name)
    {
        $school_code = auth()->user()->operator_id;
        $course_header = request()->input('course_header');
        $course_keys_details = (new CourseModule())->where('header_key', $course_header)->get();
        $school_information = (new TrainingSchool())->select("school_code", "index_code", "state_code", "school_name")->where('school_code', $school_code)->with('state')->get();
        $course_header_details = (new CourseHeader())->where('header_key', $course_header)->get();
        $scores = FinalResult::where('course_header', $course_header)
            ->with(['results' => function ($query) use ($course_header, $year, $course_header_information) {
                $query->where('course_header', $course_header)
                    ->where('year', '=', $course_header_information->month . $year);
            },
                'incourse' => function ($query) use ($school_code, $year) {
                    $query->where('school_code', $school_code)
                        ->where('exam_id', 'like', '%' . $year);
                }])
            ->where('school_code', $school_code)
            ->where('year', '=', $course_header_information->month . $year)
            ->groupBy('candidate_index')
            ->get();

        if (empty($scores)) {
            $message = "Final Result Doesnt Exist For Selected Course Header and Exam Year";
            return response()->json(['message' => $message], 201);
        }

        foreach ($scores as $score) {
            $failed_courses = [];
            $resultsss = $score->results;
            if (count($resultsss) > 1) {
                $re = [];

                foreach ($resultsss as $res) {
                    $re[] = [
                        'course_average' => $res->course_average,
                        'course_key' => $res->course_key,
                        'course_unit' => $res->course_unit,
                        'year' => $res->year,
                        'candidate_index' => $res->candidate_index
                    ];
                }

                $courses = array_column($re, 'course_key');
                $years = array_column($re, 'year');
                $new_arr = array_multisort($courses, SORT_DESC, $years, SORT_DESC, $re);
                $resultsss = ResultAnalysis::CheckIfItemExistInAnArray($re, 'course_key');
            }


            foreach ($resultsss as $result) {
                if (number_format($result['course_average']) < 40) {
                    $failed_courses[] = explode('.', $result['course_key'])[1];
                }
            }

            if (!$score->incourse || $score->course_header == 'A2' || $score->course_header == 'A3' || $score->course_header == 'A7') {
                $candidate_index = $score->candidate_index;
                $first = 2.7;
                $second = 2.7;
                $third = 2.7;
                $cgpa = 2.7;
                $cgpa30 = 0.3 * $cgpa;
            } else {
                $candidate_index = $score->candidate_index;
                $first = $score->incourse->first_sem_score;
                $second = $score->incourse->second_sem_score;
                $third = $score->incourse->third_sem_score;
                $cgpa = $score->incourse->average_score;
                $cgpa30 = 0.3 * $cgpa;
            }
            $wahebgpa = $score->gpa;
            $waheb70 = 0.7 * $wahebgpa;
            $waheb30_70 = $cgpa30 + $waheb70;
            $diploma_class = '';
            if ($waheb30_70 >= 2.0) {
                $diploma_class = 'PASS';
            }
            if ($waheb30_70 >= 2.5) {
                $diploma_class = 'LOWER CREDIT';
            }
            if ($waheb30_70 >= 3.0) {
                $diploma_class = 'UPPER CREDIT';
            }
            if ($waheb30_70 >= 3.5) {
                $diploma_class = 'DISTINCTION';
            }


            if (count($failed_courses)) {
                $diploma_class = count($failed_courses) < 3 ? 'REFERRED IN: ' . implode(', ', $failed_courses) : 'REFERRED IN ALL PAPERS';
            }

            if (ExamOffender::whereStatus(1)->whereCandidateIndex($candidate_index)->where('registration_date', 'like', '%' . $year)->exists()) {
                $diploma_class = 'MALPRACTICE';
            }

            $results[] = [
                'candidate_index' => $candidate_index,
                'first' => $first,
                'second' => $second,
                'third' => $third,
                'cgpa' => number_format($cgpa, 2),
                'cgpa30' => number_format($cgpa30, 2),
                'wahebgpa' => number_format($wahebgpa, 2),
                'waheb70' => number_format($waheb70, 2),
                'waheb30_70' => number_format($waheb30_70, 2),
                'diploma_class' => $diploma_class,
                'exam_year' => $score->year,
                'course_description' => $course_keys_details,
            ];

        }
        return response()->json(['data' => $results ?? ' ', 'course_header_information' => $course_header_details ?? ' ', 'school_information' => $school_information ?? ' '], 201);
    }

    public function resultAnalysisFiltedForSchool_II($course_header_information, $year, $course_description, $school_name)
    {
        $school_code = auth()->user()->operator_id;
        $course_header = request()->input('course_header');
        $course_header_details = (new CourseHeader())->where('header_key', $course_header)->get();
        $school_details = TrainingSchool::with('state')->where('school_code', $school_code)->first();
        $school_results = FinalResult::with(['candidate:id,candidate_index,first_name,last_name,middle_name',
            'results' => function ($query) use ($course_header, $year, $course_header_information) {
                $query->orderBy('course_key', 'asc')
                    ->whereRaw('BINARY course_header = ?', $course_header)
                    ->where('year', '=', $course_header_information->month . $year);
            }])
            ->leftJoin('candidate_incourses as wic', function ($builder) use ($year) {
                $builder->on('wic.candidate_index', 'final_results.candidate_index')
                    ->where('exam_id', 'like', '%' . $year);
            })
            ->with(['offences', 'offences.examOffence'])
            ->where('final_results.school_code', $school_code)
            ->where('final_results.course_header', '=', $course_header)
            ->where('final_results.year', '=', $course_header_information->month . $year)
            ->select('final_results.id', 'final_results.candidate_index', 'final_results.school_code', 'final_results.course_header', 'final_results.total_credit', 'final_results.weighted_score', 'gpa', 'waheb',
                'wic.first_semester_score', 'wic.second_semester_score', 'wic.third_semester_score', 'wic.average_score')
            ->orderBy('final_results.candidate_index')
            ->groupBy('final_results.candidate_index')
            ->paginate(13);

        $total_registered = count($school_results);
        $courses_lists = (new CourseModule())->where('header_key', $course_header)->get();
        $course_list = [];
        foreach ($courses_lists as $course) {
            $course_list[] = $course->course_key;
        }

        foreach ($school_results as $results) {
            $failed_courses = [];
            $final_passes = [];
            $total_withheld = 0;
            $referred_status = 0;
            $total_withheld = 0;
            $referred_status = 0;
            $total_malpractice = 0;
            $total_passed = 0;
            $total_failed = 0;
            $total_absent = 0;

            foreach ($results->results as $res) {
                if (number_format($res->course_average) < 40) {
                    $referred_status += 1;
                    $failed_courses[] = explode('.', $res->course_key)[1];
                }

                if (number_format($res->course_average) < 1) {
                    $total_absent += 1;
                }
            }
            $referred_class_II = ' ';
            $complete_subject_list = [];
            foreach ($course_list as $list) {
                if (ScoreResult::whereCandidateIndex($results->candidate_index)->whereCourseKey($list)->count() > 0) {
                    $subject = explode('.', $list)[1];
                    if ($subject != 'EVT399') {
                        $complete_subject_list[] = explode('.', $list)[1];
                    }
                }
            }

            if (count($complete_subject_list) > 3) {
                $referred_class_II = 'ABSENT IN ALL PAPERS';
            } else if (count($complete_subject_list) > 0) {
                if (count($failed_courses) > 0) {
                    if (count($failed_courses) > 3) {
                        $referred_class_II = 'ABSENT IN ' . implode(', ', $complete_subject_list) . ' AND REFERRED IN ALL PAPERS';
                    } else {
                        $referred_class_II = 'ABSENT IN ' . implode(', ', $complete_subject_list) . ' AND REFERRED IN: ' . implode(', ', $failed_courses);
                    }
                } else {
                    $referred_class_II = 'ABSENT IN ' . implode(', ', $complete_subject_list);
                }
            }

            if ($total_absent && count($course_list) == count($failed_courses)) {
                $referred_class_II = 'ABSENT IN ALL PAPERS';
            }

            $referred_class_II = strlen($referred_class_II) > 0 ? $referred_class_II : 'PASS';
            ((strpos($referred_class_II, 'PAS') !== false) || (strpos($referred_class_II, 'CREDIT') !== false) || (strpos($referred_class_II, 'DISTINCT') !== false)) ? $total_passed++ : '';
            strpos($referred_class_II, 'REFERRED') !== false ? $total_failed++ : '';
            strpos($referred_class_II, 'ABSENT') !== false ? $total_absent++ : '';
            strpos($referred_class_II, 'MALPRAC') !== false ? $total_malpractice++ : '';


            $exam_offender = ExamOffender::where('candidate_index', $results->candidate_index)->get();
            if (empty($exam_offender)) {
                $total_withheld = 0;
            } else {
                foreach ($exam_offender as $offender) {
                    if ($offender->exam_offence_id == '05') {
                        $total_withheld++;
                    }
                }
            }

            if (!$results->incourse || $results->course_header == 'A2' || $results->course_header == 'A3' || $results->course_header == 'A7') {
                $candidate_index = $results->candidate_index;
                $candidate_first_name = $results->candidate->first_name;
                $candidate_last_name = $results->candidate->last_name;
                $candidate_midle_name = $results->middle_name;
                $first = 2.7;
                $second = 2.7;
                $third = 2.7;
                $cgpa = 2.7;
                $cgpa30 = 0.3 * $cgpa;
            } else {
                $candidate_index = $results->candidate_index;
                $candidate_first_name = $results->candidate->first_name;
                $candidate_last_name = $results->candidate->last_name;
                $candidate_midle_name = $results->middle_name;
                $first = $score->incourse->first_sem_score;
                $second = $score->incourse->second_sem_score;
                $third = $score->incourse->third_sem_score;
                $cgpa = $score->incourse->average_score;
                $cgpa30 = 0.3 * $cgpa;
            }
            $wahebgpa = $results->gpa;
            $waheb70 = 0.7 * $wahebgpa;
            $waheb30_70 = $cgpa30 + $waheb70;
            $referred_class_I = '';

            if ($waheb30_70 >= 2.0) {
                $referred_class_I = 'PASS';
            }
            if ($waheb30_70 >= 2.5) {
                $referred_class_I = 'LOWER CREDIT';
            }
            if ($waheb30_70 >= 3.0) {
                $referred_class_I = 'UPPER CREDIT';
            }
            if ($waheb30_70 >= 3.5) {
                $referred_class_I = 'DISTINCTION';
            }

            if (count($failed_courses)) {
                $referred_class_I = count($failed_courses) > 3 ? 'REFERRED IN: ' . implode(', ', $failed_courses) : 'REFERRED IN ALL PAPERS';
            }

            if (ExamOffender::whereStatus(1)->whereCandidateIndex($results->candidate_index)->where('registration_date', 'like', '%' . $year)->exists()) {
                $referred_class_I = 'MALPRACTICE';
            }

            $collection_of_result[] = [
                'candidate_first_name' => $candidate_first_name,
                'candidate_last_name' => $candidate_last_name,
                'candidate_middle_ name' => $candidate_midle_name,
                'candidate_index' => $candidate_index,
                'cgpa30' => number_format($cgpa30, 2),
                'wahebgpa' => number_format($wahebgpa, 2),
                'waheb70' => number_format($waheb70, 2),
                'waheb30_70' => number_format($waheb30_70, 2),
                'referred_status_message_I' => $referred_class_I,
               'referred_status_messae_II' => $referred_class_II,
            ];

            $collection_of_statistics = [
                'total_registered' => $total_registered,
                'total_malpractice' => $total_malpractice,
                'total_withheld' => $total_withheld,
                'referred_status' => $referred_status,
                'total_passed' => $total_passed,
                'total_referred' => $total_failed,
                'total_absent' => $total_absent,
            ];

            $collection_of_statistics_II = [
                'percentage_of_total_passsed' => $total_passed * 100 / $total_registered,
                'percentage_of_total_referred' => $total_failed * 100 / $total_registered,
                'percentage_of_total_absent' => $total_absent * 100 / $total_registered,
                'percentage_of_total_malpractise' => $total_malpractice * 100 / $total_registered,
                'percentage_of_total_withheld' => $total_withheld * 100 / $total_registered,
            ];

            $data = [
//                    'school_results' => $school_results,
                'exam_year' => request()->input('exam_year'),
                'year' => $year,
                'course_list' => $course_list,
                'results' => $collection_of_result,
                'collection_of_statistics' => $collection_of_statistics,
                'collection_of_statistics_II' => $collection_of_statistics_II
            ];
        }

        return $this->successResponse([$data ?? ' ', 'school_details' => $school_details ?? ' ', 'course_header_information' => $course_header_details ?? ' '], "Result Analysis Based On Waheb School Retrieved Successfully!");
    }

    public function resultAnalysisFiltedForOnlyPassedCandidate_II($course_header_information, $year, $course_description)
    {
        $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];

        $exam_period = str_split(request()->input('exam_year'))[0] . str_split(request()->input('exam_year'))[1]
            . '/' . str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];

        $school_code = auth()->user()->operator_id;
        $course_header = request()->input('course_header');
        $course_header_details = (new CourseHeader())->where('header_key', $course_header)->first();
        $school_details = (new TrainingSchool())->with('state')->where('school_code', $school_code)->first();
        $final_result_candidate_indexs = FinalResult::get('candidate_index');
        $school_details = (new TrainingSchool())->with('state')->where('school_code', $school_code)->first();

        $school_results = FinalResult::with(['candidate:candidate_index,first_name,last_name',
            'results' => function ($query) use ($course_header, $year, $course_header_information) {
                $query->orderBy('course_key', 'asc')
                    ->where('year', '=', $course_header_information->month . $year);
            }])
            ->leftJoin('candidate_incourses as wic', function ($builder) use ($year) {
                $builder->on('wic.candidate_index', 'final_results.candidate_index')
                    ->where('exam_id', 'like', '%' . $year);
            })
            ->where('final_results.school_code', $school_code)
            ->where('final_results.course_header', '=', $course_header)
            ->where('final_results.year', '=', $course_header_information->month . $year)
            ->select('final_results.candidate_index',
                'final_results.total_credit', 'final_results.weighted_score', 'final_results.gpa', 'final_results.waheb',
                'wic.first_semester_score', 'wic.second_semester_score', 'wic.third_semester_score', 'wic.average_score')
            ->groupBy('final_results.candidate_index')
            ->paginate(15);

        $total_registered = count($school_results);
        $courses_lists = (new CourseModule())->where('header_key', $course_header)->first();

        if (empty($courses_lists)) {
            return response()->json(['message' => 'Selected Course Header Has No List Of Subjects'], 201);
        }

        $course_list = [];
        foreach ($courses_lists->all() as $course) {
            $course_list[] = $course->course_key;
        }

        $r_count = [];
        $abs_count = [];
        $malpractice_count = [];
        $x = 0;
        $acs = [];
        $final_passes = [];
        $total_malpractice = 0;
        $total_referred = 0;

        foreach ($school_results as $school_result) {
            $failed_list = 0;
            $referred_status = 0;
            $failed_courses = [];

            foreach ($school_result->results as $res) {
                if (number_format($res->course_average) < 40) {
                    $referred_status += 1;
                    $failed_list += 1;
                    $failed_courses[] = explode('.', $res->course_key)[1];
                }
            }

            if ($referred_status > 1) {
                $final_passes[] = $school_result->id;
            }

            $rcount = [];
            $abscount = [];
            $f = 0;
            if ((new ExamOffender())->where('candidate_index', $school_result->candidate_index)->count() > 0) {
                $total_malpractice++;
            } else {
                $scores_list = ScoreResult::where('candidate_index', $school_result->candidate_index)->with('courseScoreResults')->get();
                foreach ($scores_list as $score_list) {
                    if ($score_list) {
                        $total_referred++;
                    }
                }
                if (!$school_result->incourse || $school_result->course_header == 'A2' || $school_result->course_header == 'A3' ||
                    $school_result->course_header == 'A7') {
                    $candidate_index = $school_result->candidate_index;
                    $candidate_first_name = $school_result->candidate->first_name;
                    $candidate_last_name = $school_result->candidate->last_name;
                    $candidate_midle_name = $school_result->middle_name;
                    $first = 2.7;
                    $second = 2.7;
                    $third = 2.7;
                    $cgpa = 2.7;
                    $cgpa30 = 0.3 * $cgpa;
                } else {
                    $candidate_index = $school_result->candidate_index;
                    $candidate_first_name = $school_result->candidate->first_name;
                    $candidate_last_name = $school_result->candidate->last_name;
                    $candidate_midle_name = $school_result->middle_name;
                    $first = $school_result->incourse->first_sem_score;
                    $second = $school_result->incourse->second_sem_score;
                    $third = $school_result->incourse->third_sem_score;
                    $cgpa = $school_result->incourse->average_score;
                    $cgpa30 = 0.3 * $cgpa;
                }
                $wahebgpa = $school_result->gpa;
                $waheb70 = 0.7 * $wahebgpa;
                $waheb30_70 = $cgpa30 + $waheb70;
                $referred_class_I = '';

                if ($waheb30_70 >= 2.0) {
                    $referred_class_I = 'PASS';
                }
                if ($waheb30_70 >= 2.5) {
                    $referred_class_I = 'LOWER CREDIT';
                }
                if ($waheb30_70 >= 3.0) {
                    $referred_class_I = 'UPPER CREDIT';
                }
                if ($waheb30_70 >= 3.5) {
                    $referred_class_I = 'DISTINCTION';
                }

                if (count($failed_courses)) {
                    $referred_class_I = count($failed_courses) > 3 ? 'REFERRED IN: ' . implode(', ', $failed_courses) : 'REFERRED IN ALL PAPERS';
                }

                if (ExamOffender::whereStatus(1)->whereCandidateIndex($school_result->candidate_index)->where('registration_date', 'like', '%' . $year)->exists()) {
                    $referred_class_I = 'MALPRACTICE';
                }


            }

            $total_passed = count($final_passes);
            $total_absent = $total_registered - ($total_passed + $total_malpractice + $total_referred);
            $percent_passed = $total_passed * 100 / $total_registered;
            $percent_referred = $total_referred * 100 / $total_registered;
            $percent_absent = $total_absent * 100 / $total_registered;
            $percent_malpractice = $total_malpractice * 100 / $total_registered;


            $collection_of_result[] = [
                'candidate_first_name' => $candidate_first_name,
                'candidate_last_name' => $candidate_last_name,
                'candidate_middle_ name' => $candidate_midle_name,
                'candidate_index' => $candidate_index,
                'cgpa30' => number_format($cgpa30, 2),
                'wahebgpa' => number_format($wahebgpa, 2),
                'waheb70' => number_format($waheb70, 2),
                'waheb30_70' => number_format($waheb30_70, 2),
                'referred_status_message_I' => $referred_class_I,
            ];

            $stats = [
                'passed' => $total_passed,
                'referred' => $total_referred,
                'absent' => $total_absent,
                'passed_percent' => number_format($percent_passed, 2),
                'referred_percent' => number_format($percent_referred, 2),
                'absent_percent' => number_format($percent_absent, 2),
                'total_registered' => count($school_results),
                'malpractice' => $total_malpractice,
                'malpractice_percent' => number_format($percent_malpractice, 2)
            ];


            $data = [
//                    'final_passes' => $final_passes,
                'results' => $collection_of_result,
                'course_description' => $course_description,
                'school_details' => $school_details,
                'exam_year' => $exam_year,
                'year' => $year,
                'stats' => $stats,
                'course_list' => $course_list,
                'exam_period' => $exam_period,
                'course_header' => $course_header,
            ];
        }
        return $this->successResponse([$data ?? ' ', 'school_information' => $school_details ?? ' ', 'coure_header' => $course_header_details ?? ' '], "Result Analysis For Only Passed Candidate Retrieved Successfully");
    }

    public function retrievalOfCourseIndexStatisticsInformation()
    {
        if (request()->input('exam_year') && request()->input('course_header')) {
            $course_information = (new CourseHeader())->where('header_key', request()->input('course_header'))->first();
            if (empty($course_header)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }
            $year = str_split(request()->input('exam_year'), 2)[1];
            $indexed_candidates = (new CandidateIndexing())
                ->where('course_header', request()->input('course_header'))
                ->orWhere('exam_id', 'like', '%' . $year)
                ->with('trainingSchool')
                ->paginate(10);

            if (empty($indexed_candidates)) {
                return $this->errorResponse('Invalid Candidate Index Selected');
            }

            $data = [
                'year' => $year, 'sn' => 1, 'indexed_candidates' => $indexed_candidates,
                'course_header' => request()->input('course_header'),
                'course_header_details' => $course_header_information
            ];
            return $this->successResponse($data, 'Retrieval Of CourseIndex Statistics Information Successfully!');
        }

        return $this->successResponse([], 'Data Not Avaliable For Search Filter');
    }

    public function retrievalOfPracticalMarksheetInformation()
    {
        if (request()->input('school_code') && request()->input('course_header') && request()->input('exam_year')) {
            $course_module = CourseModule::where('header_key', request()->input('course_header'))->get();
            $exam_id = str_split(request()->input('exam_year'), 2);
            $training_school = (new TrainingSchool())->where('school_code', request()->input('school_code'))
                ->with('state')->first();

            if (empty($training_school)) {
                return $this->errorResponse('Invalid TrainingSchool Selected');
            }

            $course_header = (new CourseHeaderRepository(new CourseHeader()))->selectCourseHeader();
            if (empty($course_header)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }

            $school_perfomance = (new SchoolPerformance())
                ->where('malpractice', 0)
                ->where('course_header', request()->input('course_header'))
                ->where('school_code', request()->input('school_code'))
                ->where('exam_id', 'like', '%' . $exam_id[1])
                ->with('candidateSchoolPerfomance')
                ->orderBy('candidate_index')
                ->groupBy('candidate_index')
                ->get();

            if (empty($school_perfomance)) {
                return $this->errorResponse('No Performance data found For Selected Filter');
            }

            $page_details = [
                'school_info' => $training_school,
                'print_date' => date('d-M-y h:i A'),
                'course_header_info' => $course_header,
                'course_details' => $course_module,
                'exam_year' => request()->input('exam_year')
            ];


            $data = [
                'school_performances' => $school_perfomance,
                'page_details' => $page_details,
                'sn' => 1
            ];
            return $this->successResponse($data, 'Retrieval Of Practical MarkSheet Information Successfully!');
        }

        return $this->successResponse([], 'search Filter Record Not Found!');
    }

    public function retrievalOfOralMarksheetInformation()
    {
        $school_code = request()->input('school_code');
        $course_header = request()->input('course_header');
        $exam_year = request()->input('exam_year');
        if ($school_code && $course_header && $exam_year) {
            $course_module = CourseModule::where('header_key', $course_header)->get();
            $exam_id = str_split($exam_year, 2);
            $training_school = (new TrainingSchool())->where('school_code', $school_code)
                ->with('state')->first();

            if (empty($training_school)) {
                return $this->errorResponse('Invalid TrainingSchool Selected');
            }

            $course_headerInfo = (new CourseHeaderRepository(new CourseHeader()))->selectCourseHeader();
            if (empty($course_headerInfo)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }

            $candidatess = DB::table('candidates as c')
                ->join(
                    'candidate_indexings as ci',
                    'ci.candidate_index',
                    'c.candidate_index'
                )
                ->select(
                    'ci.last_name',
                    'ci.first_name',
                    'ci.middle_name',
                    'c.*',
                    'ci.validate'
                )
                ->where('ci.course_header', $course_header)
                ->where('c.school_code', $school_code)
                ->where('c.course_header', $course_header)
                ->where('c.exam_id', 'like', '%' . $exam_id[1])
                ->groupBy('c.candidate_index')
                ->get();

            if (empty($candidatess)) {
                return $this->errorResponse('No Oral data found For Selected Filter');
            }

            $filtered = $candidatess->filter(function ($x) {
                return ($x->registration_type == 'fresh' && $x->validate == 'yes') ||
                    $x->registration_type == 'resitall';
            });

            $candidates = $filtered->all();

            $page_details = [
                'school_info' => $training_school,
                'print_date' => date('d-M-y h:i A'),
                'course_header_info' => $course_headerInfo,
                'course_details' => $course_module,
                'exam_year' => $exam_year,
                'exam_date' => $course_headerInfo->month . '/' . $exam_id[1]
            ];


            $data = [
                'candidates' => $candidates,
                'page_details' => $page_details,
                'sn' => 1
            ];
            return $this->successResponse($data, 'Retrieval Of Oral MarkSheet Information Successfully!');
        }

        return $this->successResponse([], 'search Filter Record Not Found!');
    }

    public function RetrievalOfScoresResultBasedOnPassAndfailStatisticInformation()
    {
        if (request()->input('course_header') && request()->input('exam_year')) {
            $year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $course_header_information = (new CourseHeader())->where('header_key', request()->input('course_header'))->first();

            if (empty($course_header_information)) {
                return $this->errorResponse('Invalid Course Header Selected');
            }

            $results_passed = (new ScoreResult())
                ->where('course_header', request()->input('course_header'))
                ->where('course_average', '>=', 40)
                ->where('year', 'like', '%' . $year)
                ->select('id', 'candidate_index', 'course_key', 'course_header', 'course_average', \DB::raw('count(*) as total_passes'))
                ->orderBy('course_key')
                ->groupBy('course_key')
                ->paginate(20);

            $results_failed = (new ScoreResult())
                ->where('course_header', request()->input('course_header'))
                ->where('course_average', '<', 40)
                ->where('year', 'like', '%' . $year)
                ->select('id', 'candidate_index', 'course_key', 'course_header', 'course_average', \DB::raw('count(*) as total_failed'))
                ->orderBy('course_key')
                ->groupBy('course_key')
                ->paginate(20);


            $results = [];
            foreach ($results_passed as $key => $pass) {
                foreach ($results_failed as $key => $fail) {
                    if ($pass->course_key == $fail->course_key) {
                        $results[] = [
                            'course_key' => $pass->course_key,
                            'total_passed' => $pass->total_passes,
                            'total_failed' => $fail->total_failed,
                            'course_header' => $pass->courseScoreResult()->get('description'),
                        ];
                    }
                }
            }

            $page_details = [
                'print_date' => date('d-M-y h:i A'),
                'course_header_information' => $course_header_information,
                'sn' => 1,
                'exam_date' => $course_header_information->month . '/' . $year
            ];

            $data = [
                'results' => $results,
                'page_details' => $page_details
            ];

            return $this->successResponse($data, 'Retrieval Of Pass And fail Statistic Information Successfully!');
        }
        return $this->successResponse($data, 'Search Filter Results Not Found');
    }

    public function retrievealOfApplicationForIndexingInformation()
    {
        $exam_id_splitted = str_split(request()->exam_year);
        $exam_year = $exam_id_splitted[2].$exam_id_splitted[3];

        $candidate_index  = CandidateIndexing::select('first_name', 'last_name','middle_name', 'exam_id', 'course_header',
                                                         'school_code', 'date_of_birth', 'month_yr_reg', 'english', 'biology', 'health_science',
                                                        'chemistry', 'mathematics', 'geography', 'economics', 'food_and_nutrition', 'accounting', 'commerce',
                                                        'physics', 'technical_drawing', 'integrated_science', 'general_science', 'agric', 'seatings',
                                                        'reg_nurse', 'yoruba', 'igbo', 'hausa', 'history', 'religious_knowledge', 'government',
                                                        'literature', 'nationality', 'year_of_certificate_evaluated', 'year_of_certificate_evaluated_2', 'reg_midwife','month_yr')
                                            ->where('candidate_index', request()->candidate_index)
                                            ->where('course_header', request()->course_header)
                                            ->orWhere('exam_id', 'like', '%' . $exam_year)
                                            ->groupBy('candidate_index')
                                            ->orderBy('candidate_index', 'asc')
                                            ->first();


        if(empty($candidate_index))
        {
            return $this->errorResponse("Candidate Information Not Available");
        }

        foreach (array($candidate_index) as $item)
        {
            $exam_id_splitted = str_split($item->exam_id);
            $dob = Carbon::createFromFormat('Y-m-d H:i:s', $item->date_of_birth)->format('Y');
            $current_date = Carbon::now()->format('Y');

            $candidate_details = [
                 'first_name' =>  $item->first_name ?? null,
                 'last_name' =>  $item->last_name ?? null,
                 'middle_name' =>  $item->middle_name ?? null,
                 'gender' =>  $item->gender ?? null,
                 'month_of_exam' =>  $exam_id_splitted[0].$exam_id_splitted[1] ?? null,
                 'year of waheb' =>  $exam_id_splitted[2].$exam_id_splitted[3] ?? null,
                 'nationality' =>  $item->nationality ?? null,
                'age' => (int)$current_date - (int)$dob ?? null
            ];

            $educational_qualification = [
                        $english = [
                            'subject' => 'english',
                            'grade' =>  $item->english,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $biology = [
                            'subject' => 'biology',
                            'grade' => $item->biology,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $health_science = [
                            'subject' => 'health_science',
                            'grade' => $item->health_science,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $chemistry = [
                            'subject' => 'chemistry',
                            'grade' => $item->chemistry,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $geography = [
                            'subject' => 'geography',
                            'grade' => $item->geography,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $mathematics = [
                            'subject' => 'mathematics',
                            'grade' => $item->mathematics,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $economics = [
                            'subject' => 'economics',
                            'grade' => $item->economics,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $food_and_nutrition = [
                            'subject' => 'food_and_nutrition',
                            'grade' => $item->food_and_nutrition,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $accounting = [
                            'subject' => 'accounting',
                            'grade' => $item->accounting,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $commerce = [
                            'subject' => 'commerce',
                            'grade' => $item->commerce,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $physics = [
                            'subject' => 'physics',
                            'grade' => $item->physics,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $technical_drawing = [
                            'subject' => 'technical_drawing',
                            'grade' => $item->technical_drawing,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $integrated_science = [
                            'subject' => 'integrated_science',
                            'grade' => $item->integrated_science,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $general_science = [
                            'subject' => 'general_science',
                            'grade' => $item->general_science,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $agric = [
                            'subject' => 'agric',
                            'grade' => $item->agric,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $yoruba = [
                            'subject' => 'yoruba',
                            'grade' => $item->yoruba,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $igbo = [
                            'subject' => 'igbo',
                            'grade' => $item->igbo,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $hausa = [
                            'subject' => 'hausa',
                            'grade' => $item->hausa,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $history = [
                            'subject' => 'history',
                            'grade' => $item->history,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $religious_knowledge = [
                            'subject' => 'religious_knowledge',
                            'grade' => $item->religious_knowledge,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $government = [
                            'subject' => 'government',
                            'grade' => $item->government,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                        $literature = [
                            'subject' => 'literature',
                            'grade' => $item->literature,
                            'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                        ],
                ];

            $professional_qualification = [
                $reg_nurse = [
                    'subject' => 'reg_nurse',
                    'grade' => $item->reg_nurse,
                    'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                ],
                $reg_nurse = [
                    'subject' => 'reg_nurse',
                    'grade' => $item->reg_nurse,
                    'year' =>  $item->year_of_certificate_evaluated ?? $item->year_of_certificate_evaluated_2
                ]
            ];

        }


        $school_code = TrainingSchool::select('school_code', 'school_name', 'state_id', 'state_code')
                    ->where('school_code', '=', $candidate_index->school_code)
                    ->with('state:id,name')->first();
        return $this->successResponse(['candidate_information' => $candidate_details,
                                        'school_information' => $school_code,
                                        'education_qualification' => $educational_qualification,
                                        'professional_qualification' => $professional_qualification],"Candidate Indexing Information Retrieved Successfully");
    }
}
