<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('categories')->insert([
            'name' => 'Бухгалтер',
            'description' => 'dsadas',
        ]);
        \DB::table('categories')->insert([
            'name' => 'Юрист',
            'description' => 'dsadas',
        ]);
        \DB::table('directions')->insert([
            'id' => 1,
            'name' => 'Бухгалтерский учёт',
            'description' => 'dsadas',
            'cost_price' => '600',
            'extra' => '20',
            'categories' => '[1]',
            'iframe_url' => '',
        ]);
        \DB::table('directions')->insert([
            'id' => 2,
            'name' => 'Банкротство',
            'description' => 'dsadas1',
            'cost_price' => '400',
            'extra' => '15',
            'categories' => '[1]',
            'iframe_url' => '',
        ]);
        
        \DB::table('status')->insert([
            'id' => 1,
            'name' => 'Начальный',
            'type' => '1000',
            'order' => '1'
        ]);
        \DB::table('status')->insert([
            'id' => 2,
            'name' => 'Спорный',
            'type' => '1003',
            
            'order' => '2'
        ]);
        \DB::table('status')->insert([
            'id' => 3,
            'name' => 'Приянто',
            'type' => '1002',
            'order' => '3'
        ]);
        \DB::table('status')->insert([
            'id' => 4,
            'name' => 'Подписан документ',
            'type' => '1002',
            'order' => '4'
        ]);

        \DB::table('status')->insert([
            'id' => 5,
            'name' => 'Не распределенно',
            'type' => '1004',
            'order' => '99'
        ]);


        \DB::table('disput_type')->insert([
            'name' => 'Битый номер',
        ]);
        \DB::table('disput_type')->insert([
            'name' => 'Другое',
        ]);

        \DB::table('bids')->insert([
            'regions' => '[]',
            'direction_id' => 1,
            'category_id' => 1,
            'consumption' => 950.0,
            'is_launch' => false,
            'is_notification' => false,
            'is_delete' => false,
            'daily_limit' => 0,
            'insurance' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
    }
}
