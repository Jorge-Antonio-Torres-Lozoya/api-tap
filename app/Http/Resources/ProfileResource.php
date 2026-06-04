<?php

namespace App\Http\Resources;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => (string) $this->getKey(),
            'code'       => $this->code,
            'name'       => $this->name,
            'sections'   => $this->resolvedSections(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    private function resolvedSections(): array
    {
        $slugs = $this->sections ?? [];

        if (empty($slugs)) {
            return [];
        }

        return Section::whereIn('slug', $slugs)
            ->get()
            ->map(fn ($s) => [
                'code' => $s->code,
                'name' => $s->name,
                'slug' => $s->slug,
            ])
            ->toArray();
    }
}
