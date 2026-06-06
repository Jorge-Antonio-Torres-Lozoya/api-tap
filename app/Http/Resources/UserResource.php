<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (string) $this->getKey(),
            'code'          => $this->code,
            'name'          => $this->name,
            'username'      => $this->username,
            'phone'         => $this->phone,
            'profile_photo' => $this->profile_photo
                ? asset('storage/'.$this->profile_photo)
                : null,
            'profiles'      => $this->profiles()->map(fn ($p) => [
                'id'   => (string) $p->getKey(),
                'code' => $p->code,
                'name' => $p->name,
            ]),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
