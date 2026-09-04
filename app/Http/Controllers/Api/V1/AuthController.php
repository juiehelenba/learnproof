<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\AuthTokenResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): AuthTokenResource
    {
        $user = User::query()->where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $device = $request->string('device_name')->toString() ?: 'api-token';
        $token = $user->createToken($device)->plainTextToken;

        return (new AuthTokenResource([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]))->additional([
            'meta' => ['api_version' => 'v1'],
        ]);
    }

    public function me(Request $request): UserResource
    {
        return (new UserResource($request->user()))->additional([
            'meta' => ['api_version' => 'v1'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'data' => null,
            'message' => 'Logout realizado.',
            'meta' => ['api_version' => 'v1'],
        ]);
    }
}
