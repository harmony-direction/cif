<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NationalitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('nationalities')->insert([
            [
            'name' =>'ไทย'
            ],
            [
            'name' =>'เมียนมา'
            ],
            [
            'name' =>'กัมพูชา'
            ],
            [
            'name' =>'ลาว'
            ],
            [
            'name' =>'ฟิลิปปินส์'
            ]
        ]);
    }
}
