<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\UserInfoService;
use App\Services\UserService;
use App\Traits\UserAuthTrait;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Users;
use App\Models\UsersStore;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Cookie, DB};

class UsersController extends Controller
{
    use UserAuthTrait;

    protected UserService $userService;
    protected UserInfoService $userInfoService;

    public function __construct(UserService $userService, UserInfoService $userInfoService)
    {
        $this->userService = $userService;
        $this->userInfoService = $userInfoService;
    }

    public function login(Request $request): JsonResponse
    {
        try {
            return $this->userService->UserAuthenticate($request);
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = Users::findOrFail($request->id);
            $user->isLogin = 'false';
            $user->save();
            $cookie = Cookie::forget('refreshToken');

            return $this->success("Logout successfully")->withCookie($cookie);
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'username' => 'required|string|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string',
            ]);

            return $this->userService->createUser($request->all());
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    // get all the user controller method
    public function getAllUser(Request $request): JsonResponse
    {
        return $this->userInfoService->getAllUsers($request);
    }

    // get a single user controller method
    public function getSingleUser(Request $request): JsonResponse
    {
        try {

            $authorizationResult = $this->authorizeUser($request);
            if ($authorizationResult !== null) {
                return $authorizationResult;
            }

            $singleUser = Users::where('id', $request['id'])
                ->with('saleInvoice', 'employmentStatus', 'shift', 'education', 'awardHistory.award', 'salaryHistory', 'designationHistory.designation', 'quote', 'role', 'department', 'usersStore.store')
                ->first();
            if (!$singleUser) {
                return $this->notFound('User not found!');
            }

            $userWithoutPassword = $singleUser->toArray();
            unset($userWithoutPassword['password']);
            unset($userWithoutPassword['isLogin']);

            return $this->response($userWithoutPassword);
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    public function updateSingleUser(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $joinDate = new DateTime($request->input('joinDate'));
            $leaveDate = $request->input('leaveDate') !== null ? new DateTime($request->input('leaveDate')) : null;

            if ($request->input('password')) {
                $hash = Hash::make($request->input('password'));
                $request->merge([
                    'password' => $hash,
                ]);
            }

            $joinDateString = $joinDate->format('Y-m-d H:i:s');
            $leaveDateString = $leaveDate?->format('Y-m-d H:i:s');

            $storeIds = [];
            if ($request->input('storeId')) {
                $storeIds = $request->input('storeId');
                $request->merge([
                    'defaultStoreId' => $storeIds[0]
                ]);
            }

            $request->merge([
                'joinDate' => $joinDateString,
                'leaveDate' => $leaveDateString,
            ]);

            $user = Users::findOrFail((int)$id);

            if (!$user) {
                return $this->badRequest('User not found!');
            }

            //if role is super admin you can not change your own roleId, if not super admin you can not change roleId to super admin
            if ($request->input('roleId') === 1) {
                return $this->badRequest('You can not change the role to super admin');
            }

            if ($user->roleId === 1 && $request->input('roleId')) {
                return $this->badRequest('You can not change super admin role');
            }

            if ($user->roleId === 1) {
                $user->update($request->all());
                $user->save();
            } else {
                $user->update($request->all());
                $user->save();
            }


            $userWithoutPassword = $user->toArray();
            unset($userWithoutPassword['password']);
            unset($userWithoutPassword['isLogin']);
            DB::commit();
            return $this->response($userWithoutPassword);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('User not found!');
        } catch (Exception $error) {
            DB::rollback();
            return $this->badRequest($error->getMessage());
        }
    }

    public function deleteUser(Request $request, $id): JsonResponse
    {
        try {
            //update the status
            $user = Users::findOrFail($id);
            if (!$user) {
                return $this->notFound('User not found!');
            }
            $user->status = $request->input('status');
            $user->save();
            return $this->success('User status updated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->notFound('User not found!');
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }
}
