<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;

class StateSeeder extends Seeder
{

    public function run()
    {
        State::insert([
            [ 'code' => 'abi', 'name' => 'abia', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'abj', 'name' => 'abuja', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'akw', 'name' => 'akwa-ibom', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'ana', 'name' => 'anambra', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'bau', 'name' => 'bauchi', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'bay', 'name' => 'bayelsa', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'ben', 'name' => 'benue', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'bor', 'name' => 'borno', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'crs', 'name' => 'cross-river', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'del', 'name' => 'delta' , 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'ebo', 'name' => 'ebonyi', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'edo', 'name' => 'edo' ,'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'eki', 'name' => 'ekiti','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'enu', 'name' => 'enugu' ,'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'gom', 'name' => 'gombe','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'imo', 'name' => 'imo','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'jig', 'name' => 'jigawa','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'kad', 'name' => 'kaduna' ,'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'kan', 'name' => 'kano' ,'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'kat', 'name' => 'katsina' ,'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'keb', 'name' => 'kebbi' ,'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'kog', 'name' => 'kogi','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'kwa', 'name' => 'kwara','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'lag', 'name' => 'lagos','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'ond', 'name' => 'ondo','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'osu', 'name' => 'osun','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'oyo', 'name' => 'oyo','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'riv', 'name' => 'rivers','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'pla', 'name' => 'plateau','created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
            [ 'code' => 'sok', 'name' => 'sokoto','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'tar', 'name' => 'taraba','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'yob', 'name' => 'yobe','created_at' =>Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'code' => 'zam', 'name' => 'zamfara', 'created_at' =>Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
