<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code'  => $this->code,
//            'created_at' => $this->created_at->format('d/m/Y'),
//            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
