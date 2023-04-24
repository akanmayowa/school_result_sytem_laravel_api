<?php /** @noinspection ALL */

namespace App\Services;

use App\Helpers\GeneralLogs;
use Carbon\Carbon;
use App\Models\SchoolResit;
use App\Models\ScoreResult;
use Illuminate\Support\Str;
use App\Models\CourseModule;
use App\Traits\ResponsesTrait;
use App\Models\CandidateIndexing;
use Illuminate\Support\Facades\DB;


class SchoolResitServices
{

    use ResponsesTrait;

    public function __construct()
    {
        $this->candidateIndexing = new CandidateIndexing;
        $this->courseModule = new CourseModule;
        $this->schoolResit = new SchoolResit;
        $this->scoreResult = new ScoreResult;
    }

    public function fetchAllSchoolResitV2()
    {
        if(request()->resit_header AND request()->exam_year  AND  !empty(request()->exam_year) AND !empty(request()->resit_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $data = [
    'schoolResit' => CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
        ->with('schoolResists', function ($query) use ($exam_year){
            $query->where('exam_date','LIKE', '%' . $exam_year)
                ->where('resit_header', request()->resit_header);
        })
        ->whereNotNull('exam_id')
        ->where('school_code', auth()->user()->operator_id)
        ->groupBy(['candidate_index','exam_id'])
        ->has('schoolResists')
        ->orderByDesc('id')
        ->get(),
      ];
            return $this->successResponse($data, "Retrieving School Resit information");
        }
        //working
        if(request()->exam_year  AND !empty(request()->exam_year))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $data = [
                'schoolResit' =>  CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                    ->with('schoolResists', function ($query) use ($exam_year){
                        $query->where('exam_date','LIKE', '%' . $exam_year . '%');
                    })
                    ->whereNotNull('exam_id')
                    ->where('school_code', auth()->user()->operator_id)
                    ->groupBy(['candidate_index','exam_id'])
                    ->has('schoolResists')
                    ->orderByDesc('id')
                    ->get(),

            ];
            return $this->successResponse($data, "Retrieving  School Resit information");
        }
        /// working fine
        if(request()->exam_year AND request()->resit_header AND !empty(request()->exam_year) AND !empty(request()->resit_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $data = [
                'schoolResit' => CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                    ->with('schoolResists', function ($query) use ($exam_year){
                        $query->where('exam_date','LIKE', '%' . $exam_year . '%')->where('resit_header', request()->resit_header);
                    })
                    ->whereNotNull('exam_id')
                    ->where('school_code', auth()->user()->operator_id)
                    ->groupBy(['candidate_index','exam_id'])
                    ->has('schoolResists')
                    ->orderByDesc('id')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        ///working
        if(request()->resit_header)
        {
            $data = [
                'schoolResit' =>  CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                    ->with('schoolResists', function ($query) {
                        $query->where('school_code', auth()->user()->operator_id)
                            ->where('resit_header',  request()->resit_header);
                    })
                    ->whereNotNull('exam_id')
                    ->where('school_code', auth()->user()->operator_id)
                    ->groupBy(['candidate_index','exam_id'])
                    ->has('schoolResists')
                    ->orderByDesc('id')
                    ->get(),

            ];
            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        //working
        if(request()->has('candidate_index'))
        {
            $data = [
                'schoolResit' => CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                    ->with('schoolResists')
                    ->whereNotNull('exam_id')
                    ->where('school_code', auth()->user()->operator_id)
                    ->where('candidate_index', 'LIKE', '%' . request()->input('candidate_index') . '%')
                    ->groupBy(['candidate_index','exam_id'])
                    ->has('schoolResists')
                    ->orderByDesc('id')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving School Resit Information");
        }

        $data = [
            'schoolResit' => CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                ->with('schoolResists')
                ->whereNotNull('exam_id')
                ->where('school_code', auth()->user()->operator_id)
                ->groupBy(['candidate_index','exam_id'])
                ->has('schoolResists')
                ->orderByDesc('id')
                ->get(),
        ];

        return $this->successResponse($data,"Retrieving Exam Offender information");
    }

    public function fetchAllSchoolResit()
    {

        if(request()->school_code AND request()->resit_header AND request()->exam_year AND !empty(request()->school_code) AND  !empty(request()->exam_year) AND !empty(request()->resit_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $data = [
                'schoolResit' => CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code', 'exam_id','visible','course_header','registered_at')
                    ->whereNotNull('exam_id')
                    ->with('schoolResists', function ($query)use ($exam_year){
                            $query->where('school_code', 'LIKE', '%' . request()->input('school_code') . '%')
                                ->whereNotNull('subject_code');
                     })
                    ->whereHas('schoolResists', function ($query){
                        return $query->whereNotNull('subject_code');
                    })
                    ->where('course_header', request()->resit_header)
                    ->where('school_code', 'LIKE', '%' . request()->input('school_code') . '%')
                    ->where('exam_id','LIKE', '%' . $exam_year . '%')
                    ->groupBy('candidate_index','exam_id')
                    ->orderBy('candidate_index', 'asc')
                    ->has('schoolResists')
                    ->get(),
            ];

            return $this->successResponse($data, "Retrieving School Resit information");
        }
        //working
        if(request()->exam_year AND request()->school_code AND !empty(request()->exam_year) AND !empty(request()->school_code))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $school_code = request()->input('school_code');
            $resit_header = request()->input('course_header');

            $school_resits = SchoolResit::whereSchoolCode($school_code)
                ->with('candidateIndexing:id,candidate_index,first_name,last_name,visible,registered_at')
                ->whereResitHeader($resit_header)
                ->where('exam_date', 'like', '%' . $exam_year . '%')
                ->get()
                ->map(function ($schoolResit) {
                    $schoolResit->name =
                        optional($schoolResit->candidateIndexing)->first_name .
                        ' ' .
                        optional($schoolResit->candidateIndexing)->last_name;
                    $schoolResit->visible = optional($schoolResit->candidateIndexing)->visible;
                    $schoolResit->registered_at = optional($schoolResit->candidateIndexing)->registered_at;
                    return $schoolResit;
                })
                ->groupBy('candidate_index')
                ->values()
                ->map(function ($resit) {
                    $courses = $resit->map(function ($r) {
                        return $r->subject_code;
                    });

                    return [
                        'candidate_index' => $resit[0]->candidate_index,
                        'school_code' => $resit[0]->school_code,
                        'course_header' => $resit[0]->resit_header,
                        'exam_id' => $resit[0]->exam_date,
                        'visible' => $resit[0]->visible,
                        'registered_at' => $resit[0]->registered_at,
                        'name' => $resit[0]->name,
                        'courses' => $courses,
                    ];
                });

            $data = ['schoolResit' => $school_resits];

            return $this->successResponse($data, "Retrieving  School Resit information");
        }
        /// working fine
        if(request()->exam_year AND request()->resit_header AND !empty(request()->exam_year) AND !empty(request()->resit_header))
        {
            $exam_year = str_split(request()->exam_year)[2] . str_split(request()->exam_year)[3];
            $data = [
                'schoolResit' => CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                    ->whereNotNull('exam_id')
                    ->with('schoolResists', function ($query) use ($exam_year){
                        return $query
                            ->where('resit_header', 'LIKE', '%' . request()->input('resit_header') . '%')
                            ->where('exam_date','LIKE', '%' . $exam_year)
                            ->whereNotNull('subject_code');
                    })
                    ->where('course_header', 'LIKE', '%' . request()->input('resit_header') . '%')
                    ->where('exam_id','LIKE', '%' . $exam_year)
                    ->whereHas('schoolResists', function ($query){
                        return $query->whereNotNull('subject_code');
                    })
                    ->groupBy(['candidate_index','exam_id'])
                    ->has('schoolResists')
                    ->orderBy('candidate_index', 'asc')
                    ->get(),
            ];

            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        ///working
        if(request()->school_code && request()->resit_header)
        {
            $data = [
                'schoolResit' =>  CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                    ->whereNotNull('exam_id')
                    ->with('schoolResists', function ($query){
                        return $query
                            ->where('resit_header', 'LIKE', '%' . request()->input('resit_header') . '%')
                            ->where('school_code', 'LIKE', '%' . request()->school_code . '%')
                            ->whereNotNull('subject_code');
                    })
                    ->whereHas('schoolResists', function ($query){
                        return $query->whereNotNull('subject_code');
                    })
                    ->where('course_header', 'LIKE', '%' . request()->input('resit_header') . '%')
                    ->where('school_code', 'LIKE', '%' . request()->school_code . '%')
                    ->groupBy(['candidate_index','exam_id'])
                    ->has('schoolResists')
                    ->orderBy('candidate_index', 'asc')
                    ->get(),
            ];

            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
        //working
        if(request()->has('resit_header'))
        {
            $data = [
                'schoolResit' =>  CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                    ->whereNotNull('exam_id')
                    ->where('course_header', request()->input('resit_header'))
                    ->with('schoolResists', function ($query) {
                      return  $query->where('resit_header', request()->input('resit_header'))
                            ->whereNotNull('subject_code');
                    })
                    ->whereHas('schoolResists', function ($query){
                        return $query->whereNotNull('subject_code');
                    })
                    ->groupBy(['candidate_index','exam_id'])
                    ->has('schoolResists')
                    ->orderBy('candidate_index', 'asc')
                    ->get(),
            ];

            return $this->successResponse($data, "Retrieving School Resit information");
        }
        // working
        if(request()->has('exam_year'))
        {
            $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $data = [
                'schoolResit' =>  CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code','exam_id','visible','course_header','registered_at')
                    ->whereNotNull('exam_id')
                    ->where('exam_id','LIKE', '%' . $exam_year)
                    ->with('schoolResists', function ($query) use ($exam_year) {
                      return  $query->where('exam_date','LIKE', '%' . $exam_year)
                            ->whereNotNull('subject_code');
                    })
                    ->whereHas('schoolResists', function ($query){
                        return $query->whereNotNull('subject_code');
                    })

                    ->groupBy('candidate_index','exam_id', 'course_header')
                    ->has('schoolResists')
                    ->orderBy('candidate_index', 'asc')
                    ->get(),
            ];
            return $this->successResponse($data, "Retrieving School Resit information");
        }
        //working
        if(request()->has('candidate_index'))
        {
            $candidate_index = request()->input('candidate_index');

            $school_resits = SchoolResit::whereCandidateIndex($candidate_index)
                ->with('candidateIndexing:id,candidate_index,first_name,last_name,visible,registered_at')
                ->whereNotNull('resit_header')
                ->whereNotNull('exam_date')
                ->latest()
                ->get()
                ->map(function ($schoolResit) {
                    $schoolResit->name =
                        optional($schoolResit->candidateIndexing)->first_name .
                        ' ' .
                        optional($schoolResit->candidateIndexing)->last_name;
                    $schoolResit->visible = optional($schoolResit->candidateIndexing)->visible;
                    $schoolResit->registered_at = optional($schoolResit->candidateIndexing)->registered_at;
                    return $schoolResit;
                })
                ->groupBy('candidate_index')
                ->values()
                ->map(function ($resit) {
                    $courses = $resit->map(function ($r) {
                        return $r->subject_code;
                    });

                    return [
                        'candidate_index' => $resit[0]->candidate_index,
                        'school_code' => $resit[0]->school_code,
                        'course_header' => $resit[0]->resit_header,
                        'exam_id' => $resit[0]->exam_date,
                        'visible' => $resit[0]->visible,
                        'registered_at' => $resit[0]->registered_at,
                        'name' => $resit[0]->name,
                        'courses' => $courses,
                    ];
                });

            $data = ['schoolResit' => $school_resits];

            return $this->successResponse($data, "Retrieving School Resit Information");
        }
        //working
        if(request()->has('school_code'))
        {
            $data = [
                'schoolResit' => CandidateIndexing::select('id','candidate_index','first_name','last_name','school_code', 'exam_id', 'visible','course_header','registered_at')
                    ->whereNotNull('exam_id')
                    ->with('schoolResists', function ($query){
                       return $query->whereNotNull('subject_code');
                    })
                    ->whereHas('schoolResists', function ($query){
                        return $query->whereNotNull('subject_code');
                    })
                    ->where('school_code', request()->input('school_code'))
                    ->groupBy('candidate_index','exam_id', 'course_header')
                    ->orderBy('candidate_index', 'asc')
                    ->get(),
            ];

            return $this->successResponse($data, "Retrieving Exam Offender information");
        }
    }

    public function createSchoolResit(array $data)
    {
        DB::beginTransaction();
        $data['registration_type'] = $this->getRegType($data);
        if ($exist = $this->schoolResit->where('candidate_index', $data['candidate_index'])
            ->where('exam_date', $data['exam_id'])
            ->first()) {
            $exist->delete();
        }
        $candidate = CandidateIndexing::select('school_code')->where('candidate_index', $data['candidate_index'])->first();
        $data['school_code'] = $candidate->school_code;

        $results = [];
        foreach ($data['course_keys'] as $result) {
            $data['batch'] = Str::random(4);
            $items = explode(',', $result);
            foreach ($items as $item)
                $results[] =  SchoolResit::create([
                    'resit_reg_status' => 1,
                    'candidate_index' => $data['candidate_index'],
                    'school_code' => $data['school_code'],
                    'exam_date' => $data['exam_id'],
                    'subject_code' => $item,
                    'batch' => $data['batch'],
                    'resit_header' => $data['course_header']
                ]);
        }

        DB::commit();
        return $this->successResponse($results, 'Record created');
    }

    public function createScoreResult($data)
    {
        foreach ($data['course_keys'] as $course) {
            $courseKey = $data['course_header'] . '.' . $course;
            $courseModule = $this->courseModule::where('course_key', $courseKey)->first();

            ScoreResult::create([
                'school_code' => $data['school_code'],
                'candidate_index' => $data['candidate_index'],
                'course_average' => 0,
                'course_header' => $data['course_header'],
                'course_unit' => $courseModule->creditsc ?? 0,
                'year' => $data['exam_id'],
                'course_key' => $courseKey
            ]);
        }
        return true;
    }

    public function getRegType($data)
    {
        $reg_type = count($data['course_keys']) > 3 ? 'resitall' : 'resit';

        if ($data['course_header'] == 'A4' || $data['course_header'] == 'A5') {
            $reg_type = count($data['course_keys']) > 3 ? 'resitall' : 'resit';

            if (in_array('FHD003', $data['course_keys']) || in_array('FHC003', $data['course_keys'])) {
                $reg_type = 'resitall';
            }
        }

        return $reg_type;
    }

    public function registerCandidate($data)
    {
        $this->candidate->create([
            'candidate_index' => $data['candidate_index'],
            'school_code' => $data['school_code'],
            'course_header' => $data['course_header'],
            'registration_type' => $data['registration_type'],
            'reg_status' => 'approved',
            'exam_id' => $data['exam_id'],
            'exam_date' => date("Y-m-d H:i:s", strtotime($data['exam_id'])),
        ]);
        return true;
    }

    public function storeSchoolResit($data)
    {
        SchoolResit::create([
            'resit_reg_status' => 1,
            'candidate_index' => $data['candidate_index'],
            'school_code' => $data['school_code'],
            'exam_date' => $data['exam_id'],
            'subject_code' => $data['subject_code'],
            'batch' => $data['batch'],
            'resit_header' => $data['course_header']
        ]);
    }

    public function numberOfSchoolResitCounter()
    {
        $counter = (new SchoolResit())->count();
        return $this->successResponse($counter,"Counting the Number of Student That Resit");
    }

    public function deleteResit()
    {

        if(!SchoolResit::where('candidate_index', request()->input('candidate_index'))->first())
        {
            return $this->errorResponse("Resit Info Doesnt Exist", 401);
        }


        $data = SchoolResit::where('candidate_index', request()->input('candidate_index'))->get();

        foreach (collect($data) as $item)
        {
            $item->delete();
        }

        return $this->successResponse([], "School Resit Deleted Successfully");



    }

    public function getAllSchoolResit()
    {
        if (request()->has('search') && !empty(request()->input('search'))) {
            $school_resit = (new SchoolResit())::with('candidateIndexForResit')
                                            ->orWhere('candidate_index', 'LIKE', '%' . request()->input('search') . '%')
                                            ->orWhere('subject_code', 'LIKE', '%' . request()->input('search') . '%')
                                            ->orWhere('school_code', 'LIKE', '%' . request()->input('search') . '%')
                                            ->paginate(20);
            return $this->successResponse($school_resit, "All School Resit Retrieved Successfully");
        }else{
            $school_resit = (new SchoolResit())::with('candidateIndexForResit')->paginate(20);
            return $this->successResponse($school_resit, "All School Resit Retrieved Successfully");
        }
    }

    public function fetchSingleSchoolResit()
    {
        $exam_year = request()->input('exam_year');
        $candidate = CandidateIndexing::select('first_name','last_name','school_code','candidate_index','exam_id','course_header','verify_status','verify_status_2','visible')
                                            ->where('candidate_index', request()->input('candidate_index'))
                                            ->first();

        $school_resit = SchoolResit::with('courseModules')
                                    ->where('candidate_index', request()->input('candidate_index'))
                                    ->where('resit_header', request()->input('course_header'))
                                    ->where('exam_date', 'like', '%' . $exam_year)
                                    ->get();
        $exam_id = [];
        $reg_staus = [];
        foreach($school_resit as $list)
        {
            $reg_staus = $list->resit_reg_status;
           $exam_id = $list->exam_date;
        }

        $data = [
            'candidate' => $candidate,
            'exam_id' => $exam_id,
            'registration_status' => $reg_staus,
            'school_resit' => $school_resit,
        ];

      if(empty($data))
        {
            return $this->errorResponse( 'Candidate and School Resit Information Not Available');
        }


        return $this->successResponse($data, "Details Retrieved Successfully");
    }

}
