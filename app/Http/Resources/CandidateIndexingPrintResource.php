<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidateIndexingPrintResource extends JsonResource
{


    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'candidate_index' =>  $this->candidate_index,
            'reason' => $this->reason,
            'created_at' => $this->created_at,
            'course_header' => new CourseHeaderResource($this->courseHeader),
        ];
    }
}
