<?php

namespace App\Http\Resources;

use App\Models\SchoolCategory;
use App\Models\State;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingSchoolResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'school_code' => $this->school_code,
            'index_code' => $this->index_code,
            'state_id' => $this->StateAndTrainingSchoolRelationship(),
            'school_name' => $this->school_name,
            'school_category_id' => $this->schoolCategoryAndTrainingSchoolRelationship(),
            'contact' => $this->contact,
            'position' => $this->position,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'password' => $this->password,
            'fax' => $this->fax,
            'can_register' => $this->can_register
        ];
    }

    public function StateAndTrainingSchoolRelationship()
    {
        $state_code = new State();
        return $state_code->loadMissing(['trainingSchool'])->where('id',$this->state_id)->get('code');
    }

    public function SchoolCategoryAndTrainingSchoolRelationship()
    {
        $school_category = new SchoolCategory();
        return $school_category->loadMissing(['trainingSchoolCategory'])->where('id',$this->school_category_id)->get(['category','description']);
    }

}
