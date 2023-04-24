<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'category'
    ];

    public function trainingSchoolCategory()
    {
        return $this->belongsTo(TrainingSchool::class, 'school_category_id');
    }
}
