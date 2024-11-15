<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\Users;
use App\Models\UsersStore;
use App\Traits\ErrorTrait;
use Closure;
use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

class AuthorizeMiddleware
{
    use ErrorTrait;

    public function handle(Request $request, Closure $next, $permissions)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->unauthorized('Token not provided');
        }

        try {
            /*
             * Decode the token
             */
            $secret = env('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $decoded_array = (array)$decoded;
            /*
             * Check if the user is still login
             */

            if ($decoded_array['role'] === 'admin') {
                $user = User::find($decoded_array['sub']);
                if ($user->isLogin === 'false') {
                    return $this->unauthorized('Your are not authorized to access this route. Please login first.');
                }
            }

            if ($decoded_array['role'] === 'super-admin') {
                $user = User::find($decoded_array['sub']);
            
                if ($user->isLogin === 'false') {
                    return $this->unauthorized('Your are not authorized to access this route. Please login first.');
                }
            }


            /*
             * Check if the user has the permission to access the route
             */
            $permission = Permission::where('name', $permissions)->first();
            $rolePermission = RolePermission::where('roleId', $decoded_array['roleId'])->where('permissionId', $permission->id)->first();
            if (!$rolePermission) {
                return $this->forbidden('You are not authorized to access this route');
            }

            $request->attributes->set('data', $decoded_array);
            return $next($request);
        } catch (BeforeValidException $e) {
            return $this->forbidden('Invalid token');
        } catch (Exception $e) {
            return $this->unauthorized($e);
        }
    }
}
