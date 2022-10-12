<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AuthServiceContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\RefreshTokenRequest;
use App\Http\Requests\User\UserAuthorizeRequest;

class ApiController extends Controller
{
    public function login(UserAuthorizeRequest $request): array
    {
        return app(AuthServiceContract::class)->login($request->validated());
    }

    public function logout(): bool
    {
        return app(AuthServiceContract::class)->logout();
    }

    public function refresh(RefreshTokenRequest $request): array
    {
        return app(AuthServiceContract::class)->refreshToken($request->validated());
    }

    public function check(): array
    {
        return app(AuthServiceContract::class)->check();
    }
}
