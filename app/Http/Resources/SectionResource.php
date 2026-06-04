<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => (string) $this->getKey(),
            'code' => $this->code,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
