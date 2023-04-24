<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('candidate_indexings', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_index');
            $table->string('school_code');
            $table->string('first_name');
            $table->string('title')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->dateTime('date_of_birth');
            $table->string('candidate_category');
            $table->string('years_of_experience')->nullable();
            $table->string('course_header');
            $table->string('marital_status')->nullable()->default('S');
            $table->string('english')->nullable();
            $table->string('biology')->nullable();
            $table->string('health_science')->nullable();
            $table->string('chemistry')->nullable();
            $table->string('mathematics')->nullable();
            $table->string('geography')->nullable();
            $table->string('economics')->nullable();
            $table->string('food_and_nutrition')->nullable();
            $table->string('accounting')->nullable();
            $table->string('commerce')->nullable();
            $table->string('physics')->nullable();
            $table->string('technical_drawing')->nullable();
            $table->string('integrated_science')->nullable();
            $table->string('general_science')->nullable();
            $table->string('agric')->nullable();
            $table->tinyInteger('seatings')->nullable();
            $table->string('reg_nurse')->nullable();
            $table->string('reg_midwife')->nullable();
            $table->string('month_yr');
            $table->string('month_yr_reg');
            $table->tinyInteger('verify_birth_certificate')->default(0)->nullable();
            $table->tinyInteger('verify_o_level')->default(0)->nullable();
            $table->tinyInteger('verify_marriage_certificate')->default(0)->nullable();
            $table->tinyInteger('verify_credentials')->default(0)->nullable();
            $table->tinyInteger('certificate_$_75')->default(0)->nullable();
            $table->tinyInteger('letter_of_reference')->default(0)->nullable();
            $table->tinyInteger('on_course')->default(0)->nullable();
            $table->tinyInteger('degree_holder')->default(0)->nullable();
            $table->string('form_no')->nullable();
            $table->tinyInteger('verify_status')->default(1)->nullable();
            $table->tinyInteger('verify_status_2')->default(1)->nullable();
            $table->string('nationality')->nullable();
            $table->string('certificate_evaluated')->nullable();
            $table->string('certificate_evaluated_2')->nullable();
            $table->string('yoruba')->nullable();
            $table->string('igbo')->nullable();
            $table->string('hausa')->nullable();
            $table->string('history')->nullable();
            $table->string('religious_knowledge')->nullable();
            $table->string('government')->nullable();
            $table->string('literature')->nullable();

            $table->string('photo');
            $table->string('birth_certificate_upload');
            $table->string('marriage_certificate_upload');
            $table->string('olevel_certificate_upload');
            $table->string('olevel_2_certificate_upload');
            $table->string('phn_certificate_upload');
            $table->string('phn_2_certificate_upload');
            $table->string('nd_certificate_upload')->nullable();
            $table->enum('gender',['male','female']);
            $table->string('major');
            $table->string('exam_id');
            $table->dateTime('admission_date');
            $table->string('exam_date')->nullable();
            $table->dateTime('reg_date');
            $table->enum('validate',['yes', 'no'])->default('no');
            $table->tinyInteger('dont_det')->nullable();
            $table->string('year_of_certificate_evaluated')->nullable();
            $table->string('year_of_certificate_evaluated_2')->nullable();
            $table->string('exam_number_1');
            $table->string('exam_number_2')->nullable();
            $table->dateTime('registered_at')->nullable();
            $table->tinyInteger('visible')->default(1);
            $table->tinyInteger('indexed')->default(1);
            $table->tinyInteger('unverified')->default(1);
            $table->string('hnd_certificate_upload')->nullable();
            $table->string('exam_month');
            $table->string('exam_month_2')->nullable();
            $table->text('reason');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidate_indexings');
    }
};
