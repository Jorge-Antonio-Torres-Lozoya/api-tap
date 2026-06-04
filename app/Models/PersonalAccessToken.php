<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\Contracts\HasAbilities;
use MongoDB\Laravel\Eloquent\Model;

class PersonalAccessToken extends Model implements HasAbilities
{
    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens';

    protected $fillable = ['name', 'token', 'abilities', 'expires_at', 'last_used_at'];

    protected $hidden = ['token'];

    protected $casts = [
        'abilities'    => 'array',
        'expires_at'   => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function findToken(string $token): ?static
    {
        if (!str_contains($token, '|')) {
            return static::where('token', hash('sha256', $token))->first();
        }

        [$id, $plainText] = explode('|', $token, 2);
        $instance = static::find($id);

        if ($instance && hash_equals($instance->token, hash('sha256', $plainText))) {
            return $instance;
        }

        return null;
    }

    public function can($ability): bool
    {
        return in_array('*', $this->abilities ?? [])
            || in_array($ability, $this->abilities ?? []);
    }

    public function cant($ability): bool
    {
        return !$this->can($ability);
    }
}
