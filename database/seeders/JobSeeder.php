<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jobs')->insert([
            'name' => 'Carpet Cleaning',
            'description' => 'Proffesional cleaning of carpets, get rid of all the dirt and grime',
            'price' => 2000,
            'user_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' =>Carbon::now(),
           
        ]);

    }
}
