<?php

namespace Database\Seeders;

use App\Models\UsersStore;
use Carbon\Carbon;
use App\Models\Users;
use App\Models\Education;
use App\Models\SalaryHistory;
use Illuminate\Database\Seeder;
use App\Models\DesignationHistory;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $user = new Users();
        $user->firstName = 'Al';
        $user->lastName = 'Amin';
        $user->username = 'Muhammad';
        $user->email = 'muhammad@gmail.com';
        $user->phone = '0000000000';
        $user->password = Hash::make('5555');
        $user->roleId = 1;
        $user->save();



      
    }
}
