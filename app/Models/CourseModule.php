<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_key', 'description', 'credits', 'serial_number', 'delete_status', 'practical', 'header_key',
    ];


    public function courseHeader(){
        return $this->belongsTo(CourseModule::class,'header_key','header_key');
    }
}
