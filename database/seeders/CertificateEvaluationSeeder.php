<?php

namespace Database\Seeders;

use App\Models\CertificateEvaluation;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CertificateEvaluationSeeder extends Seeder
{

    public function run()
    {
        CertificateEvaluation::insert([
            ['certification_id' => '01', 'description' => 'GCE (General Certificate of Education)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['certification_id' => '02', 'description' => 'SSCE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['certification_id' => '03', 'description' => 'NECO', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['certification_id' => '04', 'description' => 'GRADE II', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['certification_id' => '05', 'description' => 'NABTEB', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['certification_id' => '06', 'description' => 'A DEGREE CERTIFICATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['certification_id' => '07', 'description' => 'HND CERTIFICATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['certification_id' => '08', 'description' => 'CITY AND GUILDS', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['certification_id' => '09', 'description' => 'OND CERTIFICATE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
