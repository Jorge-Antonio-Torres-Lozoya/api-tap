<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lightweight resource for the users list.
 */
class UserListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => (string) $this->getKey(),
            'code'       => $this->code,
            'username'   => $this->username,
            'name'       => $this->name,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}