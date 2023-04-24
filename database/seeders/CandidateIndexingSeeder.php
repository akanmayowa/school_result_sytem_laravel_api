<?php

namespace Database\Seeders;

use App\Models\CandidateIndexing;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateIndexingSeeder extends Seeder
{

    public function run()
    {
        CandidateIndexing::insert([
        'candidate_index' => '3A5',
        'school_code' => 'IMSU',
        'first_name' => 'JANET',
        'title' => 'MISS',
        'middle_name' => 'JONNET',
        'last_name' => 'LANCEY',
        'date_of_birth' => '2017-06-01 09:00:00',
        'candidate_category' => 'A',
        'years_of_experience' => '2017-06-01 09:00:00',
        'course_header' => 'A1',
        'marital_status' => 'S',
        'english' => 'A1',
        'biology' => 'A2',
        'health_science' => 'A3',
        'chemistry' => 'A4',
        'mathematics' => 'A5',
        'geography' => 'A6',
        'economics' => 'A7',
        'food_and_nutrition' => 'A8',
        'accounting' => 'A9',
        'commerce' => 'A10',
        'physics' => 'A11',
        'technical_drawing' => 'A12',
        'integrated_science' => 'A13',
        'general_science' => 'A14',
        'agric' => 'A15',
        'seatings' => 1,
        'reg_nurse' => 'NRM',
        'reg_midwife' => 'NRM',
        'month_yr' => '10',
        'month_yr_reg' => '11',
        'verify_birth_certificate' => 0,
        'verify_o_level' => 0,
        'verify_marriage_certificate' => 0,
        'verify_credentials' => 0,
        'certificate_$_75' => 0,
        'letter_of_reference' => 0,
        'on_course' => 0,
        'degree_holder' => 1,
        'form_no' => null,
        'verify_status' => 1,
        'verify_status_2' => 1,
        'nationality' => 'NIGERIAN',
        'certificate_evaluated' => '04',
        'certificate_evaluated_2' => '03',
        'yoruba' => 'A2',
        'igbo' => 'A3',
        'hausa' => 'A4',
        'history' => 'A5',
        'religious_knowledge' => 'A3',
        'government' => 'A8',
        'literature' => 'A7',


        'gender' => 'male',
        'major' => 'null',
        'exam_id' => '8A81',
        'admission_date' => '2017-06-01 09:00:00',
        'exam_date' => '2017-06-01 09:00:00',
        'index_date' => '2017-06-01 09:00:00',
        'reg_date' => '2017-06-01 09:00:00',
        'validate' => 'no',
        'dont_det' => 0,
        'year_of_certificate_evaluated' => '1998',
        'year_of_certificate_evaluated_2' => '2022',
        'exam_number_1' => '2345',
        'exam_number_2' => '123',
        'registered_at' => '2017-06-01 09:00:00',
        'visible' => 0,
        'indexed' => 0,
        'unverified' => 0,


            'exam_month' => '09',
            'exam_month_2' => '10',
            'reason' => 'non for now',
            'created_at' =>Carbon::now(),
            'updated_at' => Carbon::now(),


        'photo' => 'default.jpg',
        'birth_certificate_upload' => 'default.jpg',
        'marriage_certificate_upload' => 'default.jpg',

        'olevel_certificate_upload' => 'default.jpg',
        'olevel_2_certificate_upload' => 'default.jpg',
        'phn_certificate_upload' => 'default.jpg',
        'phn_2_certificate_upload' => 'default.jpg',
        'nd_certificate_upload' => 'default.jpg',
        'hnd_certificate_upload' => 'default.jpg',
]);
    }
}
