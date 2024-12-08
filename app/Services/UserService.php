<?php

namespace App\Services;

use PDO;
use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Store;
use App\Models\Users;
use Firebase\JWT\JWT;
use App\Models\Tenant;
use App\Models\Education;
use App\Models\UsersStore;
use App\Traits\ErrorTrait;
use Illuminate\Support\Str;
use App\Models\Subscription;
use App\Models\SalaryHistory;
use App\Models\RolePermission;
use GuzzleHttp\Promise\Create;
use Database\Seeders\UsersSeeder;
use Illuminate\Http\JsonResponse;
use App\Models\DesignationHistory;
use Database\Seeders\TenantSeeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\{DB, Hash, Cookie, Http};

class UserService
{
    use ErrorTrait;

    public function UserAuthenticate($request): JsonResponse
    {
        try {
            $user = Users::where('username', $request->input('username'))->with('role:id,name')->first();

            if (!$user) {
                return $this->unauthorized("Username or password is incorrect");
            }

            $pass = Hash::check($request->input('password'), $user->password);

            if (!$pass) {
                return $this->unauthorized("Username or password is incorrect");
            }

            $token = $this->generateToken($user);
            $refreshToken = $this->generateRefreshToken($user);
            $cookie = $this->createRefreshTokenCookie($refreshToken);
            $userWithoutPassword = $this->prepareUserData($user, $token);

            $user->refreshToken = $refreshToken;
            $user->isLogin = 'true';
            $user->save();

            return $this->response($userWithoutPassword)->withCookie($cookie);
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

    private function generateToken($user): string
    {
        $token = [
            "sub" => $user->id,
            "roleId" => $user['role']['id'],
            "role" => $user['role']['name'],
            "exp" => time() + (60 * 60 * 6),
            "storeId" => $user['defaultStoreId'],
            "tenantId" => $user['tenantId']
        ];

        return JWT::encode($token, env('JWT_SECRET'), 'HS256');
    }

    private function generateRefreshToken($user): string
    {
        $refreshToken = [
            "sub" => $user->id,
            "role" => $user['role']['name'],
            "exp" => time() + 86400 * 30
        ];

        return JWT::encode($refreshToken, env('REFRESH_SECRET'), 'HS384');
    }

    private function createRefreshTokenCookie($refreshToken): \Symfony\Component\HttpFoundation\Cookie
    {
        return Cookie::make('refreshToken', $refreshToken, 60 * 24 * 30)
            ->withPath('/')
            ->withHttpOnly()
            ->withSameSite('None')
            ->withSecure();
    }

    private function prepareUserData($user, $token): array
    {
        $userWithoutPassword = $user->toArray();
        $userWithoutPassword['role'] = $user['role']['name'];
        $userWithoutPassword['token'] = $token;
        unset($userWithoutPassword['password']);

        return $userWithoutPassword;
    }

    // Register User
    public function createUser(array $userData): JsonResponse
    {
        try {
            DB::beginTransaction();
            try {
                $hash = Hash::make($userData['password']);

                // Now proceed to create the user using the Tenant ID
                $createUser = Users::create([
                    'firstName' => $userData['firstName'] ?? null,
                    'lastName' => $userData['lastName'] ?? null,
                    'username' => $userData['username'],
                    'password' => $hash,
                    'roleId' =>1,
                    'email' => $userData['email'] ?? null,
                    'phone' => $userData['phone'] ?? null,
                    'image' => $userData['image'] ?? null,
                ]);

                if (!$createUser) {
                    return $this->badRequest('User not created');
                }

                $this->updateRolePermission($userData);

                unset($createUser['password']);

                DB::commit();
                return $this->response($createUser->toArray());
            } catch (Exception $error) {
                DB::rollback();
                return $this->badRequest($error->getMessage());
            }
        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }

 



    private function updateRolePermission($userData): JsonResponse
    {
        try {
            // Update role permission
            RolePermission::create([
                'roleId' => $userData['roleId'],
                'permissionId' => 28
            ]);
            return $this->success('Role Permission updated successfully');
        } catch (Exception $error) {
            return $this->badRequest($error);
        }
    }


}
