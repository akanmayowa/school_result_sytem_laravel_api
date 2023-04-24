<?php /** @noinspection ALL */

namespace App\Services;

use App\Helpers\GeneralLogs;
use App\Http\Resources\CandidateIndexingResource;
use App\Models\TrainingSchool;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;
use App\Models\CandidateIndexing;
use App\Models\CourseHeader;
use App\Repositories\CandidateIndexingRepository;
use App\Traits\ResponsesTrait;
use App\Http\Requests\CandidateIndexingRequest;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Node\Query\AndExpr;
use App\Helpers;
use App\Models\User;
use App\Helpers\DigitalOceanSpaceV2;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CandidateIndexingServices
{
    use ResponsesTrait;
    public ? CandidateIndexingRepository $candidate_indexing_repository = null;

    public function __construct(CandidateIndexingRepository $candidate_indexing_repository){
        $this->candidate_indexing_repository = $candidate_indexing_repository;
        $this->candidateIndexing = new CandidateIndexing();
    }

    public function fetchAllCandidateIndexingVersion2()
    {

        if(request()->status AND request()->course_header AND request()->exam_year AND  !empty(request()->exam_year) AND !empty(request()->course_header) AND !empty(request()->status))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            if(request()->status == 'unverified'){
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where('school_code', auth()->user()->operator_id)
                    ->where('exam_id', 'LIKE', '%' . $exam_year . '%')
                    ->where('course_header', request()->course_header)
                    ->where('unverified','=', 1)
                    ->orderBy('candidate_index','asc')
                    ->groupBy(['candidate_index'])
                    ->get();
                return $this->dataOutput($candidate_indexings);
            }

            if(request()->status == 'verified'){
                $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where('exam_id', 'LIKE', '%' . $exam_year . '%')
                    ->where('school_code', auth()->user()->operator_id)
                    ->where('course_header', request()->course_header)
                    ->where('unverified','=', 0)
                    ->orderBy('candidate_index','asc')
                    ->groupBy(['candidate_index'])
                    ->get();
                return $this->dataOutput($candidate_indexings);
            }
            return $this->dataOutput($candidate_indexings = "No Search Data Avaliable");
        }



        else if(request()->course_header){
            $candidate_indexings = $this->candidateIndexing
                ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                ->with('candidateCategory')
                ->where('school_code', auth()->user()->operator_id)
                ->where('course_header', 'LIKE', '%' . request()->input('course_header') . '%')
                ->orderBy('candidate_index','asc')
                ->groupBy(['candidate_index'])
                ->get();
            return $this->dataOutput($candidate_indexings);
        }

        else if(request()->candidate_index)
        {
            $search = request()->input('candidate_index');
            $candidate_indexings = $this->candidateIndexing
                ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                ->with('candidateCategory')
                ->where('school_code', auth()->user()->operator_id)
                ->where('candidate_index',  request()->input('candidate_index'))
                ->orWhere('first_name',  request()->input('candidate_index'))
                ->orWhere('middle_name', request()->input('candidate_index'))
                ->orWhere('last_name', request()->input('candidate_index'))
                ->orWhere(function ($q) {
                    $q->where('first_name','LIKE', '%' .  request()->input('candidate_index') . '%');
                })
                ->orWhere(function ($q) {
                    $q->where('middle_name','LIKE', '%' .  request()->input('candidate_index') . '%');
                })
                ->orWhere(function ($q) {
                    $q->where('last_name','LIKE', '%' .  request()->input('candidate_index') . '%');
                })
                ->orWhere(function ($q) use ($search) {
                    $q->where(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%");
                })
                ->orWhere(function ($q) use ($search){
                    $q->where(DB::raw("concat(last_name, ' ', first_name)"), 'LIKE', "%".$search."%");
                })
                ->orderBy('candidate_index','asc')
                ->groupBy(['candidate_index'])
                ->get();
            return $this->dataOutput($candidate_indexings);
        }

        else if(request()->has('exam_year'))
        {
            $exam_year= str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $candidate_indexings = $this->candidateIndexing
                ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                ->with('candidateCategory')
                ->where('school_code', auth()->user()->operator_id)
                ->where('exam_id', 'LIKE', '%' . $exam_year)
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->dataOutput($candidate_indexings);
        }

        else if(request()->has('status'))
        {
            if(request()->status == "verified")
            {
                    $candidate_indexings = $this->candidateIndexing
                        ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                        ->with('candidateCategory')
                        ->where('school_code', auth()->user()->operator_id)
                                            ->where('unverified',  1)
                                            ->orderBy('candidate_index','asc')
                                            ->groupBy('candidate_index')
                                            ->get();
                  return $this->dataOutput($candidate_indexings);
            }
            if(request()->status == "unverified")
            {
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where('school_code', auth()->user()->operator_id)
                    ->where('unverified',   1)
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get();
                return $this->dataOutput($candidate_indexings);
            }
            return $this->successResponse([], " ");
        }
    }

    public function fetchAllCandidateIndexing()
        {
            if(request()->school_code AND request()->status AND request()->course_header AND request()->exam_year AND !empty(request()->school_code) AND  !empty(request()->exam_year) AND !empty(request()->course_header) AND !empty(request()->status))
            {

                $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
                if(request()->status == 'unverified'){
                    $candidate_indexings = $this->candidateIndexing
                        ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                        ->with('candidateCategory')
                        ->where(function ($q) use ($exam_year) {
                            $q
                                ->where('exam_id', 'LIKE', '%' . $exam_year . '%')
                                ->orWhere('month_yr_reg', 'LIKE', '%' . $exam_year . '%');
                        })
                        ->where('course_header', request()->course_header)
                        ->where('school_code', request()->school_code)
                        ->where('unverified',   0)
                        ->orderBy('candidate_index','asc')
                        ->groupBy(['candidate_index', 'exam_id'])
                        ->get();
                    return $this->dataOutput($candidate_indexings);
                }

                if(request()->status == 'verified'){
                    $candidate_indexings = $this->candidateIndexing
                        ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                        ->with('candidateCategory')
                        ->where(function ($q) use ($exam_year) {
                            $q
                                ->where('exam_id', 'LIKE', '%' . $exam_year . '%')
                                ->orWhere('month_yr_reg', 'LIKE', '%' . $exam_year . '%');
                        })
                        ->where('course_header', request()->course_header)
                        ->where('school_code', request()->school_code)
                        ->where('unverified',   1)
                        ->orderBy('candidate_index','asc')
                        ->groupBy(['candidate_index', 'exam_id'])
                        ->get();
                    return $this->dataOutput($candidate_indexings);
                }

                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where(function ($q) use ($exam_year) {
                        $q
                            ->where('exam_id', 'LIKE', '%' . $exam_year . '%')
                            ->orWhere('month_yr_reg', 'LIKE', '%' . $exam_year . '%');
                    })
                    ->where('course_header', request()->course_header)
                    ->where('school_code', request()->school_code)
                    ->orderBy('candidate_index','asc')
                    ->groupBy(['candidate_index', 'exam_id'])
                    ->get();
                return $this->dataOutput($candidate_indexings);
            }

            else if(request()->exam_year AND request()->school_code AND !empty(request()->exam_year) AND !empty(request()->school_code))
            {
                $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where(function ($q) use ($exam_year) {
                        $q
                            ->where('exam_id', 'LIKE', '%' . $exam_year . '%')
                            ->orWhere('month_yr_reg', 'LIKE', '%' . $exam_year . '%');
                    })
                    ->where('school_code', request()->school_code);
                if ($course_header = request()->course_header) {
                    $candidate_indexings = $candidate_indexings
                        ->whereCourseHeader($course_header);
                }
                $candidate_indexings = $candidate_indexings
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get();
                return $this->dataOutput($candidate_indexings);

            }

            else if(request()->exam_year AND request()->course_header AND !empty(request()->exam_year) AND !empty(request()->course_header))
            {
                $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where('exam_id','LIKE', '%' . $exam_year)
                    ->where('course_header', request()->course_header)
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get();
                return $this->dataOutput($candidate_indexings);

            }

            else if(request()->school_code AND request()->course_header)
            {
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where('school_code',  request()->school_code)
                    ->where('course_header',  request()->course_header)
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get();
                return $this->dataOutput($candidate_indexings);
            }

            else if(request()->school_code){
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where('school_code', 'LIKE', '%' . request()->input('school_code') . '%')
                    ->orderBy('candidate_index','asc')
                    ->get();
                return $this->dataOutput($candidate_indexings);
            }

            else if(request()->course_header){
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where('course_header', request()->input('course_header'))
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get();
                return $this->dataOutput($candidate_indexings);
            }

            else if(request()->candidate_index)
            {
                $search = request()->input('candidate_index');
                  $candidate_indexings = $this->candidateIndexing->orderByDesc('registered_at')
                      ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                      ->with('candidateCategory')
                      ->orWhere('candidate_index', 'LIKE', '%' .  request()->input('candidate_index') . '%')
                      ->orWhere('first_name',  request()->input('candidate_index'))
                      ->orWhere('middle_name','LIKE', '%' . request()->input('candidate_index') . '%')
                      ->orWhere('last_name', 'LIKE', '%' .request()->input('candidate_index') . '%')
                      ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")
                      ->orWhere(DB::raw("concat(last_name, ' ', first_name)"), 'LIKE', "%".$search."%")
                      ->orderBy('candidate_index','asc')
                      ->groupBy('candidate_index')
                      ->get();
                return $this->dataOutput($candidate_indexings);

                    }

            else if(request()->has('exam_year'))
            {
                $exam_year= str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->where('exam_id', 'LIKE', '%' . $exam_year)
                    ->with('candidateCategory')
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get();
                return $this->dataOutput($candidate_indexings);
            }

            else if(request()->has('status'))
            {
                if(request()->status == "verified")
                {
                    $candidate_indexings = $this->candidateIndexing
                        ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                        ->with('candidateCategory')
                        ->where('unverified',   0)
                        ->orderBy('candidate_index','asc')
                        ->groupBy('candidate_index')
                        ->get();
                    return $this->dataOutput($candidate_indexings);
                }
                if(request()->status == "unverified")
                {
                    $candidate_indexings = $this->candidateIndexing
                        ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                        ->with('candidateCategory')
                        ->where('unverified',   1)
                        ->orderBy('candidate_index','asc')
                        ->groupBy('candidate_index')
                        ->get();
                    return $this->dataOutput($candidate_indexings);
                }

                return $this->successResponse([], " ");
            }
        }

    public function searchByNameAndCandidateIndexAndCourseHeader()
        {
            if(request()->course_header AND request()->candidate_index)
            {
                $search = request()->input('candidate_index');
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->where('course_header', request()->input('course_header'))
                    ->where('candidate_index', request()->input('candidate_index'))
                    ->orWhere('first_name',  request()->input('candidate_index'))
                    ->orWhere('middle_name','LIKE', '%' . request()->input('candidate_index') . '%')
                    ->orWhere('last_name', 'LIKE', '%' .request()->input('candidate_index') . '%')
                    ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")
                    ->orWhere(DB::raw("concat(last_name, ' ', first_name)"), 'LIKE', "%".$search."%")
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get();
                return $this->successResponse($candidate_indexings, "Retrieving Results");
            }

             else if(request()->course_header)
            {
                $candidate_indexings = CandidateIndexing::
                                            select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                                            ->with('candidateCategory')
                                            ->where('course_header', 'LIKE', '%' .  request()->input('course_header') . '%')
                                            ->orderBy('candidate_index','asc')
                                            ->groupBy('candidate_index')
                                            ->get();

                return $this->successResponse($candidate_indexings, "Retrieving Results");
            }

            else if(request()->has('candidate_index'))
            {
                $search = request()->input('candidate_index');
                $candidate_indexings = $this->candidateIndexing
                    ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                    ->with('candidateCategory')
                    ->orWhere('candidate_index', 'LIKE', '%' .  request()->input('candidate_index') . '%')
                    ->orWhere('first_name',  request()->input('candidate_index'))
                    ->orWhere('middle_name','LIKE', '%' . request()->input('candidate_index') . '%')
                    ->orWhere('last_name', 'LIKE', '%' .request()->input('candidate_index') . '%')
                    ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")
                    ->orWhere(DB::raw("concat(last_name, ' ', first_name)"), 'LIKE', "%".$search."%")
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get();

                return $this->successResponse($candidate_indexings, "Retrieving Results");
            }

            else{
                return $this->successResponse(["message" => "Data Not Available"]);
            }
        }

    public function searchByfNameAndCandidateIndexAndCourseHeaderForTrainingSchool()
    {
        if(request()->input('course_header') AND request()->input('candidate_index'))
        {
            $search = request()->input('candidate_index');
            $candidate_indexings = $this->candidateIndexing
                ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                ->where('school_code', auth()->user()->operator_id)
                ->with('candidateCategory')
                ->where('course_header', request()->input('course_header'))
                ->where('candidate_index', request()->input('candidate_index'))
                ->orWhere(function ($q) {
                    $q->where('first_name','LIKE', '%' .  request()->input('candidate_index') . '%')
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orWhere(function ($q) {
                    $q->where('middle_name','LIKE', '%' .  request()->input('candidate_index') . '%')
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orWhere(function ($q) {
                    $q->where('last_name','LIKE', '%' .  request()->input('candidate_index') . '%')
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orWhere(function ($q) use ($search) {
                    $q->where(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orWhere(function ($q) use ($search){
                    $q->where(DB::raw("concat(last_name, ' ', first_name)"), 'LIKE', "%".$search."%")
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->successResponse($candidate_indexings, "Retrieving Results");
        }

        else if(request()->has('candidate_index'))
        {
            $search = request()->input('candidate_index');
            $candidate_indexings = $this->candidateIndexing
                ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                ->where('school_code', auth()->user()->operator_id)
                ->with('candidateCategory')
                ->where('candidate_index', 'LIKE', '%' .  request()->input('candidate_index') . '%')
                ->orWhere(function ($q) {
                    $q->where('first_name','LIKE', '%' .  request()->input('candidate_index') . '%')
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orWhere(function ($q) {
                    $q->where('middle_name','LIKE', '%' .  request()->input('candidate_index') . '%')
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orWhere(function ($q) {
                    $q->where('last_name','LIKE', '%' .  request()->input('candidate_index') . '%')
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orWhere(function ($q) use ($search) {
                    $q->where(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orWhere(function ($q) use ($search){
                    $q->where(DB::raw("concat(last_name, ' ', first_name)"), 'LIKE', "%".$search."%")
                        ->where('school_code', auth()->user()->operator_id);
                })
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->successResponse($candidate_indexings, "Retrieving Results");
        }

        else if(request()->has('course_header'))
        {
            $candidate_indexings = $this->candidateIndexing
                ->select('id','candidate_index','first_name','last_name','middle_name','school_code','exam_id','course_header','unverified','candidate_category')
                ->with('candidateCategory')
                ->where('school_code', auth()->user()->operator_id)
                ->where('course_header', request()->input('course_header'))
                ->orderBy('candidate_index','asc')
                ->groupBy('candidate_index')
                ->get();
            return $this->successResponse($candidate_indexings, "Retrieving Results");
        }

        else{
            return $this->successResponse(["message" => "Data Not Available"]);
        }
    }

    public function dataOutput($data)
    {
        return response()->json($data);
    }

    public function generateCandidateIndexNumber()
    {
        $course_header = CourseHeader::where('header_key', request()->course_header)->first();
        $current_year = Carbon::now()->format('y');
        $trimmed_course_year_index_code =  str_replace(' ', '', $course_header->add_year);
        $school = TrainingSchool::where('school_code',request()->school_code)->first();
        $course_header_school_code = $course_header->index_code.substr($school->school_code, 0,2);

        if($trimmed_course_year_index_code >= 0)
        {
            $format_add_year =  str_replace('+', ' ', $trimmed_course_year_index_code);
            $add_year = (int)$current_year + (int)$format_add_year ;
        }

        if($trimmed_course_year_index_code < 0)
        {
            $format_add_year =  str_replace('-', ' ', $trimmed_course_year_index_code);
            $add_year = (int)$current_year - (int)$format_add_year;
        }

        $candidate_index = CandidateIndexing::query()
            ->where('course_header', request()->course_header)
            ->where('school_code', request()->school_code)
            ->orderByDesc('id')
            ->latest()
            ->first();

        if(!$candidate_index ){
            $number =  1;
            $value = sprintf("%03d", $number);

            if($course_header->cadre == 'HND') {
                $add_year = $add_year.'H';
            }
            return  $add_year."/".$course_header_school_code."/".$value;
        }

        if($candidate_index){
            $items = explode('/', $candidate_index->candidate_index);
            if ((int) $items[2] < 1000) {
                $number = $items[2] + 1;
                $value = sprintf("%03d", $number);
            }
            if((int) $items[2] >= 1000)
            {
                $number = $items[2] + 1;
                $value = sprintf("%04d", $number);
            }

            if($course_header->cadre == 'HND')
            {
                $add_year = $add_year.'H';
            }

            return  $add_year."/".$course_header_school_code."/".$value;
        }

    }

    public function createCandidateIndexing(array $request)
    {
        DB::beginTransaction();
        try {
            $examIDHelper = new Helpers\ExamID();
            $exam_month = $examIDHelper->getExamMonth($request['course_header']);
            $year = request()->input('admission_year') === 0 ? date('Y') : Carbon::now()->format('Y-m-d H:i:s');
            $get_exam_id = $examIDHelper->getExamId($request['course_header']);
            $exam_id = $get_exam_id;
            $exam_date = date('Y-m-d H:i:s', strtotime('01-' . $exam_month . '-' . $year));
            $index_number = request()->input('candidate_index');
            $date_of_birth_array = explode('-', request()->input('date_of_birth'));
            $date_of_birth = $date_of_birth_array[1] . '/' . $date_of_birth_array[0] . '/' . $date_of_birth_array[2];
            $formatted_date_of_birth = date("Y-m-d H:i:s", strtotime($date_of_birth));
            $admission_date = Carbon::now()->format('Y-m-d H:i:s');
            $index_date = date('my');
            $query = compact('exam_id', 'index_number', 'formatted_date_of_birth');
            $image_for_photo = request()->file('photo');
            $image_for_birth_certificate_upload = request()->file('birth_certificate_upload');
            $image_for_olevel_certificate_upload = request()->file('olevel_certificate_upload');
            $image_for_olevel_2_certificate_upload = request()->file('olevel_2_certificate_upload');
            $image_for_phn_certificate_upload = request()->file('phn_certificate_upload');
            $image_for_phn_2_certificate_upload = request()->file('phn_2_certificate_upload');
            $image_for_nd_certificate_upload = request()->file('nd_certificate_upload');
            $image_for_hnd_certificate_upload = request()->file('hnd_certificate_upload');
            $image_for_marriage_certificate_upload = request()->file('marriage_certificate_upload');

                if ($image_for_photo) {
                    $public_id_for_photo = DigitalOceanSpaceV2::uploadImage('photo', $image_for_photo);
                }


                if ($image_for_birth_certificate_upload) {
                    $public_id_for_birth_certificate_upload = DigitalOceanSpaceV2::uploadImage('birth_certificate_upload', $image_for_birth_certificate_upload);
                }

                if ($image_for_olevel_certificate_upload) {
                    $public_id_for_olevel_certificate_upload = DigitalOceanSpaceV2::uploadImage('olevel_certificate_upload', $image_for_olevel_certificate_upload);
                }

                if ($image_for_olevel_2_certificate_upload) {
                    $public_id_for_olevel_2_certificate_upload = DigitalOceanSpaceV2::uploadImage('olevel_2_certificate_upload', $image_for_olevel_2_certificate_upload);
                }

                if ($image_for_phn_certificate_upload) {
                    $public_id_for_phn_certificate_upload = DigitalOceanSpaceV2::uploadImage('phn_certificate_upload', $image_for_phn_certificate_upload);
                }

                if ($image_for_phn_2_certificate_upload) {
                    $public_id_for_phn_2_certificate_upload = DigitalOceanSpaceV2::uploadImage('phn_2_certificate_upload', $image_for_phn_2_certificate_upload);
                }

                if ($image_for_nd_certificate_upload) {
                    $public_id_for_nd_certificate_upload = DigitalOceanSpaceV2::uploadImage('nd_certificate_upload', $image_for_nd_certificate_upload);
                }

                if ($image_for_hnd_certificate_upload) {
                    $public_id_for_hnd_certificate_upload = DigitalOceanSpaceV2::uploadImage('hnd_certificate_upload', $image_for_hnd_certificate_upload);
                }

                if ($image_for_marriage_certificate_upload) {
                    $public_id_for_marriage_certificate_upload = DigitalOceanSpaceV2::uploadImage('marriage_certificate_upload', $image_for_marriage_certificate_upload);
                }

                $validated_image_data = [
                    'photo' => $public_id_for_photo ?? 'default.png',
                    'birth_certificate_upload' => $public_id_for_birth_certificate_upload ?? 'default.png',
                    'olevel_certificate_upload' => $public_id_for_olevel_certificate_upload ?? 'default.png',
                    'olevel_2_certificate_upload' => $public_id_for_olevel_2_certificate_upload ?? 'default.png',
                    'phn_certificate_upload' => $public_id_for_phn_certificate_upload ?? 'default.png',
                    'phn_2_certificate_upload' => $public_id_for_phn_2_certificate_upload ?? 'default.png',
                    'nd_certificate_upload' => $public_id_for_nd_certificate_upload ?? 'default.png',
                    'hnd_certificate_upload' => $public_id_for_hnd_certificate_upload ?? 'default.png',
                    'marriage_certificate_upload' => $public_id_for_marriage_certificate_upload ?? 'default.png',
                ];

                 $candidate_indexing_number = $this->generateCandidateIndexNumber();
                 if($candidate_indexing_number == false)
                 {
                     return $this->errorResponse("Maximum Candidate Index Number Reached For School And Course Header Selected");
                 }

                $candidate_indexing = CandidateIndexing::create(array_merge($request,
                    ['index_date' => $index_date],
                    ['exam_id' => $get_exam_id],
                    ['exam_date' => $exam_date],
                    ['admission_date' => $admission_date],
                    ['month_yr' => $get_exam_id],
                    ['month_yr_reg' => $get_exam_id],
                    ['major' => 'NL'],
                    ['reg_date' => Carbon::now()->format('Y-m-d H:i:s')],
                    ['exam_month_def' => Carbon::now()->format('Y-m-d H:i:s')],
                    ['registered_at' => Carbon::now()->format('Y-m-d H:i:s')],
//                    ['exam_date' => $get_exam_id],
                    ['exam_number_2' => 'Nill'],
                    ['exam_month_2' => 'Nill'],
                    ['candidate_index' => $candidate_indexing_number],
                    $query, $validated_image_data));


                  $its_a_training_school = TrainingSchool::where('school_code', auth()->user()->operator_id)->first();
                 if($its_a_training_school)
//                if (auth()->user()->isTrainingSchoolAdmin())
                {
                    $user = auth()->user()->load('trainingSchoolUser');
                    $body = "$user->name in training school {$user->school_name} indexed a candidate with candidate index number: $candidate_indexing->candidate_index";
                    auth()->user()->notify(new AdminNotification('Candidate Indexed', $body, 'candidate_indexed'));
                    DB::commit();
                    return $this->successResponse($candidate_indexing,"Candidate Indexing created successfully");
                }
                DB::commit();
               $verification_status = $this->verifyCandidateIndex($candidate_indexing);
               $body = "Admin Indexed A Candidate";
            auth()->user()->notify(new AdminNotification('Candidate Indexed', $body, 'candidate_indexed'));
            return $this->successResponse($candidate_indexing, $verification_status. "CANDIDATE INDEXING AND ".$verification_status);
            }

        catch (\Throwable $exception) {
            DB::rollback();
            throw  $exception;
        }
    }

    public function createCandidateIndexingForSchools(array $request)
    {
        DB::beginTransaction();
        try {
            $examIDHelper = new Helpers\ExamID();
            $exam_month = $examIDHelper->getExamMonth($request['course_header']);
            $year = request()->input('admission_year') === 0 ? date('Y') : Carbon::now()->format('Y-m-d H:i:s');
            $get_exam_id = $examIDHelper->getExamId($request['course_header']);
            $exam_id = $get_exam_id;
            $exam_date = date('Y-m-d H:i:s', strtotime('01-' . $exam_month . '-' . $year));
            $index_number = request()->input('candidate_index');
            $date_of_birth_array = explode('-', request()->input('date_of_birth'));
            $date_of_birth = $date_of_birth_array[1] . '/' . $date_of_birth_array[0] . '/' . $date_of_birth_array[2];
            $formatted_date_of_birth = date("Y-m-d H:i:s", strtotime($date_of_birth));
            $admission_date = Carbon::now()->format('Y-m-d H:i:s');
            $index_date = date('my');
            $query = compact('exam_id', 'index_number', 'formatted_date_of_birth');
            $image_for_photo = request()->file('photo');
            $image_for_birth_certificate_upload = request()->file('birth_certificate_upload');
            $image_for_olevel_certificate_upload = request()->file('olevel_certificate_upload');
            $image_for_olevel_2_certificate_upload = request()->file('olevel_2_certificate_upload');
            $image_for_phn_certificate_upload = request()->file('phn_certificate_upload');
            $image_for_phn_2_certificate_upload = request()->file('phn_2_certificate_upload');
            $image_for_nd_certificate_upload = request()->file('nd_certificate_upload');
            $image_for_hnd_certificate_upload = request()->file('hnd_certificate_upload');
            $image_for_marriage_certificate_upload = request()->file('marriage_certificate_upload');

            if ($image_for_photo) {
                $public_id_for_photo = DigitalOceanSpaceV2::uploadImage('photo', $image_for_photo);
            }


            if ($image_for_birth_certificate_upload) {
                $public_id_for_birth_certificate_upload = DigitalOceanSpaceV2::uploadImage('birth_certificate_upload', $image_for_birth_certificate_upload);
            }

            if ($image_for_olevel_certificate_upload) {
                $public_id_for_olevel_certificate_upload = DigitalOceanSpaceV2::uploadImage('olevel_certificate_upload', $image_for_olevel_certificate_upload);
            }

            if ($image_for_olevel_2_certificate_upload) {
                $public_id_for_olevel_2_certificate_upload = DigitalOceanSpaceV2::uploadImage('olevel_2_certificate_upload', $image_for_olevel_2_certificate_upload);
            }

            if ($image_for_phn_certificate_upload) {
                $public_id_for_phn_certificate_upload = DigitalOceanSpaceV2::uploadImage('phn_certificate_upload', $image_for_phn_certificate_upload);
            }

            if ($image_for_phn_2_certificate_upload) {
                $public_id_for_phn_2_certificate_upload = DigitalOceanSpaceV2::uploadImage('phn_2_certificate_upload', $image_for_phn_2_certificate_upload);
            }

            if ($image_for_nd_certificate_upload) {
                $public_id_for_nd_certificate_upload = DigitalOceanSpaceV2::uploadImage('nd_certificate_upload', $image_for_nd_certificate_upload);
            }

            if ($image_for_hnd_certificate_upload) {
                $public_id_for_hnd_certificate_upload = DigitalOceanSpaceV2::uploadImage('hnd_certificate_upload', $image_for_hnd_certificate_upload);
            }

            if ($image_for_marriage_certificate_upload) {
                $public_id_for_marriage_certificate_upload = DigitalOceanSpaceV2::uploadImage('marriage_certificate_upload', $image_for_marriage_certificate_upload);
            }

            $validated_image_data = [
                'photo' => $public_id_for_photo ?? 'default.png',
                'birth_certificate_upload' => $public_id_for_birth_certificate_upload ?? 'default.png',
                'olevel_certificate_upload' => $public_id_for_olevel_certificate_upload ?? 'default.png',
                'olevel_2_certificate_upload' => $public_id_for_olevel_2_certificate_upload ?? 'default.png',
                'phn_certificate_upload' => $public_id_for_phn_certificate_upload ?? 'default.png',
                'phn_2_certificate_upload' => $public_id_for_phn_2_certificate_upload ?? 'default.png',
                'nd_certificate_upload' => $public_id_for_nd_certificate_upload ?? 'default.png',
                'hnd_certificate_upload' => $public_id_for_hnd_certificate_upload ?? 'default.png',
                'marriage_certificate_upload' => $public_id_for_marriage_certificate_upload ?? 'default.png',
            ];

            $candidate_indexing_number = $this->generateCandidateIndexNumber();
            if($candidate_indexing_number == false)
            {
                return $this->errorResponse("Maximum Candidate Index Number Reached For School And Course Header Selected");
            }

            $candidate_indexing = CandidateIndexing::create(array_merge($request,
                ['index_date' => $index_date],
                ['exam_id' => $get_exam_id],
                ['exam_date' => $exam_date],
                ['admission_date' => $admission_date],
                ['month_yr' => $get_exam_id],
                ['month_yr_reg' => $get_exam_id],
                ['major' => 'NL'],
                ['reg_date' => Carbon::now()->format('Y-m-d H:i:s')],
                ['exam_month_def' => Carbon::now()->format('Y-m-d H:i:s')],
                ['registered_at' => Carbon::now()->format('Y-m-d H:i:s')],
//                    ['exam_date' => $get_exam_id],
                ['exam_number_2' => 'Nill'],
                ['exam_month_2' => 'Nill'],
                ['candidate_index' => $candidate_indexing_number],
                $query, $validated_image_data));
                $user = auth()->user()->load('trainingSchoolUser');
                $body = "$user->name in training school {$user->school_name} indexed a candidate with candidate index number: $candidate_indexing->candidate_index";
                auth()->user()->notify(new AdminNotification('Candidate Indexed', $body, 'candidate_indexed'));
                DB::commit();
                return $this->successResponse($candidate_indexing,"Candidate Indexing created successfully");
        }

        catch (\Throwable $exception) {
            DB::rollback();
            throw  $exception;
        }
    }

    public function showSingleCandidateIndexDetail($id)
    {
        $single_candidate_index_details = $this->candidateIndexing->where('id', $id)->get();
        if(empty($single_candidate_index_details))
        {
            return $this->errorResponse( 'Candidate Index Not Available');
        }
        $data = [];
        foreach ($single_candidate_index_details as $candidate_index) {
            $data[] = array_merge(
                ['id' => $candidate_index->id],
                ['candidate_index' => $candidate_index->candidate_index], ['school_code' => $candidate_index->school_code],
                ['first_name' => $candidate_index->first_name], ['title' => $candidate_index->title],
                ['middle_name' => $candidate_index->middle_name], ['last_name' => $candidate_index->last_name],
                ['date_of_birth' => $candidate_index->date_of_birth], ['candidate_category' => $candidate_index->candidate_category],
                ['years_of_experience' =>    $candidate_index->years_of_experience], ['course_header' => $candidate_index->course_header],
                ['marital_status' => $candidate_index->marital_status], ['seatings' => $candidate_index->seatings], ['reg_nurse' => $candidate_index->reg_nurse],
                ['reg_midwife' => $candidate_index->reg_midwife], ['month_yr' => $candidate_index->month_yr], ['month_yr_reg' => $candidate_index->month_yr_reg],
                ['verify_birth_certificate' => $candidate_index->verify_birth_certificate], ['verify_o_level'=> $candidate_index->verify_o_level], ['verify_marriage_certificate' => $candidate_index->verify_marriage_certificate], ['verify_credentials' => $candidate_index->verify_credentials],
                ['letter_of_reference' => $candidate_index->letter_of_reference], ['on_course'  => $candidate_index->on_course], ['degree_holder' => $candidate_index->degree_holder], ['form_no' => $candidate_index->form_no],
                ['verify_status'=> $candidate_index->verify_status], ['verify_status_2' =>  $candidate_index->verify_status_2],
                ['nationality' => $candidate_index->nationality], ['certificate_evaluated' => $candidate_index->certificate_evaluated], ['certificate_evaluated_2' => $candidate_index->certificate_evaluated_2],
                ['photo'=> $candidate_index->photo], ['birth_certificate_upload' => $candidate_index->birth_certificate_upload],
                ['marriage_certificate_upload' => $candidate_index->marriage_certificate_upload], ['olevel_certificate_upload' => $candidate_index->olevel_certificate_upload],
                ['olevel_2_certificate_upload' => $candidate_index->olevel_2_certificate_upload], ['phn_certificate_upload' => $candidate_index->phn_certificate_upload],
                ['phn_2_certificate_upload' => $candidate_index->phn_2_certificate_upload], ['nd_certificate_upload' => $candidate_index->nd_certificate_upload], ['gender' => $candidate_index->gender], ['major' => $candidate_index->major],
                ['exam_id' => $candidate_index->exam_id], ['admission_date' => $candidate_index->admission_date], ['exam_date' => $candidate_index->exam_date], ['reg_date' => $candidate_index->reg_date],
                ['validate' => $candidate_index->validate], ['dont_det' => $candidate_index->dont_det], ['year_of_certificate_evaluated' => $candidate_index->year_of_certificate_evaluated], ['year_of_certificate_evaluated_2' => $candidate_index->year_of_certificate_evaluated_2],
                ['exam_number_1' => $candidate_index->exam_number_1], ['exam_number_2' => $candidate_index->exam_number_2], ['registered_at' => $candidate_index->registered_at], ['visible' => $candidate_index->visible],
                ['indexed' => $candidate_index->indexed], ['unverified' => $candidate_index->unverified], ['hnd_certificate_upload' => $candidate_index->hnd_certificate_upload],
                ['exam_month' => $candidate_index->exam_month],
                ['exam_month_2' => $candidate_index->exam_month_2],
                ['reason' => $candidate_index->reason],
                ['subject' =>  [ 'yoruba' => $candidate_index->yoruba,
                    'igbo' => $candidate_index->igbo,
                    'hausa' => $candidate_index->hausa,
                    'history' => $candidate_index->history,
                    'religious_knowledge' => $candidate_index->religious_knowledge,
                    'government' => $candidate_index->government,
                    'literature' => $candidate_index->literature,
                    'english'=> $candidate_index->english,
                    'biology' => $candidate_index->biology,
                    'health_science' => $candidate_index->health_science,
                    'chemistry' => $candidate_index->chemistry,
                    'mathematics' => $candidate_index->mathematics,
                    'geography' => $candidate_index->geography,
                    'economics' => $candidate_index->economics,
                    'food_and_nutrition' => $candidate_index->food_and_nutrition,
                    'accounting' => $candidate_index->accounting,
                    'commerce' => $candidate_index->commerce,
                    'physics' => $candidate_index->physics,
                    'technical_drawing' => $candidate_index->technical_drawing,
                    'integrated_science' => $candidate_index->integrated_science,
                    'general_science' => $candidate_index->general_science,
                    'agric' => $candidate_index->agric,
                    'seatings' => $candidate_index->seatings,
                    'reg_midwife' => $candidate_index->reg_midwife,
                    'reg_nurse' => $candidate_index->reg_nurse,
                ],
                ],
                ['school' => $candidate_index->trainingSchools],
                ['school_course_detail' => $candidate_index->courseHeader],
                ['candidate_category' => $candidate_index->candidateCategory]
            );
        }
        return $this->successResponse($data, "Single Candidate Index Details Retrieved Successfully");
    }

    public function updateCandidateIndexing(array $request)
    {
        DB::beginTransaction();
        try {
            $year = request()->input('admission_year') === 0 ? date('Y') : Carbon::now()->format('Y-m-d H:i:s');
             if(request()->input('admission_year'))
             {
                 $get_exam_id = $exam_month . str_split($year, 2)[1];
                 $exam_id = $get_exam_id;
                 $exam_date = date('Y-m-d H:i:s', strtotime('01-' . $exam_month . '-' . $year));
                 $query = compact('exam_id', 'index_number', 'formatted_date_of_birth');
             }
             if(request()->input('candidate_index')){
                 $index_number = request()->input('candidate_index');
             }
            if(request()->input('date_of_birth'))
            {
                $date_of_birth_array = explode('-', request()->input('date_of_birth'));
                $date_of_birth = $date_of_birth_array[1] . '/' . $date_of_birth_array[0] . '/' . $date_of_birth_array[2];
                $formatted_date_of_birth = date("Y-m-d H:i:s", strtotime($date_of_birth));
            }

            $admission_date = Carbon::now()->format('Y-m-d H:i:s');
            $index_date = Carbon::now()->format('Y-m-d H:i:s');

            $image_for_photo = request()->file('photo');
            $image_for_birth_certificate_upload = request()->file('birth_certificate_upload');
            $image_for_olevel_certificate_upload = request()->file('olevel_certificate_upload');
            $image_for_olevel_2_certificate_upload = request()->file('olevel_2_certificate_upload');
            $image_for_phn_certificate_upload = request()->file('phn_certificate_upload');
            $image_for_phn_2_certificate_upload = request()->file('phn_2_certificate_upload');
            $image_for_nd_certificate_upload = request()->file('nd_certificate_upload');
            $image_for_hnd_certificate_upload = request()->file('hnd_certificate_upload');
            $image_for_marriage_certificate_upload = request()->file('marriage_certificate_upload');

            if ($image_for_photo) {
                $public_id_for_photo = DigitalOceanSpaceV2::uploadImage('photo', $image_for_photo);
            }

            if ($image_for_birth_certificate_upload) {
                $public_id_for_birth_certificate_upload = DigitalOceanSpaceV2::uploadImage('birth_certificate_upload', $image_for_birth_certificate_upload);
            }

            if ($image_for_olevel_certificate_upload) {
                $public_id_for_olevel_certificate_upload = DigitalOceanSpaceV2::uploadImage('olevel_certificate_upload', $image_for_olevel_certificate_upload);
            }

            if ($image_for_olevel_2_certificate_upload) {
                $public_id_for_olevel_2_certificate_upload = DigitalOceanSpaceV2::uploadImage('olevel_2_certificate_upload', $image_for_olevel_2_certificate_upload);
            }

            if ($image_for_phn_certificate_upload) {
                $public_id_for_phn_certificate_upload = DigitalOceanSpaceV2::uploadImage('phn_certificate_upload', $image_for_phn_certificate_upload);
            }

            if ($image_for_phn_2_certificate_upload) {
                $public_id_for_phn_2_certificate_upload = DigitalOceanSpaceV2::uploadImage('phn_2_certificate_upload', $image_for_phn_2_certificate_upload);
            }

            if ($image_for_nd_certificate_upload) {
                $public_id_for_nd_certificate_upload = DigitalOceanSpaceV2::uploadImage('nd_certificate_upload', $image_for_nd_certificate_upload);
            }

            if ($image_for_hnd_certificate_upload) {
                $public_id_for_hnd_certificate_upload = DigitalOceanSpaceV2::uploadImage('hnd_certificate_upload', $image_for_hnd_certificate_upload);
            }

            if ($image_for_marriage_certificate_upload) {
                $public_id_for_marriage_certificate_upload = DigitalOceanSpaceV2::uploadImage('marriage_certificate_upload', $image_for_marriage_certificate_upload);
            }

            $validated_image_data = [
                'photo' => $public_id_for_photo ?? 'default.png',
                'birth_certificate_upload' => $public_id_for_birth_certificate_upload ?? 'default.png',
                'olevel_certificate_upload' => $public_id_for_olevel_certificate_upload ?? 'default.png',
                'olevel_2_certificate_upload' => $public_id_for_olevel_2_certificate_upload ?? 'default.png',
                'phn_certificate_upload' => $public_id_for_phn_certificate_upload ?? 'default.png',
                'phn_2_certificate_upload' => $public_id_for_phn_2_certificate_upload ?? 'default.png',
                'nd_certificate_upload' => $public_id_for_nd_certificate_upload ?? 'default.png',
                'hnd_certificate_upload' => $public_id_for_hnd_certificate_upload ?? 'default.png',
                'marriage_certificate_upload' => $public_id_for_marriage_certificate_upload ?? 'default.png',
            ];

            $candidate_indexing = $this->candidateIndexing->where('id','=',  request()->id)->where('candidate_index', request()->candidate_index)->first();
            if(!$candidate_indexing)
            {
                return $this->errorResponse("Candidate Index Id Doesnt Exist");
            }

//            $candidate_indexing->update(array_merge($request,
//                ['index_date' => $index_date ?? $candidate_indexing->index_date],
//                ['exam_date' => $exam_date ?? $candidate_indexing->exam_date],
//                ['admission_date' => $admission_date ?? $candidate_indexing->admission_date],
//                ['month_yr' => $get_exam_id ?? $candidate_indexing->month_yr],
//                ['month_yr_reg' => $get_exam_id ?? $candidate_indexing->month_yr_reg],
//                ['major' => 'NL'],
//                ['reason' => 'NON'],
//                ['reg_date' => Carbon::now()->format('Y-m-d H:i:s')],
//                ['exam_month_def' => Carbon::now()->format('Y-m-d H:i:s')],
//                ['registered_at' => Carbon::now()->format('Y-m-d H:i:s')],
//                ['exam_number_2' => 'Nill'],
//                ['exam_month_2' => 'Nill'],
//                $validated_image_data));


            CandidateIndexing::updateOrCreate(
                [
                    'candidate_index' => request()->candidate_index,
                    'id' => request()->id
                ],
                array_merge($request,[
                'index_date' => $index_date ?? $candidate_indexing->index_date,
                'exam_date' => $exam_date ?? $candidate_indexing->exam_date,
                'admission_date' => $admission_date ?? $candidate_indexing->admission_date,
                'month_yr' => $get_exam_id ?? $candidate_indexing->month_yr,
                'month_yr_reg' => $get_exam_id ?? $candidate_indexing->month_yr_reg,
                'major' => 'NL',
                'reason' => 'NON',
                'reg_date' => Carbon::now()->format('Y-m-d H:i:s'),
                'exam_month_def' => Carbon::now()->format('Y-m-d H:i:s'),
                'registered_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'exam_number_2' => 'Nill',
                'exam_month_2' => 'Nill',
                    'photo' => $public_id_for_photo ?? 'default.png',
                    'birth_certificate_upload' => $public_id_for_birth_certificate_upload ?? 'default.png',
                    'olevel_certificate_upload' => $public_id_for_olevel_certificate_upload ?? 'default.png',
                    'olevel_2_certificate_upload' => $public_id_for_olevel_2_certificate_upload ?? 'default.png',
                    'phn_certificate_upload' => $public_id_for_phn_certificate_upload ?? 'default.png',
                    'phn_2_certificate_upload' => $public_id_for_phn_2_certificate_upload ?? 'default.png',
                    'nd_certificate_upload' => $public_id_for_nd_certificate_upload ?? 'default.png',
                    'hnd_certificate_upload' => $public_id_for_hnd_certificate_upload ?? 'default.png',
                    'marriage_certificate_upload' => $public_id_for_marriage_certificate_upload ?? 'default.png'
                ])
            );

            if (auth()->user()->isTrainingSchoolAdmin()) {
                $user = auth()->user()->load('trainingSchoolUser');
                $body = "$user->name in training school {$user->school_name} indexed a candidate with candidate index number: $candidate_indexing->candidate_index";
                auth()->user()->notify(new AdminNotification('Candidate Indexed', $body, 'candidate_indexed'));
                DB::commit();
                return $this->successResponse($candidate_indexing->refresh(),"  Candidate Indexing Updated successfully ");
            }
            DB::commit();
            return $this->successResponse($candidate_indexing->refresh(), "  Candidate Indexing Updated successfully ");
        }
        catch (\Throwable $exception) {
            DB::rollback();
            throw  $exception;
        }
    }

    public function getSuperAdmin()
    {
        return User::where('user_role','super_admin')->first();
    }

    public function toPass($course)
    {
        if(str_contains($course, 'A')){
            return true;
        }
        if(str_contains($course, 'B')){
            return true;
        }
        if(str_contains($course, 'C')){
            return true;
        }
        return false;
    }

    public function toPassString($course)
    {
        if(str_contains($course, 'A')){
            return 'true';
        }
        if(str_contains($course, 'B')){
            return 'true';
        }
        if(str_contains($course, 'C')){
            return 'true';
        }
        return 'false';
    }

    public function toPassStringTwo($course)
    {
        if(str_contains($course, 'A')){
            return 'true';
        }
        if(str_contains($course, 'B')){
            return 'true';
        }
        if(str_contains($course, 'C')){
            return 'true';
        }
        if(str_contains($course, 'D')){
            return 'true';
        }
        if(str_contains($course, 'E')){
            return 'true';
        }
        return 'false';
    }

    public function verifyCandidateIndex($candidate_indexing)
    {
        if ($candidate_indexing->course_header == 'B5' || $candidate_indexing->course_header == 'A4' || $candidate_indexing->course_header == 'A5')
        {
            $candidate_indexing->update([
                'indexed' => 1,
                'visible' => 1,
                'unverified' => 0
            ]);
            return 'valid';
        } else {
            return $this->verify($candidate_indexing);
        }
    }

    public static function verify($candidate)
    {

        $valid = false;
            $english = $candidate->english ?? null;
            $biology = $candidate->biology ?? null;
            $chemistry = $candidate->chemistry ?? null;
            $health_science = $candidate->health_science ?? null;
            $integrated_science = $candidate->integrated_science ?? null;
            $mathematics = $candidate->mathematics ?? null;
            $geography = $candidate->geography ?? null;
            $agric = $candidate->agric ?? null;
            $economics = $candidate->economics ?? null;
            $physics = $candidate->physics ?? null;
            $accounting = $candidate->accounting ?? null;
            $commerce = $candidate->commerce ?? null;
            $government = $candidate->government ?? null;
            $literature = $candidate->literature ?? null;
            $yoruba = $candidate->yoruba ?? null;
            $technical_drawing = $candidate->technical_drawing ?? null;
            $general_science = $candidate->general_science ?? null;
            $food_and_nutrition = $candidate->food_and_nutrition ?? null;
            $igbo = $candidate->igbo ?? null;
            $hausa = $candidate->hausa ?? null;
            $history = $candidate->history ?? null;
            $religious_knowledge = $candidate->religious_knowledge ?? null;
            $valid = false;
            $reason = '';
        $candidate_course_header = CourseHeader::where('header_key',$candidate->course_header)->first();

        switch ($candidate_course_header->cadre)
        {
                case 'PHN':
                    $has_english = false;
                    $has_maths = false;
                    $has_biology = false;
                    $has_physics = false;
                    $has_chemistry = false;

                    if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                        $has_english = true;
                    }

                    if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                        $has_maths = true;
                    }

                    if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C')) {
                        $has_biology = true;
                    }

                    if (str_contains($chemistry, 'A') || str_contains($chemistry, 'B') || str_contains($chemistry, 'C')) {
                        $has_chemistry = true;
                    }

                    if (str_contains($physics, 'A') || str_contains($physics, 'B') || str_contains($physics, 'C')) {
                        $has_physics = true;
                    }

                    $valid = $has_english && $has_maths && $has_biology && $has_chemistry && $has_physics;
                    $reason = [];
                    $reason_items = [];

                    if (!$valid) {
                        $reason_items[] = !$has_english ? 'english' : '';
                        $reason_items[] = !$has_maths ? 'mathematics' : '';
                        $reason_items[] = !$has_biology ? 'biology' : '';
                        $reason_items[] = !$has_chemistry ? 'chemistry' : '';
                        $reason_items[] = !$has_physics ? 'physics' : '';
                    }

                    foreach ($reason_items as $reasons) {
                        if (!empty($reasons)) {
                            $reason[] = $reasons;
                        }
                    }

                    $reason = join(', ', $reason) ? 'No credit in ' . join(', ', $reason) : '';
                    break;

                case 'ND':
                    $has_english = false;
                    $has_maths = false;
                    $has_biology_or_health_science = false;
                    $has_chemistry = false;
                    $has_others = false;

                    if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                        $has_english = true;
                    }

                    if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                        $has_maths = true;
                    }

                    if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                        $has_biology_or_health_science = true;
                    }

                    if (str_contains($chemistry, 'A') || str_contains($chemistry, 'B') || str_contains($chemistry, 'C')) {
                        $has_chemistry = true;
                    }

                    if (str_contains($geography, 'A') || str_contains($geography, 'B') || str_contains($geography, 'C') || str_contains($economics, 'A') || str_contains($economics, 'B') || str_contains($economics, 'C') || str_contains($food_and_nutrition, 'A') || str_contains($food_and_nutrition, 'B') || str_contains($food_and_nutrition, 'C') || str_contains($physics, 'A') || str_contains($physics, 'B') || str_contains($physics, 'C') || str_contains($technical_drawing, 'A') || str_contains($technical_drawing, 'B') || str_contains($technical_drawing, 'C')) {
                        $has_others = true;
                    }
                    $valid = $has_english && $has_maths && $has_biology_or_health_science && $has_chemistry && $has_others;

                    $reason = [];
                    $reason_items = [];
                    if (!$valid) {
                        $reason_items[] = !$has_english ? 'english' : '';
                        $reason_items[] = !$has_maths ? 'mathematics' : '';
                        $reason_items[] = !$has_biology_or_health_science ? '1 credit in  biology or health science' : '';
                        $reason_items[] = !$has_chemistry ? 'chemistry' : '';
                        $reason_items[] = !$has_others ? '1 credit in geography, economics, food and nutrition, physics or technical drawing' : '';
                    }

                    foreach ($reason_items as $reasons) {
                        if (!empty($reasons)) {
                            $reason[] = $reasons;
                        }
                    }

                    $reason = join(', ', $reason) ? 'Does not have ' . join(', ', $reason) : '';

                    break;

                case 'EHA': //  A3
                    $req_array = [$english ?? '0', $biology ?? '0', $health_science ?? '0', $chemistry ?? '0', $food_and_nutrition ?? '0', $physics ?? '0', $general_science ?? '0', $integrated_science ?? '0'];
                    $req_array_count = array_count_values($req_array);

                    $a1_count = $req_array_count['A1'] ?? 0;
                    $a2_count = $req_array_count['A2'] ?? 0;
                    $a3_count = $req_array_count['A3'] ?? 0;
                    $b2_count = $req_array_count['B2'] ?? 0;
                    $b3_count = $req_array_count['B3'] ?? 0;
                    $b4_count = $req_array_count['B4'] ?? 0;
                    $b5_count = $req_array_count['B5'] ?? 0;
                    $b6_count = $req_array_count['B6'] ?? 0;
                    $c4_count = $req_array_count['C4'] ?? 0;
                    $c5_count = $req_array_count['C5'] ?? 0;
                    $c6_count = $req_array_count['C6'] ?? 0;

                    $d7_count = $req_array_count['D7'] ?? 0;
                    $e8_count = $req_array_count['E8'] ?? 0;

                    $total_credits = $a1_count + $a2_count + $a3_count + $b2_count + $b3_count + $b4_count + $b5_count + $b6_count + $c4_count + $c5_count + $c6_count;
                    $total_passes = $total_credits + $d7_count + $e8_count;


                    $others_array = array($mathematics ?? '0', $geography ?? '0', $agric ?? '0', $economics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0');

                    $count = array_count_values($others_array);
                    $a1_count = $count['A1'] ?? 0;
                    $a2_count = $count['A2'] ?? 0;
                    $a3_count = $count['A3'] ?? 0;
                    $b2_count = $count['B2'] ?? 0;
                    $b3_count = $count['B3'] ?? 0;
                    $b4_count = $count['B4'] ?? 0;
                    $b5_count = $count['B5'] ?? 0;
                    $b6_count = $count['B6'] ?? 0;
                    $c4_count = $count['C4'] ?? 0;
                    $c5_count = $count['C5'] ?? 0;
                    $c6_count = $count['C6'] ?? 0;
                    $d7_count = $count['D7'] ?? 0;
                    $e8_count = $count['E8'] ?? 0;

                    $total_pass_count = $a1_count + $a2_count + $a3_count + $b2_count + $b3_count + $b4_count + $b5_count + $b6_count + $c4_count + $c5_count + $c6_count + $d7_count + $e8_count;

                    $has_others = false;

                    $has_four_passes = false;
                    $has_one_requried_pass = false;


                    if($total_passes > 3) {
                        $has_four_passes = true;
                        $has_one_requried_pass = true;
                    } else if ($total_passes > 2) {
                        $has_one_requried_pass = true;
                        if($total_pass_count > 0) {
                            $has_four_passes = true;
                        }
                    } else if ($total_passes > 1) {
                        $has_one_requried_pass = true;
                        if($total_pass_count > 1) {
                            $has_four_passes = true;
                        }
                    } else if($total_passes > 0) {
                        $has_one_requried_pass = true;
                        if($total_pass_count > 2) {
                            $has_four_passes = true;
                        }
                    } else if($total_passes == 0) {
                        $has_one_requried_pass = false;
                        if($total_pass_count > 2) {
                            $has_four_passes = true;
                        }
                    }


                    $valid = $has_one_requried_pass && $has_four_passes;
                    $reason = [];
                    $reason_items = [];
                    if (!$valid && $has_four_passes) {
                        $reason_items[] = !$has_one_requried_pass ? 'a pass in one science subject' : '';
                    } else {
                        $reason_items[] = !$has_one_requried_pass ? 'a pass in one science subject' : '';
                        $reason_items[] = !$has_others ? '3 passes in other subjects' : '';
                    }
                    foreach ($reason_items as $reasons) {
                        if (!empty($reasons)) {
                            $reason[] = $reasons;
                        }
                    }

                    $reason = join(', ', $reason) ? 'Does not have ' . join(' and ', $reason) : '';
                    break;

                case 'HEP': //  A7
                    $others_array = [$geography ?? '0', $agric ?? '0', $economics ?? '0', $physics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $general_science ?? '0', $food_and_nutrition ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0', $chemistry ?? '0'];
                    $count = array_count_values($others_array);
                    $a1_count = $count['A1'] ?? 0;
                    $a2_count = $count['A2'] ?? 0;
                    $a3_count = $count['A3'] ?? 0;
                    $b2_count = $count['B2'] ?? 0;
                    $b3_count = $count['B3'] ?? 0;
                    $b4_count = $count['B4'] ?? 0;
                    $b5_count = $count['B5'] ?? 0;
                    $b6_count = $count['B6'] ?? 0;
                    $c4_count = $count['C4'] ?? 0;
                    $c5_count = $count['C5'] ?? 0;
                    $c6_count = $count['C6'] ?? 0;

                    $total_pass_count = 0;

                    if ($a1_count > 0)
                        $total_pass_count += 1;
                    if ($a2_count > 0)
                        $total_pass_count += 1;
                    if ($a3_count > 0)
                        $total_pass_count += 1;
                    if ($b2_count > 0)
                        $total_pass_count += 1;
                    if ($b3_count > 0)
                        $total_pass_count += 1;
                    if ($b4_count > 0)
                        $total_pass_count += 1;
                    if ($b5_count > 0)
                        $total_pass_count += 1;
                    if ($b6_count > 0)
                        $total_pass_count += 1;
                    if ($c4_count > 0)
                        $total_pass_count += 1;
                    if ($c5_count > 0)
                        $total_pass_count += 1;
                    if ($c6_count > 0)
                        $total_pass_count += 1;
                    $has_english = false;
                    $has_maths = false;
                    $has_biology_health_science = false;
                    $has_others = $total_pass_count > 0;

                    if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                        $has_english = true;
                    }
                    if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                        $has_maths = true;
                    }

                    if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                        $has_biology_health_science = true;
                    }

                    if ($has_english && $has_maths && ((str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C')) && (str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')))) {
                        $valid = true;
                    } else {
                        $valid = $has_english && $has_maths && $has_biology_health_science && $has_others;
                    }

                    $reason = [];
                    $reason_items = [];
                    if (!$valid) {
                        $reason_items[] = !$has_english ? 'English' : '';
                        $reason_items[] = !$has_maths ? 'Mathematics' : '';
                        $reason_items[] = !$has_biology_health_science ? 'Biology or Health science' : '';
                        $reason_items[] = !$has_others ? 'other subject' : '';
                    }

                    foreach ($reason_items as $reasons) {
                        if (!empty($reasons)) {
                            $reason[] = $reasons;
                        }
                    }

                    $reason = join(', ', $reason) ? 'Does not have credit in ' . join(', ', $reason) : '';

                    break;

                case 'EVT':
                    $others_array = array($mathematics ?? '0', $geography ?? '0', $physics ?? '0', $chemistry ?? '0', $agric ?? '0');
                    $other_subjects = array($mathematics ?? '0', $geography ?? '0', $physics ?? '0', $chemistry ?? '0', $agric ?? '0', $economics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $general_science ?? '0', $food_and_nutrition ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0');
                    $count = array_count_values($others_array);
                    $a1_count = $count['A1'] ?? 0;
                    $a2_count = $count['A2'] ?? 0;
                    $a3_count = $count['A3'] ?? 0;
                    $b2_count = $count['B2'] ?? 0;
                    $b3_count = $count['B3'] ?? 0;
                    $b4_count = $count['B4'] ?? 0;
                    $b5_count = $count['B5'] ?? 0;
                    $b6_count = $count['B6'] ?? 0;
                    $c4_count = $count['C4'] ?? 0;
                    $c5_count = $count['C5'] ?? 0;
                    $c6_count = $count['C6'] ?? 0;

                    $total_pass_count = 0;

                    if ($a1_count > 0)
                        $total_pass_count += 1;
                    if ($a2_count > 0)
                        $total_pass_count += 1;
                    if ($a3_count > 0)
                        $total_pass_count += 1;
                    if ($b2_count > 0)
                        $total_pass_count += 1;
                    if ($b3_count > 0)
                        $total_pass_count += 1;
                    if ($b4_count > 0)
                        $total_pass_count += 1;
                    if ($b5_count > 0)
                        $total_pass_count += 1;
                    if ($b6_count > 0)
                        $total_pass_count += 1;
                    if ($c4_count > 0)
                        $total_pass_count += 1;
                    if ($c5_count > 0)
                        $total_pass_count += 1;
                    if ($c6_count > 0)
                        $total_pass_count += 1;

                    if ($a1_count > 1)
                        $total_pass_count += 1;
                    if ($a2_count > 1)
                        $total_pass_count += 1;
                    if ($a3_count > 1)
                        $total_pass_count += 1;
                    if ($b2_count > 1)
                        $total_pass_count += 1;
                    if ($b3_count > 1)
                        $total_pass_count += 1;
                    if ($b4_count > 1)
                        $total_pass_count += 1;
                    if ($b5_count > 1)
                        $total_pass_count += 1;
                    if ($b6_count > 1)
                        $total_pass_count += 1;
                    if ($c4_count > 1)
                        $total_pass_count += 1;
                    if ($c5_count > 1)
                        $total_pass_count += 1;
                    if ($c6_count > 1)
                        $total_pass_count += 1;

                    $count_2 = array_count_values($other_subjects);
                    $a1_count_2 = $count_2['A1'] ?? 0;
                    $a2_count_2 = $count_2['A2'] ?? 0;
                    $a3_count_2 = $count_2['A3'] ?? 0;
                    $b2_count_2 = $count_2['B2'] ?? 0;
                    $b3_count_2 = $count_2['B3'] ?? 0;
                    $b4_count_2 = $count_2['B4'] ?? 0;
                    $b5_count_2 = $count_2['B5'] ?? 0;
                    $b6_count_2 = $count_2['B6'] ?? 0;
                    $c4_count_2 = $count_2['C4'] ?? 0;
                    $c5_count_2 = $count_2['C5'] ?? 0;
                    $c6_count_2 = $count_2['C6'] ?? 0;
                    $d7_count_2 = $count_2['D7'] ?? 0;
                    $e8_count_2 = $count_2['E8'] ?? 0;

                    $total_pass_count_2 = 0;

                    if ($a1_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($a2_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($a3_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($b2_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($b3_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($b4_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($b5_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($b6_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($c4_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($c5_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($c6_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($d7_count_2 > 0)
                        $total_pass_count_2 += 1;
                    if ($e8_count_2 > 0)
                        $total_pass_count_2 += 1;

                    $has_english = false;
                    $has_biology_health_science = false;
                    $has_two_science_subjects = $total_pass_count > 1;
                    $has_others_subject = $total_pass_count_2 > 0;

                    if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                        $has_english = true;
                    }

                    if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                        $has_biology_health_science = true;
                    }

                    $valid = $has_english && $has_biology_health_science && $has_two_science_subjects && $has_others_subject;

                    $reason = [];
                    $reason_items = [];

                    if (!$valid) {
                        $reason_items[] = !$has_english || !$has_biology_health_science ? '2 required credits in English and Biology/Health Science' : '';
                        $reason_items[] = !$has_two_science_subjects ? '2 required credits in Geography, Chemistry, Physics, Agric, Technical Drawing, Mathematics' : '';
                        $reason_items[] = !$has_others_subject ? '1 pass in Economics, Integrated Science, Accounting, Commerce, Government, Literature, Yoruba, General science, Food and Nutrition, Igbo, Hausa, History or Religious Knowledge' : '';
                    }

                    foreach ($reason_items as $reasons) {
                        if (!empty($reasons)) {
                            $reason[] = $reasons;
                        }
                    }

                    $reason = join(', ', $reason) ? 'Does not have ' . join(' and ', $reason) : '';
                    break;

                  default:
                $has_english = false;
                $has_maths = false;
                $has_biology = false;
                $has_physics = false;
                $has_chemistry = false;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }

                if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                    $has_maths = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C')) {
                    $has_biology = true;
                }

                if (str_contains($chemistry, 'A') || str_contains($chemistry, 'B') || str_contains($chemistry, 'C')) {
                    $has_chemistry = true;
                }

                if (str_contains($physics, 'A') || str_contains($physics, 'B') || str_contains($physics, 'C')) {
                    $has_physics = true;
                }

                $valid = $has_english && $has_maths && $has_biology && $has_chemistry && $has_physics;
                $reason = [];
                $reason_items = [];

                if (!$valid) {
                    $reason_items[] = !$has_english ? 'english' : '';
                    $reason_items[] = !$has_maths ? 'mathematics' : '';
                    $reason_items[] = !$has_biology ? 'biology' : '';
                    $reason_items[] = !$has_chemistry ? 'chemistry' : '';
                    $reason_items[] = !$has_physics ? 'physics' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'No credit in ' . join(', ', $reason) : '';
                break;
        }
            if ($valid) {
                $candidate->update([
                    'indexed' => 1,
                    'visible' => 1,
                    'unverified' => 0,
                    'reason' => $reason
                ]);
            } else {
                $candidate->update([
                    'unverified' => 1,
                    'visible' => 1,
                    'reason' => $reason
                ]);
            }
        return $valid ? '  VERIFICATION IS VALID ' : '  VERIFICATION IS INVALID ';
    }

    public function getCandidate()
    {
        $candidate_index =  CandidateIndexing::select('candidate_index', 'first_name', 'middle_name', 'last_name', 'school_code')->get();
        $datas = collect($candidate_index);
        $data = $datas->chunk(1);
        return $this->successResponse($data," ");
    }

    Public function verifyCandidateIndexForTrainingSchool()
    {
        if(!request()->id){
            return $this->errorResponse('Candidate ID is required');
        }

        if(!request()->candidate_index)
        {
            return $this->errorResponse('Candidate Index is required');
        }

        $candidate_indexing = CandidateIndexing::where('candidate_index',request()->candidate_index)
                                                       ->where('id', request()->id)->first();

        if(! $candidate_indexing)
        {
            return $this->errorResponse('Candidate Index is Invalid');
        }


        $course_header = request()->course_header ?? $candidate_indexing->course_header;
        if ($course_header == 'B5' || $course_header == 'A4' || $course_header == 'A5')
        {
            $candidate_indexing->update([
                'indexed' => 1,
                'visible' => 1,
                'unverified' => 0
            ]);
            return $this->successResponse($candidate_indexing, "VERIFICATION IS VALID");
        } else {
             $status = $this->verifyTrainingSchoolIndexedCandidate($candidate_indexing,$course_header);
              return $this->successResponse($candidate_indexing, $status);
        }
    }

    public function verifyTrainingSchoolIndexedCandidate($candidate_indexing,$course_header)
    {
        $valid = false;
        $english = request()->english ?? $candidate_indexing->english ?? null;
        $biology = request()->biology ?? $candidate_indexing->biology ?? null;
        $chemistry = request()->chemistry ?? $candidate_indexing->chemistry ?? null;
        $health_science = request()->health_science ??$candidate_indexing->health_science ?? null;
        $integrated_science = request()->integrated_science ?? $candidate_indexing->integrated_science ?? null;
        $mathematics = request()->mathematics ?? $candidate_indexing->mathematics ?? null;
        $geography = request()->geography ?? $candidate_indexing->geography ?? null;
        $agric = request()->agric ?? $candidate_indexing->agric ?? null;
        $economics = request()->economics ?? $candidate_indexing->economics ?? null;
        $physics = request()->physics ?? $candidate_indexing->physics ?? null;
        $accounting = request()->accounting ?? $candidate_indexing->accounting ?? null;
        $commerce = request()->commerce ?? $candidate_indexing->commerce ?? null;
        $government = request()->government ?? $candidate_indexing->government ?? null;
        $literature =  request()->literature ?? $candidate_indexing->literature ?? null;
        $yoruba = request()->yoruba ??  $candidate_indexing->yoruba ?? null;
        $technical_drawing = request()->technical_drawing ?? $candidate_indexing->technical_drawing ?? null;
        $general_science = request()->general_science ?? $candidate_indexing->general_science ?? null;
        $food_and_nutrition = request()->food_and_nutrition ?? $candidate_indexing->food_and_nutrition ?? null;
        $igbo = request()->igbo ?? $candidate_indexing->igbo ?? null;
        $hausa = request()->hausa ?? $candidate_indexing->hausa ?? null;
        $history = request()->history ?? $candidate_indexing->history ?? null;
        $religious_knowledge = request()->religious_knowledge ?? $candidate_indexing->religious_knowledge ?? null;
        $valid = false;
        $reason = '';
        $candidate_course_header = CourseHeader::where('header_key',$course_header)->first();

        switch ($candidate_course_header->cadre)
        {
            case 'PHN':
                $has_english = false;
                $has_maths = false;
                $has_biology = false;
                $has_physics = false;
                $has_chemistry = false;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }

                if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                    $has_maths = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C')) {
                    $has_biology = true;
                }

                if (str_contains($chemistry, 'A') || str_contains($chemistry, 'B') || str_contains($chemistry, 'C')) {
                    $has_chemistry = true;
                }

                if (str_contains($physics, 'A') || str_contains($physics, 'B') || str_contains($physics, 'C')) {
                    $has_physics = true;
                }

                $valid = $has_english && $has_maths && $has_biology && $has_chemistry && $has_physics;
                $reason = [];
                $reason_items = [];

                if (!$valid) {
                    $reason_items[] = !$has_english ? 'english' : '';
                    $reason_items[] = !$has_maths ? 'mathematics' : '';
                    $reason_items[] = !$has_biology ? 'biology' : '';
                    $reason_items[] = !$has_chemistry ? 'chemistry' : '';
                    $reason_items[] = !$has_physics ? 'physics' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'No credit in ' . join(', ', $reason) : '';
                break;

            case 'ND':
                $has_english = false;
                $has_maths = false;
                $has_biology_or_health_science = false;
                $has_chemistry = false;
                $has_others = false;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }

                if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                    $has_maths = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                    $has_biology_or_health_science = true;
                }

                if (str_contains($chemistry, 'A') || str_contains($chemistry, 'B') || str_contains($chemistry, 'C')) {
                    $has_chemistry = true;
                }

                if (str_contains($geography, 'A') || str_contains($geography, 'B') || str_contains($geography, 'C') || str_contains($economics, 'A') || str_contains($economics, 'B') || str_contains($economics, 'C') || str_contains($food_and_nutrition, 'A') || str_contains($food_and_nutrition, 'B') || str_contains($food_and_nutrition, 'C') || str_contains($physics, 'A') || str_contains($physics, 'B') || str_contains($physics, 'C') || str_contains($technical_drawing, 'A') || str_contains($technical_drawing, 'B') || str_contains($technical_drawing, 'C')) {
                    $has_others = true;
                }
                $valid = $has_english && $has_maths && $has_biology_or_health_science && $has_chemistry && $has_others;

                $reason = [];
                $reason_items = [];
                if (!$valid) {
                    $reason_items[] = !$has_english ? 'english' : '';
                    $reason_items[] = !$has_maths ? 'mathematics' : '';
                    $reason_items[] = !$has_biology_or_health_science ? '1 credit in  biology or health science' : '';
                    $reason_items[] = !$has_chemistry ? 'chemistry' : '';
                    $reason_items[] = !$has_others ? '1 credit in geography, economics, food and nutrition, physics or technical drawing' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'Does not have ' . join(', ', $reason) : '';

                break;

            case 'EHA': //  A3
                $req_array = [$english ?? '0', $biology ?? '0', $health_science ?? '0', $chemistry ?? '0', $food_and_nutrition ?? '0', $physics ?? '0', $general_science ?? '0', $integrated_science ?? '0'];
                $req_array_count = array_count_values($req_array);

                $a1_count = $req_array_count['A1'] ?? 0;
                $a2_count = $req_array_count['A2'] ?? 0;
                $a3_count = $req_array_count['A3'] ?? 0;
                $b2_count = $req_array_count['B2'] ?? 0;
                $b3_count = $req_array_count['B3'] ?? 0;
                $b4_count = $req_array_count['B4'] ?? 0;
                $b5_count = $req_array_count['B5'] ?? 0;
                $b6_count = $req_array_count['B6'] ?? 0;
                $c4_count = $req_array_count['C4'] ?? 0;
                $c5_count = $req_array_count['C5'] ?? 0;
                $c6_count = $req_array_count['C6'] ?? 0;

                $d7_count = $req_array_count['D7'] ?? 0;
                $e8_count = $req_array_count['E8'] ?? 0;

                $total_credits = $a1_count + $a2_count + $a3_count + $b2_count + $b3_count + $b4_count + $b5_count + $b6_count + $c4_count + $c5_count + $c6_count;
                $total_passes = $total_credits + $d7_count + $e8_count;


                $others_array = array($mathematics ?? '0', $geography ?? '0', $agric ?? '0', $economics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0');

                $count = array_count_values($others_array);
                $a1_count = $count['A1'] ?? 0;
                $a2_count = $count['A2'] ?? 0;
                $a3_count = $count['A3'] ?? 0;
                $b2_count = $count['B2'] ?? 0;
                $b3_count = $count['B3'] ?? 0;
                $b4_count = $count['B4'] ?? 0;
                $b5_count = $count['B5'] ?? 0;
                $b6_count = $count['B6'] ?? 0;
                $c4_count = $count['C4'] ?? 0;
                $c5_count = $count['C5'] ?? 0;
                $c6_count = $count['C6'] ?? 0;
                $d7_count = $count['D7'] ?? 0;
                $e8_count = $count['E8'] ?? 0;

                $total_pass_count = $a1_count + $a2_count + $a3_count + $b2_count + $b3_count + $b4_count + $b5_count + $b6_count + $c4_count + $c5_count + $c6_count + $d7_count + $e8_count;

                $has_others = false;

                $has_four_passes = false;
                $has_one_requried_pass = false;


                if($total_passes > 3) {
                    $has_four_passes = true;
                    $has_one_requried_pass = true;
                } else if ($total_passes > 2) {
                    $has_one_requried_pass = true;
                    if($total_pass_count > 0) {
                        $has_four_passes = true;
                    }
                } else if ($total_passes > 1) {
                    $has_one_requried_pass = true;
                    if($total_pass_count > 1) {
                        $has_four_passes = true;
                    }
                } else if($total_passes > 0) {
                    $has_one_requried_pass = true;
                    if($total_pass_count > 2) {
                        $has_four_passes = true;
                    }
                } else if($total_passes == 0) {
                    $has_one_requried_pass = false;
                    if($total_pass_count > 2) {
                        $has_four_passes = true;
                    }
                }


                $valid = $has_one_requried_pass && $has_four_passes;
                $reason = [];
                $reason_items = [];
                if (!$valid && $has_four_passes) {
                    $reason_items[] = !$has_one_requried_pass ? 'a pass in one science subject' : '';
                } else {
                    $reason_items[] = !$has_one_requried_pass ? 'a pass in one science subject' : '';
                    $reason_items[] = !$has_others ? '3 passes in other subjects' : '';
                }
                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'Does not have ' . join(' and ', $reason) : '';
                break;

            case 'HEP': //  A7
                $others_array = [$geography ?? '0', $agric ?? '0', $economics ?? '0', $physics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $general_science ?? '0', $food_and_nutrition ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0', $chemistry ?? '0'];
                $count = array_count_values($others_array);
                $a1_count = $count['A1'] ?? 0;
                $a2_count = $count['A2'] ?? 0;
                $a3_count = $count['A3'] ?? 0;
                $b2_count = $count['B2'] ?? 0;
                $b3_count = $count['B3'] ?? 0;
                $b4_count = $count['B4'] ?? 0;
                $b5_count = $count['B5'] ?? 0;
                $b6_count = $count['B6'] ?? 0;
                $c4_count = $count['C4'] ?? 0;
                $c5_count = $count['C5'] ?? 0;
                $c6_count = $count['C6'] ?? 0;

                $total_pass_count = 0;

                if ($a1_count > 0)
                    $total_pass_count += 1;
                if ($a2_count > 0)
                    $total_pass_count += 1;
                if ($a3_count > 0)
                    $total_pass_count += 1;
                if ($b2_count > 0)
                    $total_pass_count += 1;
                if ($b3_count > 0)
                    $total_pass_count += 1;
                if ($b4_count > 0)
                    $total_pass_count += 1;
                if ($b5_count > 0)
                    $total_pass_count += 1;
                if ($b6_count > 0)
                    $total_pass_count += 1;
                if ($c4_count > 0)
                    $total_pass_count += 1;
                if ($c5_count > 0)
                    $total_pass_count += 1;
                if ($c6_count > 0)
                    $total_pass_count += 1;
                $has_english = false;
                $has_maths = false;
                $has_biology_health_science = false;
                $has_others = $total_pass_count > 0;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }
                if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                    $has_maths = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                    $has_biology_health_science = true;
                }

                if ($has_english && $has_maths && ((str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C')) && (str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')))) {
                    $valid = true;
                } else {
                    $valid = $has_english && $has_maths && $has_biology_health_science && $has_others;
                }

                $reason = [];
                $reason_items = [];
                if (!$valid) {
                    $reason_items[] = !$has_english ? 'English' : '';
                    $reason_items[] = !$has_maths ? 'Mathematics' : '';
                    $reason_items[] = !$has_biology_health_science ? 'Biology or Health science' : '';
                    $reason_items[] = !$has_others ? 'other subject' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'Does not have credit in ' . join(', ', $reason) : '';

                break;

            case 'EVT':
                $others_array = array($mathematics ?? '0', $geography ?? '0', $physics ?? '0', $chemistry ?? '0', $agric ?? '0');
                $other_subjects = array($mathematics ?? '0', $geography ?? '0', $physics ?? '0', $chemistry ?? '0', $agric ?? '0', $economics ?? '0', $accounting ?? '0', $commerce ?? '0', $government ?? '0', $literature ?? '0', $yoruba ?? '0', $technical_drawing ?? '0', $general_science ?? '0', $food_and_nutrition ?? '0', $igbo ?? '0', $hausa ?? '0', $history ?? '0', $religious_knowledge ?? '0');
                $count = array_count_values($others_array);
                $a1_count = $count['A1'] ?? 0;
                $a2_count = $count['A2'] ?? 0;
                $a3_count = $count['A3'] ?? 0;
                $b2_count = $count['B2'] ?? 0;
                $b3_count = $count['B3'] ?? 0;
                $b4_count = $count['B4'] ?? 0;
                $b5_count = $count['B5'] ?? 0;
                $b6_count = $count['B6'] ?? 0;
                $c4_count = $count['C4'] ?? 0;
                $c5_count = $count['C5'] ?? 0;
                $c6_count = $count['C6'] ?? 0;

                $total_pass_count = 0;

                if ($a1_count > 0)
                    $total_pass_count += 1;
                if ($a2_count > 0)
                    $total_pass_count += 1;
                if ($a3_count > 0)
                    $total_pass_count += 1;
                if ($b2_count > 0)
                    $total_pass_count += 1;
                if ($b3_count > 0)
                    $total_pass_count += 1;
                if ($b4_count > 0)
                    $total_pass_count += 1;
                if ($b5_count > 0)
                    $total_pass_count += 1;
                if ($b6_count > 0)
                    $total_pass_count += 1;
                if ($c4_count > 0)
                    $total_pass_count += 1;
                if ($c5_count > 0)
                    $total_pass_count += 1;
                if ($c6_count > 0)
                    $total_pass_count += 1;

                if ($a1_count > 1)
                    $total_pass_count += 1;
                if ($a2_count > 1)
                    $total_pass_count += 1;
                if ($a3_count > 1)
                    $total_pass_count += 1;
                if ($b2_count > 1)
                    $total_pass_count += 1;
                if ($b3_count > 1)
                    $total_pass_count += 1;
                if ($b4_count > 1)
                    $total_pass_count += 1;
                if ($b5_count > 1)
                    $total_pass_count += 1;
                if ($b6_count > 1)
                    $total_pass_count += 1;
                if ($c4_count > 1)
                    $total_pass_count += 1;
                if ($c5_count > 1)
                    $total_pass_count += 1;
                if ($c6_count > 1)
                    $total_pass_count += 1;

                $count_2 = array_count_values($other_subjects);
                $a1_count_2 = $count_2['A1'] ?? 0;
                $a2_count_2 = $count_2['A2'] ?? 0;
                $a3_count_2 = $count_2['A3'] ?? 0;
                $b2_count_2 = $count_2['B2'] ?? 0;
                $b3_count_2 = $count_2['B3'] ?? 0;
                $b4_count_2 = $count_2['B4'] ?? 0;
                $b5_count_2 = $count_2['B5'] ?? 0;
                $b6_count_2 = $count_2['B6'] ?? 0;
                $c4_count_2 = $count_2['C4'] ?? 0;
                $c5_count_2 = $count_2['C5'] ?? 0;
                $c6_count_2 = $count_2['C6'] ?? 0;
                $d7_count_2 = $count_2['D7'] ?? 0;
                $e8_count_2 = $count_2['E8'] ?? 0;

                $total_pass_count_2 = 0;

                if ($a1_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($a2_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($a3_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b2_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b3_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b4_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b5_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($b6_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($c4_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($c5_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($c6_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($d7_count_2 > 0)
                    $total_pass_count_2 += 1;
                if ($e8_count_2 > 0)
                    $total_pass_count_2 += 1;

                $has_english = false;
                $has_biology_health_science = false;
                $has_two_science_subjects = $total_pass_count > 1;
                $has_others_subject = $total_pass_count_2 > 0;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C') || str_contains($health_science, 'A') || str_contains($health_science, 'B') || str_contains($health_science, 'C')) {
                    $has_biology_health_science = true;
                }

                $valid = $has_english && $has_biology_health_science && $has_two_science_subjects && $has_others_subject;

                $reason = [];
                $reason_items = [];

                if (!$valid) {
                    $reason_items[] = !$has_english || !$has_biology_health_science ? '2 required credits in English and Biology/Health Science' : '';
                    $reason_items[] = !$has_two_science_subjects ? '2 required credits in Geography, Chemistry, Physics, Agric, Technical Drawing, Mathematics' : '';
                    $reason_items[] = !$has_others_subject ? '1 pass in Economics, Integrated Science, Accounting, Commerce, Government, Literature, Yoruba, General science, Food and Nutrition, Igbo, Hausa, History or Religious Knowledge' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? ' Does not have ' . join(' and ', $reason) : ' ';
                break;

            default:
                $has_english = false;
                $has_maths = false;
                $has_biology = false;
                $has_physics = false;
                $has_chemistry = false;

                if (str_contains($english, 'A') || str_contains($english, 'B') || str_contains($english, 'C')) {
                    $has_english = true;
                }

                if (str_contains($mathematics, 'A') || str_contains($mathematics, 'B') || str_contains($mathematics, 'C')) {
                    $has_maths = true;
                }

                if (str_contains($biology, 'A') || str_contains($biology, 'B') || str_contains($biology, 'C')) {
                    $has_biology = true;
                }

                if (str_contains($chemistry, 'A') || str_contains($chemistry, 'B') || str_contains($chemistry, 'C')) {
                    $has_chemistry = true;
                }

                if (str_contains($physics, 'A') || str_contains($physics, 'B') || str_contains($physics, 'C')) {
                    $has_physics = true;
                }

                $valid = $has_english && $has_maths && $has_biology && $has_chemistry && $has_physics;
                $reason = [];
                $reason_items = [];

                if (!$valid) {
                    $reason_items[] = !$has_english ? 'english' : '';
                    $reason_items[] = !$has_maths ? 'mathematics' : '';
                    $reason_items[] = !$has_biology ? 'biology' : '';
                    $reason_items[] = !$has_chemistry ? 'chemistry' : '';
                    $reason_items[] = !$has_physics ? 'physics' : '';
                }

                foreach ($reason_items as $reasons) {
                    if (!empty($reasons)) {
                        $reason[] = $reasons;
                    }
                }

                $reason = join(', ', $reason) ? 'No credit in ' . join(', ', $reason) : '';
                break;
        }

        $o_levels_array = [
            'english' => $english,
            'biology' => $biology,
            'chemistry' =>  $chemistry,
            'health_science' => $health_science,
            'integrated_science' => $integrated_science,
            'mathematics' => $mathematics,
            'geography'  => $geography,
            'agric' => $agric,
            'economics' => $economics,
            'physics' =>  $physics,
            'accounting' =>  $accounting,
            'commerce' =>  $commerce,
            'government' =>  $government,
            'literature'    =>  $literature,
            'yoruba'   =>  $yoruba,
            'technical_drawing'  =>  $technical_drawing,
            'general_science'  =>  $general_science,
            'food_and_nutrition' =>  $food_and_nutrition,
            'igbo' =>  $igbo,
            'hausa' =>  $hausa,
            'history' =>  $history,
            'religious_knowledge' => $religious_knowledge,
        ];
        if ($valid) {
            $candidate_indexing->update([
                'indexed' => 1,
                'visible' => 1,
                'unverified' => 0,
                'reason' => $reason,
             ] + $o_levels_array);
        } else {
            $candidate_indexing->update([
                'unverified' => 1,
                'visible' => 1,
                'reason' => $reason,
            ] + $o_levels_array);
        }
        return $valid ? ' VERIFICATION IS VALID ' : ' VERIFICATION IS INVALID ';
    }

    public function delete()
    {
        if(!CandidateIndexing::where('candidate_index', request()->input('candidate_index'))->first())
        {
            return $this->errorResponse("Candidate Index Info Doesnt Exist", 401);
        }

        $data = CandidateIndexing::where('candidate_index', request()->input('candidate_index'))->get();
        foreach (collect($data) as $item)
        {
            $item->delete();
        }

        return $this->successResponse([], "Candidate Index Deleted Successfully");
    }

}
