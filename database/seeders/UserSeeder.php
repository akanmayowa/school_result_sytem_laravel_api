<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    public function run()
    {
        User::insert([
            [
                'name'=>'Super Admin User',
                'email' => 'superadmin@waheb.com',
                'operator_id' => '124',
                'user_role'  => 'super_admin',
                'password'=> bcrypt('123456'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'name'=>'Admin User',
                'email' => 'admin@waheb.com',
                'operator_id' => '125',
                'user_role'  => 'admin',
                'password'=> bcrypt('123456'),
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name'=>'School Admin User',
                'email' => 'schooladmin@waheb.com',
                'operator_id' => '125',
                'user_role'  => 'school_admin',
                'password'=> bcrypt('123456'),
                'created_at' =>Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
