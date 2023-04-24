<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'description' ,'certification_id'
    ];
}
