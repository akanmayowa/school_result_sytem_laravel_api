<?php

namespace Database\Seeders;

use App\Models\ExamOffence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamOffenceSeeder extends Seeder
{

    public function run()
    {
        ExamOffence::insert([
            ['description' => 'Exam Malpractices 1st time', 'punishment' => 'Two years Waheb  Exam ban', 'duration' => '5'],
            ['description' => 'Exam Malpractices 2st time','punishment' => 'Banned from waheb exam for life', 'duration' => '1'],
            ['description' => 'Special Offences', 'punishment' => 'Result withheld Indefinintely','duration' => '2'],
            ['description' => 'Impersonation', 'punishment' => 'Banned from waheb exam for life','duration' => '4'],
            ['description' => 'WithHeld Result','punishment' => 'Two Years Waheb Exam ban','duration' => '3'],
        ]);
    }
}
