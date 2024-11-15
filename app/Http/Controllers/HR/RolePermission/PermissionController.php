<?php

namespace App\Http\Controllers\HR\RolePermission;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    //get all permission
    public function getAllPermission(Request $request): JsonResponse
    {
        if ($request->query('query') === 'all') {
            try {
                $allPermissions = Permission::orderBy('id', 'desc')->get();

                return $this->response($allPermissions->toArray());
            }catch (Exception $error) {
                return response()->json(['error' => 'An error occurred during getting permission. Please try again later.'], 500);
            }
        } else {
            $pagination = getPagination($request->query());
            try {
                $permissions = Permission::orderBy('id', 'desc')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                return $this->response($permissions->toArray());
            } catch (Exception $error) {
                return response()->json(['error' => 'An error occurred during getting permission. Please try again later.'], 500);
            }
        }
    }
}
