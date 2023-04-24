<?php

namespace Database\Seeders;

use App\Models\State;
use App\Models\TrainingSchool;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingSchoolStateIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $trainingSchools = TrainingSchool::whereNull('state_id')->get();

        foreach ($trainingSchools as $key => $trainingSchool) {

           if($state = State::where('code', $trainingSchool->state_code)->first()){
                $trainingSchool->state_id = $state->id;
                $trainingSchool->save();

           }

        }

    }
}
