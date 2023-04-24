<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolCategory;

class SchoolCategorySeeder extends Seeder
{
    public function run()
    {
        SchoolCategory::insert([
            [ 'category' => '1', 'description' => 'SCHOOL OF HEALTH TECHNOLOGY','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'category' => '1', 'description' => 'PRIVATE CANDIDATES','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'category' => '1','description' => 'SCHOOL/COLLEGE OF NURSING','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'category' => '1','description' => 'CATERING SCHOOL','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()]
        ]);
    }
}


