<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        if (env('APP_DEBUG') === true) {
            $this->call([
                RoleSeeder::class,
                UsersSeeder::class,
                PermissionSeeder::class,
                RolePermissionSeeder::class,
                CurrencySeeder::class,
                AppSettingSeeder::class,
                InvoiceCategorySeeder::class,
                NamazTimeSeeder::class,
            ]);
        } else {
            $this->call([
                RoleSeeder::class,
                UsersSeeder::class,
                PermissionSeeder::class,
                RolePermissionSeeder::class,
                CurrencySeeder::class,
                AppSettingSeeder::class,
                InvoiceCategorySeeder::class,
                NamazTimeSeeder::class,
            ]);
        }

    }
}
