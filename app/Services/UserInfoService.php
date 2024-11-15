<?php

namespace App\Services;

use App\Models\Users;
use App\Traits\ErrorTrait;
use App\Traits\SearchTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class UserInfoService
{
    use SearchTrait, ErrorTrait;

    public function getAllUsers($request): JsonResponse
    {
        try {

            if ($request->query('query') === 'all') {
                return $this->getAllUsersQuery();
            } elseif ($request->query('query') === 'search') {
                return $this->searchUsersQuery($request);
            } elseif ($request->query()) {
                return $this->getUsersQuery($request);
            } else {
                return $this->badRequest('Invalid query');
            }
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    private function getAllUsersQuery(): JsonResponse
    {
        try {
            $allUser = Users::orderBy('id', 'desc')
                ->where('status', 'true')
                ->with('saleInvoice')
                ->get();

            $filteredUsers = $allUser->map(function ($user) {
                unset($user['isLogin']);
                return $user->makeHidden('password')->toArray();
            });

            $converted = array_map(function ($user) {
                return $user;
            }, $filteredUsers->toArray());

            return $this->response([
                'getAllUser' => $converted,
                'totalUser' => count($converted),
            ]);
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    private function searchUsersQuery($request): JsonResponse
    {
        try {
            $pagination = getPagination($request->query());

            $searchUser = $this->searchQry(Users::query(), ['id', 'username', 'firstName', 'lastName'], 'like', $request->query('key'))
                ->with('role:id,name')
                ->orderBy('id', 'desc')
                ->where('status', 'true')
                ->skip($pagination['skip'])
                ->take($pagination['limit'])
                ->get()
                ->toArray();

            $totalUser = $this->searchQry(Users::query(), ['id', 'username', 'firstName', 'lastName'], 'like', $request->query('key'))
                ->where('status', 'true')
                ->count();

            $filteredUsers = array_map(function ($user) {
                unset($user['isLogin']);
                return $user;
            }, $searchUser);

            return $this->response([
                'getAllUser' => $filteredUsers,
                'totalUser' => $totalUser,
            ]);
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    private function getUsersQuery($request): JsonResponse
    {
        $pagination = getPagination($request->query());
        $getUserQry = Users::where('status', $request->query('status'))
            ->with('role:id,name')
            ->orderBy('id', 'desc')
            ->skip($pagination['skip'])
            ->take($pagination['limit'])
            ->get()
            ->toArray();
        $converted = $this->arrayKeysToCamelCase($getUserQry);
        return $this->response([
            'getAllUser' => $converted,
            'totalUser' => count($getUserQry),
        ]);
    }
}
