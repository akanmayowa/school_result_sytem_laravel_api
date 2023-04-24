<?php

namespace App\Repositories;

use App\Helpers\Activities;
use App\Helpers\GeneralLogs;
use App\Models\CandidateIncourse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CandidateInCourseRepository extends BaseRepository
{

        public function __construct (CandidateIncourse $model)
        {
            parent::__construct($model);
        }

        public function all()
        {
            return parent::all();
        }

        public function create(array $data)
        {
            return parent::create($data);
        }

        public function CheckingIfCandidateInCourseExist($candidate_index)
        {
            return $this->model->where('candidate_index', $candidate_index)->where('course_header', request()->course_header)->where('exam_id', request()->exam_date)->exists();
        }


    public function updateCandidateInCourse($candidate_index,$first_semester_score,$second_semester_score,$third_semester_score,$total_score,$average_score,){
           return $this->model->where('candidate_index', $candidate_index)->where('course_header', request()->course_header)->where('exam_id', request()->exam_date)->update([
                'course_header' => request()->course_header,
                'candidate_index' => $candidate_index,
                'first_semester_score' => $first_semester_score,
                'second_semester_score' => $second_semester_score,
                'third_semester_score' => $third_semester_score,
                'total_score' => $total_score,
                'average_score' => $average_score,
                'exam_id' => request()->exam_date,
                'school_code' => request()->school_code,
                'new' => 1,
               'operator_id' => auth()->user()->id ,
            ]);
        }

        public function createCandidateInCourse($candidate_index,$first_semester_score,$second_semester_score,$third_semester_score,$total_score,$average_score)
        {
            return $this->model->create([
                'course_header' => request()->course_header,
                'candidate_index' => trim($candidate_index),
                'first_semester_score' => $first_semester_score,
                'second_semester_score' => $second_semester_score,
                'third_semester_score' => $third_semester_score,
                'total_score' => $total_score,
                'average_score' => $average_score,
                'exam_id' => request()->exam_date,
                'school_code' => request()->school_code,
                'new' => 1,
                'operator_id' => auth()->user()->id ?? 123,
            ]);
        }



        public function updateCandidateInCourseV2($id)
        {
            $marker_three = $this->model->find($id);
            $marker_three->total_score = request()->first_sem_score + request()->second_sem_score + request()->third_sem_score;
            $marker_three->first_sem_score = request()->first_sem_score;
            $marker_three->second_sem_score = request()->second_sem_score;
            $marker_three->third_sem_score = request()->third_sem_score;
            $marker_three->average_score = $marker_three->total_score / 3;
            $marker_three->operator_id =  Auth::id() ?? 123;
            $marker_three->save();
            $score_array = ['total_score' => request()->q1,
                'first_sem_score' => request()->first_sem_score,
                'second_Sem_Score' => request()->second_sem_score,
                'third_sem_score' => request()->third_sem_score,
                'average_score' => $marker_three->total_score / 3,
                'operator_id' => Auth::id() ?? 123,
            ];
            Activities::scoreLog($score_array, trim($marker_three->candidate_index), Auth::id(), $marker_three->course_key, $marker_three->course_header, 'Score marker 2', $marker_three->exam_id);
            GeneralLogs::createLog('updated candidate\'s in course score', $marker_three->candidate_index);
            return $this->successResponse("candidate Score Updated Successfully");
        }





}
