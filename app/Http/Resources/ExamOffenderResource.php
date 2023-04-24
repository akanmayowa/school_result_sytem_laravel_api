<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamOffenderResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'candidate_index' => $this->candidate_index,
            'course_header' => $this->course_header,
            'registration_date' => $this->registration_date,
            'exam_date' => $this->exam_date,
            'exam_offence_id' => $this->exam_offence_id,
            'duration' => $this->duration,
            'comment' => $this->comment,
            'school_code' => $this->school_code,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
