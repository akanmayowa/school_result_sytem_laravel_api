<?php

namespace Database\Seeders;

use App\Models\Grade;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{

    public function run()
    {
        Grade::insert([
            ['name' => 'A1','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'AB','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'B','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'BC','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'C','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'CD','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'D','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'E','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'F','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
