<?php

namespace Database\Seeders;

use App\Enums\SectionSlugEnum;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'code' => 'SEC-0001',
                'name' => 'Productos',
                'slug' => SectionSlugEnum::PRODUCTS->value,
            ],
            [
                'code' => 'SEC-0002',
                'name' => 'Usuarios',
                'slug' => SectionSlugEnum::USERS->value,
            ],
            [
                'code' => 'SEC-0003',
                'name' => 'Perfiles',
                'slug' => SectionSlugEnum::PROFILES->value,
            ],
        ];

        foreach ($sections as $section) {
            Section::updateOrCreate(
                ['slug' => $section['slug']],
                $section
            );
        }
    }
}
