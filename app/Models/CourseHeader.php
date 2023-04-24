<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CourseHeader extends Model
{
    use HasFactory;
    protected $fillable = [
        'header_key', 'description', 'cadre', 'delete_status', 'total_units', 'number of modules',
        'exam_date','add_year','month','index_code','modules'
    ];

    public function courseModule(){
        return $this->hasMany(CourseModule::class,'header_key','header_key');
    }

    public function candidateIndexing(){
        return $this->belongsTo(CandidateIndexing::class,'header_key','course_header',);
    }
}
