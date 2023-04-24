<?php

namespace App\Services;

use App\Helpers\PrintDocument;
use App\Models\Candidate;
use App\Models\CourseHeader;
use App\Models\CourseModule;
use App\Models\ScoreMarkerOne;
use App\Models\ScoreMarkerTwo;
use App\Models\State;
use App\Models\TrainingSchool;
use App\Traits\ResponsesTrait;
use App\Models\CandidateIndexing;
use App\Http\Resources\CandidatePrintResource;
use App\Http\Resources\CandidateIndexingPrintResource;
use App\Models\SchoolResit;

class PrintDocumentService
{

    use ResponsesTrait;

    public function __construct()
    {
        $this->candidateIndexing = new CandidateIndexing();
        $this->candidate = new Candidate();
        $this->courseHeader = new CourseHeader();
        $this->trainingSchool = new TrainingSchool();
        $this->schoolResit = new SchoolResit();
    }

    public function candidateIndexing(array $data)
    {
        if (request()->input('school_code') && request()->input('course_header') && request()->input('exam_year')) {
            $year_spliited = str_split(request()->input('exam_year'));
            $exam_year = $year_spliited[2] . $year_spliited[3];
            $candidateIndexings = (new CandidateIndexing())::select('id', 'candidate_index', 'first_name', 'last_name', 'middle_name', 'school_code', 'course_header', 'exam_id', 'reason')
                ->with('courseHeader')
                ->where('course_header', $data['course_header'])
                ->where('school_code', $data['school_code'])
                ->where('exam_id', 'like', '%' . $exam_year);

            if ($data['print_type'] === 'unverified_candidates') {
                $candidateIndexings = $candidateIndexings->where('unverified', '=', 1);
            }
            if ($data['print_type'] === 'verified_candidates') {
                $candidateIndexings = $candidateIndexings->whereUnverified(0)->where('visible', '=', 1);
            }

            $school = (new TrainingSchool())->where('school_code', request()->input('school_code'))->with('state')->get();

            $data = [
                'candidate' => $candidateIndexings->groupBy('candidate_index', 'exam_id')->get(),
                'school_information' => $school,
                'exam_year' => request()->input('exam_year'),
            ];
            return $this->successResponse($data);
        }

        return $this->successResponse([], "Search Filter Data Not Found");
    }

    public function candidateIndexing_II()
    {
        if (request()->input('course_header') && request()->input('exam_year')) {
            $year_spliited = str_split(request()->input('exam_year'));
            $exam_year = $year_spliited[2] . $year_spliited[3];
            $candidateIndexings = (new CandidateIndexing())::
            select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id',  'first_name', 'last_name', 'middle_name')
                ->with('courseHeader')
                ->where('course_header', request()->input('course_header'))
                ->where('school_code', auth()->user()->operator_id)
                ->where('exam_id', 'like', '%' . $exam_year);

            if (request()->input('print_type') === 'unverified_candidates') {
                $candidateIndexings = $candidateIndexings->where('unverified', '=', 1);
            }
            if (request()->input('print_type') === 'verified_candidates') {
                $candidateIndexings = $candidateIndexings->where('visible', '=', 1);
            }
            $school = (new TrainingSchool())->where('school_code', auth()->user()->operator_id)->with('state')->get();
            $data = [
                'candidate' => $candidateIndexings->groupBy('candidate_index')->orderBy('candidate_index', 'asc')->get(),
                'school_information' => $school,
            ];
            return $this->successResponse($data);
        }

        return $this->successResponse([], "Search Filter Data Not Found");
    }

    public function oralMarkSheet()
    {
        $course_header = CourseHeader::where('header_key', request()->input('course_header'))->get();
        if (request()->input('school_code') && request()->input('course_header') && request()->input('exam_year')) {
            $year_splitting = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $school = (new TrainingSchool())->where('school_code', request()->input('school_code'))->with('state')->get();
            $candidates = $this->candidate::
            select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id',  'first_name', 'last_name', 'middle_name')
                ->with('candidateIndexing')
                ->where('course_header', request()->input('course_header'))
                ->where('school_code', request()->input('school_code'))
                ->where('exam_id', 'like', '%' . $year_splitting)
                ->where(function ($query) {
                    $query->where('registration_type', '=', 'fresh')
                        ->orWhere('registration_type', '=', 'resitall');
                });


            return $this->successResponse([$candidates->groupBy('candidate_index', 'exam_id')->orderBy('candidate_index', 'asc')->get(),
                $school, 'course header' => $course_header,
            ], "Candidate Oral Mark Sheet Data");
        }
        return $this->successResponse([], "Search Filter Data Not Found");
    }

    public function researchProject()
    {
        if (request()->input('school_code') && request()->input('exam_year')) {
            $year_splitting = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $check_if_school_exist = (new TrainingSchool())->where('school_code', request()->input('school_code'))->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $candidates = $this->candidate->select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id',  'first_name', 'last_name', 'middle_name')
                ->where('school_code', request()->input('school_code'))
                ->where('exam_id', 'like', '%' . $year_splitting)
                ->where('course_header', 'B7')
                ->with('candidateIndexing')
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
                ->orderBy('candidate_index', 'asc')
                ->groupBy('candidate_index', 'exam_id')
                ->get();


            $data = [
                'candidate' => $candidates,
                'school_information' => $check_if_school_exist,
                'exam_date' => "06/" . $year_splitting
            ];

            return $this->successResponse($data, 'Data Record Found');
        }
        return $this->successResponse([], "Search Filter Data Not Found");
    }

    public function researchProjecAA2()
    {
        if (request()->input('school_code') && request()->input('exam_year')) {
            $year_splitting = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $check_if_school_exist = (new TrainingSchool())->where('school_code', request()->input('school_code'))->get();
            if (empty($check_if_school_exist)) {
                return $this->errorResponse('Invalid School Selected');
            }

            $candidates = $this->candidate::
            select('id', 'candidate_index', 'school_code', 'course_header', 'exam_id')
                ->with('candidateIndexing')
                ->where('course_header', 'AA2')
                ->where('school_code', request()->input('school_code'))
                ->where('exam_id', 'like', '%' . $year_splitting)
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
                ->orderBy('candidate_index', 'ASC')
                ->groupBy('candidate_index', 'exam_id')
                ->get();

            $data = [
                'candidate' => CandidatePrintResource::collection($candidates),
                'school_information' => $check_if_school_exist,
                'exam_date' => "10/" . $year_splitting
            ];

            return $this->successResponse($data, "Search Record Collection Successfully Retrieved");
        }
        return $this->errorResponse(" ", "Search Filter Record Not Found");
    }

    public function zonalMarksheet()
    {
        $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
        $course_header_info = $this->courseHeader->where('header_key', request()->input('course_header'))->first();
        $course_key = request()->input('course_key');
        $school_code = request()->input('school_code');
        $course_header = request()->input('course_header');
        $course_details = (new CourseModule())->where('course_key', request()->input('course_key'))->first();


        if (empty($course_header_info)) {
            return $this->errorResponse('Invalid Course Header Selected');
        }

        $school_info = (new TrainingSchool())->where('school_code', request()->input('school_code'))->with('state')->get();
        if (empty($school_info)) {
            return $this->errorResponse('Invalid School Selected');
        }

        $candidate_scores = CandidateIndexing::leftJoin(
            'score_marker_ones as wsco',
            function ($builder) use ($course_key) {
                $builder
                    ->on('wsco.candidate_index', 'candidate_indexings.candidate_index')
                    ->where('wsco.course_key', $course_key);
            }
        )
            ->leftJoin('score_marker_twos as wsct', function ($builder) use (
                $exam_year,
                $course_key
            ) {
                $builder
                    ->on('wsct.candidate_index', 'candidate_indexings.candidate_index')
                    ->where('wsct.course_key', $course_key)
                    ->where('wsct.exam_id', 'like', '%' . $exam_year);
            })
            ->where('candidate_indexings.school_code', $school_code)
            ->where('wsco.course_header', $course_header)
            ->where('wsco.exam_id', 'like', '%' . $exam_year)
            ->orderBy('candidate_indexings.candidate_index')
            ->groupBy('candidate_indexings.candidate_index')
            ->select(
                'candidate_indexings.school_code',
                'candidate_indexings.candidate_index',
                'wsco.q1',
                'wsco.q2',
                'wsco.q3',
                'wsco.q4',
                'wsco.q5',
                'wsct.q1 as q_1',
                'wsct.q2 as q_2',
                'wsct.q3 as q_3',
                'wsct.q4 as q_4',
                'wsct.q5 as q_5',
                'wsco.operator_id as op_id',
                'wsct.operator_id as op_id2',
                'candidate_indexings.exam_date',
                'wsco.status'
            )
            ->get();

//        $candidate_scores = $this->candidateIndexing
//            ->select('first_name', 'last_name', 'middle_name', 'candidate_index', 'id', 'school_code', 'course_header')
//            ->where('course_header', request()->input('course_header'))
//            ->where('school_code', request()->input('school_code'))
//            ->with(['scoreMarkerOneForCandidate' => function ($q) {
//                $q->where('course_key', 'like', '%' . request()->input('course_key'));
//            }])
//            ->with(['scoreMarkerTwoForCandidate' => function ($q) use ($exam_year) {
//                $q->where('course_key', request()->input('course_key'))
//                    ->where('exam_id', 'like', '%' . $exam_year);
//            }])
//            ->orderBy('candidate_index', 'asc')
//            ->groupBy('candidate_index', 'exam_id')
//            ->get();

        $newData = [
            'candidate_scores' => $candidate_scores,
            'course_header_info' => $course_header_info,
            'school_info' => $school_info,
            'exam_year' => request()->input('exam_year'),
            'year' => $exam_year,
            'course_module_info' =>  $course_details,
            'operator' => auth()->user()->id
        ];
        return $this->successResponse([$newData]);
    }

    public function courseRegistrationStatistics()
    {
        if (request()->input('course_header') && request()->input('exam_year')) {
            $course_header = request()->input('course_header');
            $course_header_check = (new CourseHeader())->where('header_key', request()->input('course_header'))->first();

            if ($course_header_check) {
                $this->errorResponse("Invalid Course Header Information");
            }

            $exam_year = str_split(request()->input('exam_year'))[2] . str_split(request()->input('exam_year'))[3];
            $registered_candidatess = Candidate::with([
                'trainingSchoolCandidate:school_code,school_name,state_code,state_id',
                'trainingSchoolCandidate.state',
            ])
                ->join(
                    'candidate_indexings as wic',
                    'wic.candidate_index',
                    'candidates.candidate_index'
                )
                ->where('candidates.course_header', $course_header)
                ->where('candidates.exam_id', 'like', '%' . $exam_year)
                ->select('candidates.*', 'wic.validate', 'wic.visible')
                ->groupBy('candidates.candidate_index')
                ->get();

            $sorted = $registered_candidatess->sortBy('trainingSchoolCandidate.state.name');

            $registered_candidates_sorted = collect($sorted->values()->all());

            $filtered = $registered_candidates_sorted->filter(function ($x) {
                return ($x->registration_type == 'fresh' && $x->visible == 1) ||
                    $x->registration_type == 'resit' ||
                    $x->registration_type == 'resitall';
            });

            $registered_candidatesss = $filtered->all();
            $registered_candidates = collect($registered_candidatesss)->groupBy(
                'school_code'
            );

            $results = [];

            foreach ($registered_candidates as $key => $candidates) {
                $resits = SchoolResit::where('school_code', $key)
                    ->where('resit_header', $course_header)
                    ->get(['candidate_index'])
                    ->map(function ($x) use ($key, $exam_year, $course_header) {
                        $courses = SchoolResit::whereCandidateIndex($x->candidate_index)
                            ->where('school_code', $key)
                            ->where('resit_header', $course_header)
                            ->where('exam_date', 'like', '%' . $exam_year)
                            ->get(['subject_code']);

                        if (count($courses) > 0) {
                            if ($courses[0]->subject_code == null) {
                                $failed_courses = [1, 2, 3, 4];
                            } else {
                                $failed_courses = $courses;
                            }
                            return count($failed_courses);
                        }
                    });

                $filtered = $resits->filter(function ($resit) {
                    return !!$resit;
                });

                $resits = collect(array_values($filtered->all()));

                $resit_all = $resits->filter(function ($resit) {
                    return $resit > 3;
                });

                $resit = $resits->filter(function ($resit) {
                    return $resit < 4;
                });

                $school_details = [
                    'fresh' => 0,
                    'resit' => count($resit),
                    'resit_all' => count($resit_all),
                    'training_school' => $candidates[0]->trainingSchoolCandidate ?? " ",
                    'state_name' => $candidates[0]->trainingSchoolCandidate->state->name ?? " ",
                    'total' => count($resit) + count($resit_all),
                    'date' => request()->input('exam_year'),
                ];

                foreach ($candidates as $candidate) {
                    if (
                        $candidate->registration_type == 'fresh' &&
                        $candidate->validate == 'yes' &&
                        $candidate->visible == 1
                    ) {
                        $school_details['fresh'] += 1;
                        $school_details['total'] += 1;
                    }
                }
                $results[] = $school_details;
            }

            $newData = [
                'print_date' => date('d-M-y h:i A'),
                'course_header_info' => $this->courseHeader->where('header_key', request()->input('course_header'))->first(),
                'exam_year' => request()->input('exam_year'),
                'results' => $results
            ];

            return $this->successResponse($newData);
//            $registered_candidate = Candidate::with(['trainingSchoolCandidate:id,school_code,school_name,state_id', 'trainingSchoolCandidate.state'])
//                ->where('course_header', $course_header)
//                ->where('exam_id', 'like', '%' . $exam_year)
//                ->where(function ($query) {
//                    $query->where('registration_type', '=', 'fresh')
//                        ->orWhere('registration_type', '=', 'resitall')
//                        ->groupBy('school_code');
//                })
//                ->orderBy('candidate_index', 'asc')
//                ->groupBy('candidate_index')
//                ->get();
//
//            $results = [];
//
//            $registered_candidates = collect($registered_candidate);
//            foreach ($registered_candidates->chunk(1) as $key => $candidates) {
//                $resits = $this->schoolResit->where('school_code', $key)
//                    ->where('resit_header', $course_header)
//                    ->get(['candidate_index'])
//                    ->map(function ($x) use ($key, $exam_year, $course_header) {
//                        $courses = $this->schoolResit->whereCandidateIndex($x->candidate_index)
//                            ->where('school_code', $key)
//                            ->where('resit_header', $course_header)
//                            ->where('exam_date', 'like', '%' . $exam_year)
//                            ->get(['subject_code']);
//
//                        if (count($courses) > 0) {
//                            if ($courses[0]->subject_code == null) {
//                                $failed_courses = [1, 2, 3, 4];
//                            } else {
//                                $failed_courses = $courses;
//                            }
//                            return count($failed_courses);
//                        }
//                    });
//
//
//                $filtered = $resits->filter(function ($resit) {
//                    return !!$resit;
//                });
//
//                $resits = collect(array_values($filtered->all()));
//
//                $resit_all = $resits->filter(function ($resit) {
//                    return $resit > 3;
//                });
//
//                $resit = $resits->filter(function ($resit) {
//                    return $resit < 4;
//                });
//
//
//                $school_details = [
//                    'fresh' => 0,
//                    'resit' => count($resit),
//                    'resit_all' => count($resit_all),
//                    'training_school' => $candidates[0]->trainingSchoolCandidate ?? " ",
//                    'state_name' => $candidates[0]->trainingSchoolCandidate->state->name ?? " ",
//                    'total' => count($resit) + count($resit_all),
//                    'date' => request()->input('exam_year')
//                ];
//
//                $results[] = $school_details;
//            }
//
//            $newData = [
//                'print_date' => date('d-M-y h:i A'),
//                'course_header_info' => $this->courseHeader->where('header_key', request()->input('course_header'))->first(),
//                'exam_year' => request()->input('exam_year'),
//                'results' => $results
//            ];
        }
        return $this->errorResponse("Search Filter Results Not Found");
    }




}
