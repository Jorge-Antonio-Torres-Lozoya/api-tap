<?php

namespace App\Support;

use App\Models\PersonalAccessToken;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * MongoDB-compatible replacement for Laravel\Sanctum\NewAccessToken, whose
 * constructor type-hints the concrete SQL-bound PersonalAccessToken model.
 */
class NewAccessToken implements Arrayable, Jsonable
{
    public function __construct(
        public PersonalAccessToken $accessToken,
        public string $plainTextToken
    ) {}

    public function toArray(): array
    {
        return [
            'accessToken'    => $this->accessToken,
            'plainTextToken' => $this->plainTextToken,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
