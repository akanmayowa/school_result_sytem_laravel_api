<?php

namespace App\Models;

use App\Enums\UserStatus;
use Exception;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    	'operator_id',
        'training_school_id',
        'photo',
        'user_status',
        'user_role',
        'two_factor_code',
        'expires_at',
        'phone_number',
        'user_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'user_status' => UserStatus::class,
    ];



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
//    protected int $two_factor_code;
//    protected Carbon $expires_at;

    public function generateTwoFactorAuthCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(1000, 999999);
        $this->expires_at = Carbon::now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorAuthCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->expires_at = null;
        $this->save();
    }

    public function isSchoolAdmin()
    {
        return $this->user_role == 'school_admin';
    }

    public function isSuperAdmin()
    {
        return $this->user_role == 'super_admin';
    }

    public function isAdmin()
    {
        return $this->user_role == 'admin';
    }

    public function isStudent()
    {
        return $this->user_role == 'student';
    }

    public function isTrainingSchoolAdmin()
    {
        return $this->user_role == 'training_school_admin';
    }


    public function trainingSchoolUser()
    {
        return $this->belongsTo(TrainingSchool::class, 'training_school_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class,'operator_id', 'operator_id');
    }
}
