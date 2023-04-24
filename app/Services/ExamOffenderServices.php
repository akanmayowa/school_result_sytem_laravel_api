<?php

namespace App\Services;

use App\Models\SchoolResit;
use App\Models\User;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use App\Models\ExamOffence;
use App\Models\CourseModule;
use App\Models\ExamOffender;
use App\Models\ScoreMarkerOne;
use App\Models\ScoreMarkerTwo;
use App\Traits\ResponsesTrait;
use App\Models\CandidateIndexing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ExamOffenderResource;
use App\Repositories\ExamOffenderRepository;
use App\Repositories\CandidateIndexingRepository;

class ExamOffenderServices{

    use ResponsesTrait;

    public $examOffenderRepository;
    public $candidateIndexingRepository;

    public function __construct(ExamOffenderRepository $examOffenderRepository,
                                CandidateIndexingRepository $candidateIndexingRepository)
    {
        $this->examOffenderRepository = $examOffenderRepository;
        $this->candidateIndexingRepository = $candidateIndexingRepository;
        $this->candidateIndexing = new CandidateIndexing;
        $this->examOffender = new ExamOffender;
        $this->examOffence = new ExamOffence;
        $this->courseModule = new CourseModule;
        $this->scoreMarkerOne = new ScoreMarkerOne;
        $this->scoreMarkerTwo = new ScoreMarkerTwo;
    }

    public function fatchAllExamOffender()
     {

         if(request()->school_code AND request()->course_header AND request()->exam_year AND !empty(request()->school_code) AND  !empty(request()->exam_year) AND !empty(request()->course_header))
         {
             $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
             $data = [
                 'examOffenders' =>  ExamOffender::with('singleOffence', 'candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                     ->where('registration_date','LIKE', '%' . $exam_year)
                     ->where('course_header', request()->course_header)
                     ->where('school_code', request()->school_code)
                     ->groupBy('candidate_index')
                     ->orderBy('candidate_index','asc')
                     ->get(),
             ];
             return $this->successResponse($data, "Retrieving Exam Offender information");
         }
         //working
         if(request()->exam_year AND request()->school_code AND !empty(request()->exam_year) AND !empty(request()->school_code))
         {
             $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
             $data = [
                 'examOffenders' =>  ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                     ->where('registration_date','LIKE', '%' . $exam_year . '%')
                     ->where('school_code', request()->school_code)
                     ->groupBy('candidate_index')
                     ->orderBy('candidate_index','asc')
                     ->get(),             ];
             return $this->successResponse($data, "Retrieving Exam Offender information");
         }
         /// working fine
         if(request()->exam_year AND request()->course_header AND !empty(request()->exam_year) AND !empty(request()->course_header))
         {
             $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
             $data = [
                 'examOffenders' =>  ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                     ->where('registration_date','LIKE', '%' . $exam_year)
                     ->where('course_header', request()->course_header)
                     ->groupBy('candidate_index')
                     ->orderBy('candidate_index','asc')
                     ->get(),
                 ];
             return $this->successResponse($data, "Retrieving Exam Offender information");
         }
         ///working
         if(request()->school_code && request()->course_header)
         {
             $data = [
                 'examOffenders' => ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                                               ->where('school_code',  request()->school_code)
                                                ->where('course_header',  request()->course_header)
                     ->groupBy('candidate_index')
                     ->orderBy('candidate_index','asc')
                     ->get(),
                 ];
             return $this->successResponse($data, "Retrieving Exam Offender information");
         }
         //working
         if(request()->has('course_header'))
         {
             $data = [
                 'examOffenders' =>  $this->examOffender
                     ->where('course_header', 'LIKE', '%' . request()->input('course_header') . '%')
                     ->with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index')
                     ->groupBy('candidate_index')
                     ->orderBy('candidate_index','asc')
                     ->get(),
             ];

             return $this->successResponse($data, "Retrieving Exam Offender information");
         }
         // working
         if(request()->has('exam_year'))
         {
             $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
             $data = [
                 'examOffenders' =>  ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                     ->where('registration_date','LIKE', '%' . $exam_year)
                     ->orderBy('candidate_index','asc')
                     ->groupBy('candidate_index')
                     ->get(),
             ];
             return $this->successResponse($data, "Retrieving Exam Offender information");
         }
         //working
         if(request()->has('candidate_index'))
         {
             $data = [
                 'examOffenders' => ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                     ->whereHas('candidateIndexForExamOffender', function ($query) {
                         $query->where(function ($q){
                             $q->whereNotNull('id')->whereNotNull('candidate_index');
                         });
                     })
                     ->where('candidate_index', 'LIKE', '%' . request()->input('candidate_index') . '%')
//                     ->groupBy('candidate_index')
                     ->orderBy('candidate_index','asc')
                     ->get(),
             ];
             return $this->successResponse($data, "Retrieving Exam Offender information");
         }
         //working
         if(request()->has('school_code'))
         {
             $data = [
                 'examOffenders' =>  $this->examOffender
                     ->where('school_code', 'LIKE', '%' . request()->input('school_code') . '%')
                     ->with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index')
                     ->groupBy('candidate_index')
                     ->orderBy('candidate_index','asc')
                     ->get(),
             ];
             return $this->successResponse($data, "Retrieving Exam Offender information");
         }
     }

//->whereNotNull('name')

    public function fetchAllExamOffenderVersion2()
    {
        if( request()->course_header AND request()->exam_year AND  !empty(request()->exam_year) AND !empty(request()->course_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $data = [
                'examOffenders' =>  ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                    ->where('registration_date','LIKE', '%' . $exam_year)
                    ->where('course_header', request()->course_header)
                    ->where('school_code', auth()->user()->operator_id)->orderByDesc('id')
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        //working
        if(request()->exam_year AND !empty(request()->exam_year))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $data = [
                'examOffenders' =>  ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                    ->where('registration_date','LIKE', '%' . $exam_year . '%')
                    ->where('school_code', auth()->user()->operator_id)->orderByDesc('id')
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        /// working fine
        if(request()->exam_year AND request()->course_header AND !empty(request()->exam_year) AND !empty(request()->course_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $data = [
                'examOffenders' =>  ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                    ->where('registration_date','LIKE', '%' . $exam_year)
                    ->where('course_header', request()->course_header)
                    ->where('school_code', auth()->user()->operator_id)
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        //working
        if(request()->has('course_header'))
        {
            $data = [
                'examOffenders' =>  $this->examOffender
                    ->where('course_header', 'LIKE', '%' . request()->input('course_header') . '%')
                    ->where('school_code', auth()->user()->operator_id)
                    ->with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index')
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        // working
        if(request()->has('exam_year'))
        {
            $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $data = [
                'examOffenders' =>  ExamOffender::with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index,exam_id')
                    ->where('registration_date','LIKE', '%' . $exam_year)
                    ->where('school_code', auth()->user()->operator_id)
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        //working
        if(request()->has('candidate_index'))
        {
            $data = [
                'examOffenders' =>  $this->examOffender
                    ->where('candidate_index', 'LIKE', '%' . request()->input('candidate_index') . '%')
                    ->where('school_code', auth()->user()->operator_id)
                    ->with('candidateIndexForExamOffender:first_name,last_name,school_code,candidate_index')
                    ->orderBy('candidate_index','asc')
                    ->groupBy('candidate_index')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
    }

    public function createExamOffender(array $data)
    {
        DB::beginTransaction();
        $date = Carbon::createFromFormat('Y-m-d', request()->exam_date);
        $examYearMonth = $date->format('y') . $date->format('m'); // 0222
        $data['exam_date'] = $date;
        $data['registration_date'] = $date->format('m').$date->format('y');
        $data['course_header'] = $data['header_key'];
        $examOffence = $this->examOffence->where('id', $data['exam_offence_id'])->first(); // this will always return data beacuse it is validated in the controller
        if($examOffence->id != 3)
        {
            $data['duration'] = $examOffence->duration ?? null;
        }
        $school_code = CandidateIndexing::where('school_code', $data['school_code'])
                                        ->where('candidate_index', $data['candidate_index'])
                                        ->first();
        if(!$school_code) {
            return $this->errorResponse("Invalid School Code Entered");
        }

        if($this->examOffender->where('candidate_index', $data['candidate_index'])->where('exam_date', $data['exam_date'])->first()){
            return $this->errorResponse('Record already exist for this candidate', 409);
        }
        $examOffender = $this->examOffender->create($data);
        if(!$examOffender){
            return $this->errorResponse('Unable to create record at this time, please try again', 500);
        }
        $processScoreMarkers = $this->processScoreMarkers($data['candidate_index'], $data['header_key'], $examYearMonth);
        if($processScoreMarkers != true){
            return $this->errorResponse('Unable to create score markers', 500);
        }
        DB::commit();
        return $this->successResponse(new ExamOffenderResource($examOffender), 'Exam offender created');
    }

    public function getExamOffender()
    {
         $exam_offence = $this->examOffence->get();
         return  $this->successResponse($exam_offence, " ");
    }

    public function processScoreMarkers($candidateIndex, $headerKey, $examDate)
    {
        $courseModules = $this->courseModule->where('header_key', $headerKey)->get();
        //  Delete candidates scores
        $this->scoreMarkerOne->where('candidate_index', $candidateIndex)->delete();
        $this->scoreMarkerTwo->where('candidate_index', $candidateIndex)->delete();
        foreach ($courseModules as $courseModule) {
            $courseModuleData['candidate_index'] = $candidateIndex;
            $courseModuleData['course_header'] = $candidateIndex;
            $courseModuleData['exam_date'] = $examDate;
            $courseModuleData['course_key'] = $courseModule['course_key'];
            $this->scoreMarkerOne->create($courseModuleData);
            $this->scoreMarkerTwo->create($courseModuleData);
        }
        return true;
    }

    public function updateExamOffencer($data, $id)
    {
        DB::beginTransaction();
        $exam_offender = (new ExamOffender())->find($id);
        if(!$exam_offender){
            return $this->errorResponse("Candidate ID doesnt Exists");
        }
        $exam_offender->candidate_index = $data['candidate_index'];
        $exam_offender->registration_date = $data['registration_date'];
        if(request()->exam_date){
            $exam_offender->exam_date = Carbon::createFromFormat('Y-m-d', $data['exam_date']);
        }
        $exam_offender->exam_offence_id =  $this->examOffence->where('id', $data['exam_offence_id'])->first();
        $exam_offender->school_code = $data['school_code'];
        $exam_offender->course_header  = $data['course_header'];
        $exam_offender->duration = $data['duration'];
        if(request()->comment){
            $exam_offender->comment = $data['comment'];
        }
        $exam_offender->save($data);
        $header_key = $data['course_header'];
        if(request()->exam_date) {
            $date = Carbon::parse($data['exam_date']);
            $examYearMonth = $date->format('y') . $date->format('m');
            $data['exam_date'] = $examYearMonth;
            $processScoreMarkers = $this->processScoreMarkers($data['candidate_index'], $header_key, $examYearMonth);
            if ($processScoreMarkers != true) {
                return $this->errorResponse('Unable to create score markers', 500);
            }
        }
        DB::commit();
        return $this->successResponse(new ExamOffenderResource($exam_offender), 'Exam offender updated successfully!');
    }

    public function fetchSingleExamOffender($id): \Illuminate\Http\JsonResponse
    {
        $data = $this->examOffender::with('candidateIndexForExamOffender', 'schoolForOffenders', 'courseHeaderForExamOffender','singleOffence')->where( 'id',$id)->first();
        return $this->successResponse($data, "single exam offender information retrieved successfully");
    }

    public function deleteExamOfender(string $examOffenderId)
    {

        if(!$examOffender = $this->examOffender->find($examOffenderId))
        {
            return $this->errorResponse("Exam Offenders Not Found", 400);
        }


        $this->examOffender->where('candidate_index', $examOffender->candidate_index)->delete();

        return $this->successResponse([], "Exam Offenders Deleted SuccessFully");
    }


    public function autoReminderForFormerOffender()
    {
        $reminder = ExamOffender::all();
        foreach ($reminder as $item)
        {
            $start_date =  date('Y', strtotime($item['exam_date']));
            $end_date = (int)$start_date + (int)$item['duration'];
            $user = User::where('user_role', 'admin')->get();
            $formatted_end_date  = Carbon::createFromFormat('Y', '2022')->format('Y');
            if($formatted_end_date == Carbon::now()->format('Y'))
            {
                collect($user)->map(function ($query) use ($item){
                                     $query->notify(new AdminNotification('Reminder',
                                         'Exam offence duration For candidate' . $item['candidate_index'] . 'has expired'
                                         , 'exam_offender'));
                    });
             echo "Exam Offender Notification Sent To Admin    ";
            }
            echo " No Notification Sent";
        }
    }


}
