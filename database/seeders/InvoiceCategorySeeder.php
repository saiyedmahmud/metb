<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    
        $categories = [
            [
                'name' => 'MAGRIB COLLECTION',
                'type' => 'donation',
            ],
            [
                'name' => 'JUMMAH COLLECTION',
                'type' => 'donation',
            ],
            [
                'name' => 'ZAKAT COLLECTION',
                'type' => 'donation',
            ],
            [
                'name' => 'SADAQAH COLLECTION',
                'type' => 'donation',
            ],
            [

                'name' => 'DONATION COLLECTION',
                'type' => 'donation',
            ],

            [
                'name' => 'SALARY',
                'type' => 'expense',
            ],
            [
                'name' => 'RENT',
                'type' => 'expense',
            ],
            [
                'name' => 'ELECTRICITY',
                'type' => 'expense',
            ],
            [
                'name' => 'WATER',
                'type' => 'expense',
            ],
            [
                'name' => 'INTERNET',
                'type' => 'expense',
            ],
            [
                'name' => 'GAS',
                'type' => 'expense',
            ],
            [
                'name' => 'STATIONARY',
                'type' => 'expense',
            ],
            [
                'name' => 'CLEANING',
                'type' => 'expense',
            ],
            [
                'name' => 'MAINTENANCE',
                'type' => 'expense',
            ],
            [
                'name' => 'TRANSPORT',
                'type' => 'expense',
            ],
            [
                'name' => 'OTHER',
                'type' => 'expense',
            ],
        ];
    
        foreach ($categories as $category) {
            \App\Models\InvoiceCategory::create($category);
        }


    }
}
