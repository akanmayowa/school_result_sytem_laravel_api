<?php

namespace App\Http\Resources;

use App\Models\CourseHeader;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseHeaderResource extends JsonResource
{

    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'header_key' => $this->header_key,
                'description' => $this->description,
                'cadre' => $this->cadre,
                'delete_status' => $this->delete_status,
                'total_units' => $this->total_units,
                'modules' => $this->modules,
                'exam_date' => $this->exam_date,
                'add_year' => $this->add_year,
                'month' => $this->month,
                'index_code' => $this->index_code,
        ];
    }
}
