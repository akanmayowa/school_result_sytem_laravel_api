<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidatePrintResource extends JsonResource
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
            'candidate_index' =>  $this->candidate_index,
            'created_at' => $this->created_at,
            'candidate_indexing' => $this->whenLoaded('candidateIndexing'),
//            'candidate_indexing' => [
//                'first_name' => $this->candidateIndexing->first_name,
//                'last_name' => $this->candidateIndexing->last_name,
//                'middle_name' => $this->candidateIndexing->middle_name,
//                'candidate_index' => $this->candidateIndexing->candidate_index,
//            ],
        ];
    }
}
