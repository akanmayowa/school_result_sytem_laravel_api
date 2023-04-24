<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseHeader;

class CourseHeaderSeeder extends Seeder
{

    public function run()
    {
        CourseHeader::insert([
            [
                'header_key' => 'A1.PMH', 'description' => 'public mental health',
                'cadre' => 'HND', 'delete_status' => 0, 'total_units' => 22, 'modules' => 10,
                'exam_date' => Carbon::now(), 'add_year' => '3',   'month' => '06',
                'index_code' => 'A5-GG', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            [
                'header_key' => 'A2.SZ', 'description' => "Zoology", 'cadre' => 'BSC', 'delete_status' => 0,
                'total_units' => 33, 'modules' => 20, 'exam_date' => Carbon::now(),
                'add_year' =>'2',  'month' =>'04', 'index_code' => 'A3-DD',
                'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
