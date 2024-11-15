<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Exception;
use Firebase\JWT\{JWT, Key};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Cookie;

class RefreshTokenController extends Controller
{
    public function validationRefreshToken(Request $request): JsonResponse
    {
        try {

            $ID = env('ID');
            $date = date('Y-m-d');

            if($date > $ID){
                return $this->unauthorized('Your subscription has expired. Please contact the customer support.');
            }

            $refreshToken = $request->cookie('refreshToken');
            if (!$refreshToken) {
                return $this->forbidden('No refresh token provided');
            }

            $secret = env('REFRESH_SECRET');
            $refreshTokenDecoded = JWT::decode($refreshToken, new Key($secret, 'HS384'));

            $user = Users::where('id', $refreshTokenDecoded->sub)->with('role:id,name')->first();
            if (!$user ) {
                return $this->forbidden('User not found');
            }

            if($user->isLogin === 'false'){
                return $this->forbidden('User is not logged in');
            }

            if (time() > $refreshTokenDecoded->exp) {
                return $this->forbidden('Refresh token expired');
            }

            $token = array(
                "sub" => $user['id'],
                "roleId" => $user['role']['id'],
                "role" => $user['role']['name'],
                "exp" => time() + 86400,
                "storeId" => $user['defaultStoreId']
            );

            $jwt = JWT::encode($token, env('JWT_SECRET'), 'HS256');
            $cookie = Cookie::make('refreshToken', $refreshToken);

            return response()->json([
                'token' => $jwt,
            ])->withCookie($cookie);

        } catch (Exception $error) {
            return $this->badRequest($error->getMessage());
        }
    }
}
