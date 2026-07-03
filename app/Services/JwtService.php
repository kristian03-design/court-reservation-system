<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;

class JwtService
{
    private string $secret;
    private string $algo;
    private int $ttl;
    private int $refreshTtl;
    private string $issuer;

    public function __construct()
    {
        $this->secret     = config('jwt.secret');
        $this->algo       = config('jwt.algo', 'HS256');
        $this->ttl        = (int) config('jwt.ttl', 60);
        $this->refreshTtl = (int) config('jwt.refresh_ttl', 10080);
        $this->issuer     = config('jwt.issuer', config('app.url'));
    }

    /**
     * Issue a new access token for the given user.
     */
    public function issueAccessToken(User $user): string
    {
        $now = time();

        $payload = [
            'iss'    => $this->issuer,
            'sub'    => (string) $user->id,
            'iat'    => $now,
            'exp'    => $now + ($this->ttl * 60),
            'jti'    => Str::uuid()->toString(),
            'type'   => 'access',
            // Custom claims
            'role'   => $user->role,
            'name'   => $user->name,
            'email'  => $user->email,
            'status' => $user->status,
        ];

        return JWT::encode($payload, $this->secret, $this->algo);
    }

    /**
     * Issue a refresh token (longer-lived, fewer claims).
     */
    public function issueRefreshToken(User $user): string
    {
        $now = time();

        $payload = [
            'iss'  => $this->issuer,
            'sub'  => (string) $user->id,
            'iat'  => $now,
            'exp'  => $now + ($this->refreshTtl * 60),
            'jti'  => Str::uuid()->toString(),
            'type' => 'refresh',
        ];

        return JWT::encode($payload, $this->secret, $this->algo);
    }

    /**
     * Decode and validate a token. Returns the payload object or throws.
     */
    public function decode(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, $this->algo));
    }

    /**
     * Try to decode safely — returns null on failure.
     */
    public function decodeOrNull(string $token): ?object
    {
        try {
            return $this->decode($token);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Extract the Bearer token from the Authorization header.
     */
    public function extractBearerToken(\Illuminate\Http\Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }

    /**
     * Build the standard token response array.
     */
    public function tokenResponse(User $user, bool $includeRefresh = true): array
    {
        $response = [
            'access_token' => $this->issueAccessToken($user),
            'token_type'   => 'bearer',
            'expires_in'   => $this->ttl * 60,
        ];

        if ($includeRefresh) {
            $response['refresh_token'] = $this->issueRefreshToken($user);
        }

        return $response;
    }

    public function getAccessTtlSeconds(): int
    {
        return $this->ttl * 60;
    }
}
