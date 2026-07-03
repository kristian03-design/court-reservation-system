<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private JwtService $jwt) {}

    // ──────────────────────────────────────────────────
    // POST /api/v1/auth/login
    // ──────────────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if (! $user->isActive()) {
            return response()->json(['message' => 'Account is suspended.'], 403);
        }

        return response()->json([
            'message' => 'Login successful.',
            'user'    => $this->userPayload($user),
            'auth'    => $this->jwt->tokenResponse($user),
        ]);
    }

    // ──────────────────────────────────────────────────
    // POST /api/v1/auth/register  (customers only)
    // ──────────────────────────────────────────────────
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        return response()->json([
            'message' => 'Registration successful.',
            'user'    => $this->userPayload($user),
            'auth'    => $this->jwt->tokenResponse($user),
        ], 201);
    }

    // ──────────────────────────────────────────────────
    // POST /api/v1/auth/logout   (requires: jwt.auth)
    // ──────────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        // Token is already validated by JwtAuthenticate middleware.
        // Since we're stateless, we just instruct the client to discard it.
        // For server-side revocation, store the jti in cache/DB blacklist here.
        return response()->json(['message' => 'Successfully logged out.']);
    }

    // ──────────────────────────────────────────────────
    // POST /api/v1/auth/refresh
    // ──────────────────────────────────────────────────
    public function refresh(Request $request): JsonResponse
    {
        $token = $this->jwt->extractBearerToken($request);

        if (! $token) {
            return response()->json(['message' => 'No refresh token provided.'], 401);
        }

        $payload = $this->jwt->decodeOrNull($token);

        if (! $payload) {
            return response()->json(['message' => 'Refresh token is invalid or expired.'], 401);
        }

        if (($payload->type ?? '') !== 'refresh') {
            return response()->json(['message' => 'Provided token is not a refresh token.'], 400);
        }

        $user = User::find($payload->sub);

        if (! $user || ! $user->isActive()) {
            return response()->json(['message' => 'User not found or account suspended.'], 401);
        }

        return response()->json([
            'message' => 'Token refreshed.',
            'auth'    => $this->jwt->tokenResponse($user),
        ]);
    }

    // ──────────────────────────────────────────────────
    // GET /api/v1/auth/me   (requires: jwt.auth)
    // ──────────────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->userPayload($request->user()),
        ]);
    }

    // ──────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────
    private function userPayload(User $user): array
    {
        return [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'phone'  => $user->phone,
            'role'   => $user->role,
            'status' => $user->status,
        ];
    }
}
