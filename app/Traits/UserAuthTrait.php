<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait UserAuthTrait
{
    use ErrorTrait;

    protected function authorizeUser($request): ?JsonResponse
    {
        $data = $request->attributes->get("data");
        if ($data['sub'] !== (int)$request['id'] && $data['role'] !== 'admin' && $data['role'] !== 'super-admin') {
            return $this->unauthorized('You are not authorized to perform this action');
        }
        return null;
    }
}
