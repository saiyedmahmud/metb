<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NamazTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
            $categories = [
                [
                    'name' => 'FAJR',
                    'time' => '5:30 AM',
                    'azanTime' => '5:00 AM',
                ],
                [
                    'name' => 'DHUHR',
                    'time' => '1:30 PM',
                    'azanTime' => '1:00 PM',
                ],
                [
                    'name' => 'ASR',
                    'time' => '5:30 PM',
                    'azanTime' => '5:00 PM',
                ],
                [
                    'name' => 'MAGRIB',
                    'time' => '7:30 PM',
                    'azanTime' => '7:00 PM',
                ],
                [
                    'name' => 'ISHA',
                    'time' => '9:30 PM',
                    'azanTime' => '9:00 PM',
                ],
                [
                    'name' => 'JUMMAH',
                    'time' => '1:30 PM',
                    'azanTime' => '1:00 PM',
                ],
            ];
    
            foreach ($categories as $category) {
                \App\Models\NamazTime::create($category);
            }
    }
}
