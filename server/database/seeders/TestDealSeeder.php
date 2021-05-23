<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deal;
use App\Models\Bid;

class TestDealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Deal::factory()->count(15)->create();
        Bid::factory()->count(15)->create();
    }
}
