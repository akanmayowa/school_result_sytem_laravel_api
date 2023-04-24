<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidateIndexingResource extends JsonResource
{

    public function toArray($request)
    {
        return [
              'id' => $this->id,
               'candidate_index' => $this->candidate_index,
               'first_name' => $this->first_name,
               'last_name' => $this->last_name,
               'middle_name' => $this->middle_name,
               'school_code' => $this->school_code,
               'exam_id' => $this->exam_id,
               'course_header' => $this->course_header,
               'unverified' => $this->unverified,
               'candidate_category' => $this->whenLoaded('candidateCategory')
        ];
    }
}
