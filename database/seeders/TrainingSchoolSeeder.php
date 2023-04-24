<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\UserStatus;
use App\Models\TrainingSchool;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TrainingSchoolSeeder extends Seeder
{

    public function run()
    {
        // TrainingSchool::insert([
        //   ['school_code' => 'ABI01', 'index_code' => 'AB', 'state_id' => 1, 'school_name' => 'LASTECH', 'password' => bcrypt(123456) ,
        //   'school_category_id' => 1, 'contact' => 'monica', 'position' => 'vice principal', 'phone' => '09012312342',
        //    'email' => 'trainingschool@gmail.com', 'status' => 1,'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],

        //   ['school_code' => 'EK014', 'index_code' => 'MB', 'state_id' => 2, 'school_name' => 'IMSU','password' => bcrypt(123456),
        //   'school_category_id' => 1, 'contact' => 'collins', 'position' => 'provost', 'phone' => '08023118781',
        //    'email' => 'trainingschool@admin.com', 'status' => 0, 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()]
        // ]);

        $data =  ['school_code' => 'ABI01', 'index_code' => 'AB', 'state_id' => 1, 'school_name' => 'LASTECH', 'password' => bcrypt(123456) ,
          'school_category_id' => 1, 'contact' => 'monica', 'position' => 'vice principal', 'phone' => '09012312342',
           'email' => 'trainingschool@gmail.com', 'status' => 1,'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()];

        $trainingSchool = TrainingSchool::create($data);
        User::create([
             'name'  => $data['school_name'],
            'email'   => $data['email'],
            'password'   => $data['password'],
            'operator_id'   => '1441',
            'training_school_id' => $trainingSchool->id,
            // 'photo',
            'user_status' => UserStatus::Active,
            'user_role' => 'training_school_admin',
            // 'two_factor_code' => '',
            // 'expires_at',
            // 'phone_number' => $data['phone']
        ]);


    }
}
