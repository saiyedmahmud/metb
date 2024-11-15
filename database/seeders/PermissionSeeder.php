<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        define('endpoints', [
            "rolePermission",
            "permission",
            "dashboard",
            "user",
            "role",
            "account",
            "setting",
            "pageSize",
            "announcement",
            "currency",
            'termsAndCondition',
            'media',
            'namazTime',
            'invoiceCategory',
            'invoice',
            'transaction',
        ]);

        define('PERMISSIONSTYPES', [
            'create',
            'readAll',
            "readSingle",
            'update',
            'delete',
        ]);
        foreach (endpoints as $endpoint) {
            foreach (PERMISSIONSTYPES as $permissionType) {
                $permission = new Permission();
                $permission->name = $permissionType . "-" . $endpoint;
                $permission->save();
            }
        }
    }
}
