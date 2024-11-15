<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = new AppSetting();
        $setting->companyName = 'BUBT MOSQUE';
        $setting->dashboardType = 'inventory';
        $setting->tagLine = 'Manage your income and expenses with ease';
        $setting->address = 'House: 139, Road: 13, Sectorr: 10, Uttara, Dhaka-1230';
        $setting->phone = '+880 18 2021 5555';
        $setting->email = 'demo@gmail.com';
        $setting->website = 'https://demo.com';
        $setting->footer = '© 2024 MET. All rights reserved.';
        $setting->logo = null;
        $setting->currencyId = 3;

        $setting->save();
    }
}
