<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseModuleResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'course_key' => $this->course_key,
            'description' => $this->description,
            'credits' => $this->credits,
            'serial_number' => $this->serial_number,
            'delete_status' => $this->delete_status,
            'practical' => $this->practical,
            'header_key' => $this->header_key,
        ];

    }
}
