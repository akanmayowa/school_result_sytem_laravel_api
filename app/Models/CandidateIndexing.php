<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateIndexing extends Model
{
    use HasFactory;
    protected $fillable = [
        'candidate_index','school_code','first_name','title',
        'middle_name','last_name', 'date_of_birth', 'candidate_category', 'years_of_experience',
        'course_header', 'marital_status','english', 'biology', 'health_science', 'chemistry',
        'mathematics','geography','economics','food_and_nutrition','accounting', 'commerce','physics',
        'technical_drawing','integrated_science','general_science','agric','seatings','reg_nurse',
        'reg_midwife','month_yr','month_yr_reg','verify_birth_certificate','verify_o_level',
        'verify_marriage_certificate','verify_credentials','certificate_$_75', 'letter_of_reference',
        'on_course','degree_holder','form_no','verify_status','verify_status_2', 'nationality',
        'certificate_evaluated','certificate_evaluated_2','yoruba','igbo', 'hausa','history',
        'religious_knowledge', 'government', 'literature',
        'photo', 'birth_certificate_upload', 'marriage_certificate_upload',
        'olevel_certificate_upload', 'olevel2_certificate_upload', 'phn_certificate_upload',
        'phn_2_certificate_upload', 'nd_certificate_upload',
        'gender', 'major', 'exam_id','admission_date','exam_date','index_date','reg_date','validate','dont_det',
        'year_of_certificate_evaluated', 'year_of_certificate_evaluated_2','exam_number_1','exam_number_2',
        'registered_at', 'visible','indexed', 'unverified',
        'hnd_certificate_upload', 'marriage_certificate_upload',
        'exam_month','exam_month_2','reason', 'training_school'
    ];

        public function trainingSchool()
        {
                return $this->hasMany(TrainingSchool::class, 'school_code','school_code');
        }

    public function trainingSchools()
    {
        return $this->belongsTo(TrainingSchool::class, 'school_code','school_code');
    }

        public function courseHeader()
        {
            return $this->hasOne(CourseHeader::class, 'header_key', 'course_header');
        }

        public function inCourse()
        {
            return $this->hasOne(CandidateIncourse::class, 'course_header','id');
        }

    public function getModelNamespace()
    {
        return 'App\Models\CandidateIndexing';
    }

    const photo = 'photo_upload';
    const birth_certificate = 'birth_certificate_upload';
    const marriage_certificate = 'marriage_certificate_upload';
    const olevel_certificate = 'olevel_certificate_upload';
    const olevel_two_certificate = 'olevel_two_certificate_upload';
    const phn_certificate = 'phn_certificate_upload';
    const phn_two_certificate = 'phn_tow_certificate_upload';
    const nd_certificate = 'nd_certificate_upload';
    const hnd_certificate = 'hnd_certificate_upload';


    public function schoolResists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SchoolResit::class, 'candidate_index', 'candidate_index');
    }

    public function schoolResist()
    {
        return $this->belongsTo(SchoolResit::class, 'candidate_index', 'candidate_index');
    }


    public function scoreMarkerOneForCandidate(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScoreMarkerOne::class, 'candidate_index', 'candidate_index');
    }


    public function CandidateIncourseForCandidate(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CandidateIncourse::class, 'candidate_index', 'candidate_index');
    }


    public function scoreMarkerTwoForCandidate(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScoreMarkerTwo::class, 'candidate_index', 'candidate_index');
    }



    public function scoreMarkerOneForCandidates(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ScoreMarkerOne::class, 'candidate_index', 'candidate_index');
    }


    public function candidateCategory()
    {
        return $this->hasOne(CandidateCategory::class, 'category', 'candidate_category' );
    }






}
