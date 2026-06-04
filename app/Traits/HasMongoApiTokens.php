<?php

namespace App\Traits;

use App\Support\NewAccessToken;
use DateTimeInterface;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;

/**
 * Wraps Sanctum's HasApiTokens to override createToken so it returns our
 * MongoDB-compatible NewAccessToken. The Sanctum trait is still used internally
 * so Guard::supportsTokens() (which checks class_uses_recursive) keeps working.
 */
trait HasMongoApiTokens
{
    use SanctumHasApiTokens {
        createToken as protected sanctumCreateToken;
    }

    public function createToken(string $name, array $abilities = ['*'], ?DateTimeInterface $expiresAt = null): NewAccessToken
    {
        $plainTextToken = Str::random(40);

        $token = $this->tokens()->create([
            'name'       => $name,
            'token'      => hash('sha256', $plainTextToken),
            'abilities'  => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return new NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
    }
}
