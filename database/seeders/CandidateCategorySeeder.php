<?php

namespace Database\Seeders;

use App\Models\CandidateCategory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateCategorySeeder extends Seeder
{

    public function run()
    {
        CandidateCategory::insert([
            ['category' => 'A', 'description'=>'Student of the School of Health Technology','created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['category' => 'B','description'=> 'Student of the School of Public Health Nursing','created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['category' => 'C','description' => 'Student of the School of Catering and Hotel Management','created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['category' => 'D','description' => 'Student of Environmental Health Technicians','created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['category' => 'E','description' => 'Student of the School of Health Assistants','created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['category' => 'F','description' => 'Students of the School of Food Hygiene(Certificate','created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['category' => 'G','description' => 'Student of the School of Food Hygiene(Diploma)','created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ]);
    }
}
