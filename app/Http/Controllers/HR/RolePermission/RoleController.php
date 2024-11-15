<?php

namespace App\Http\Controllers\HR\RolePermission;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function createSingleRole(Request $request): JsonResponse
    {
        try {
            if ($request->query('query') === 'deletemany') {
                $data = json_decode($request->getContent(), true);
                $deleteMany = Role::destroy($data);

                return response()->json([
                    'count' => $deleteMany,
                ], 200);
            } elseif ($request->query('query') === 'createmany') {
                $data = json_decode($request->getContent(), true);

                //check if product already exists
                $data = collect($data)->map(function ($item) {
                    $data = Role::where('name', $item['name'])->first();
                    if ($data) {
                        return null;
                    }
                    return $item;
                })->filter(function ($item) {
                    return $item !== null;
                })->toArray();

                //if all products already exists
                if (count($data) === 0) {
                    return response()->json(['error' => 'All Role already exists.'], 500);
                }

                $createdProduct = collect($data)->map(function ($item) {
                    return Role::firstOrCreate($item);
                });

                return response()->json(['count' => count($createdProduct)], 201);
            } else {
                $createdRole = Role::create([
                    'name' => $request->input('name'),
                ]);
                return $this->response($createdRole->toArray());
            }
        } catch (Exception $error) {
            return response()->json(['error' => 'An error occurred during create Role. Please try again later.'], 500);
        }
    }

    public function getAllRole(Request $request): JsonResponse
    {
        if ($request->query('query') === 'all') {
            try {
                $allRole = Role::orderBy('id', 'desc')
                    ->where('status', 'true')
                    ->with('rolePermission.permission')
                    ->get();

                return $this->response([
                    'getAllRole' => $allRole->toArray(),
                    'totalRole' => Role::where('status', 'true')
                        ->count(),
                ]);
            } catch (Exception $err) {
                return response()->json(['error' => 'An error occurred during getting Role. Please try again later.'], 500);
            }
        } else if ($request->query('query') === 'search') {
            try {
                $pagination = getPagination($request->query());
                $key = trim($request->query('key'));

                $getAllRole = Role::orWhere('name', 'LIKE', '%' . $key . '%')
                    ->with('rolePermission.permission')
                    ->orderBy('id', 'desc')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $allRoleCount = Role::orWhere('name', 'LIKE', '%' . $key . '%')
                    ->count();

                return $this->response([
                    'getAllRole' => $getAllRole->toArray(),
                    'totalRole' => $allRoleCount,
                ]);
            } catch (Exception $error) {
                return response()->json(['error' => 'An error occurred during getting Role. Please try again later.'], 500);
            }
        } else if ($request->query()) {
            try {
                $pagination = getPagination($request->query());
                $getAllRole = Role::with('rolePermission.permission')
                    ->when($request->query('status'), function ($query) use ($request) {
                        return $query->whereIn('status', explode(',', $request->query('status')));
                    })
                    ->orderBy('id', 'desc')
                    ->skip($pagination['skip'])
                    ->take($pagination['limit'])
                    ->get();

                $allRoleCount = Role::when($request->query('status'), function ($query) use ($request) {
                    return $query->whereIn('status', explode(',', $request->query('status')));
                })
                    ->count();

                return $this->response([
                    'getAllRole' => $getAllRole->toArray(),
                    'totalRole' => $allRoleCount,
                ]);
            } catch (Exception $error) {
                return response()->json(['error' => 'An error occurred during getting Role. Please try again later.'], 500);
            }
        } else {
            return response()->json(['error' => 'Invalid query!'], 400);
        }
    }

    public function getSingleRole(Request $request, $id): JsonResponse
    {
        try {
            $singleRole = Role::with('rolePermission.permission')->find($id);
            return $this->response($singleRole->toArray());
        } catch (Exception $error) {
            return response()->json(['error' => 'An error occurred during getting Role. Please try again later.'], 500);
        }
    }

    public function updateSingleRole(Request $request, $id): JsonResponse
    {
        try {
            $updatedRole = Role::where('id', $id)->first();

            if (!$updatedRole) {
                return $this->badRequest('Role not found!');
            }

            if ($updatedRole->name === 'super-admin' && $updatedRole->id === 1) {
                return $this->unauthorized('You are not authorized to update super-admin role');
            }


            $updatedRole->update([
                'name' => $request->input('name')
            ]);

            return response()->json(['message' => 'Role Updated Successfully'], 200);

        } catch (Exception $error) {
            return response()->json(['error' => 'An error occurred during update Role. Please try again later.'], 500);
        }
    }

    public function deleteSingleRole(Request $request, $id): JsonResponse
    {
        try {
            $deletedRole = Role::where('id', $id)->first();

            if ($deletedRole->name === 'super-admin' && $deletedRole->id === 1) {
                return $this->unauthorized('You are not authorized to delete super-admin role');
            }

            $deletedRole->update([
                'status' => $request->input('status')
            ]);
            return response()->json(['message' => 'Role Deleted Successfully'], 200);
        } catch (Exception $error) {
            return response()->json(['error' => 'An error occurred during delete Role. Please try again later.'], 500);
        }
    }
}
