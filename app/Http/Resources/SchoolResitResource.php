<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResitResource extends JsonResource
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
            // "candidate_index" => $this->candidate_index,
            // "school_code" => $this->school_code,
            "title" => $this->title,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "last_name" =>  $this->last_name,
            "school_resits" =>  $this->school_resits,
        ];
    }
}
