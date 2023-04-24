<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'school_code' => $this->school_code,
            'candidate_index' => $this->candidate_index,
            'course_header' => $this->course_header,
            'exam_id' => $this->exam_id,
            'reg_status' =>  $this->reg_status
        ];
    }
}
