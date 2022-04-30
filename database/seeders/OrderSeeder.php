<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;


class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orders')->insert([
            'job_id' => 1,
            'buyer_id' => 3,
            'Status' => 'pending',
            'created_at' => Carbon::now(),
            'updated_at' =>Carbon::now(),
           
        ]);
    }
}
