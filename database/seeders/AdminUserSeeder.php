<?php

namespace Database\Seeders;

use App\Enums\SectionSlugEnum;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $profile = Profile::updateOrCreate(
            ['name' => 'Administrador'],
            [
                'sections' => SectionSlugEnum::values(),
            ]
        );

        User::firstOrCreate(
            ['username' => 'admin@tapterminal.com'],
            [
                'name'          => 'Administrador TAP',
                'password'      => Hash::make('Admin1234!'),
                'profile_photo' => null,
                'phone'         => null,
                'profile_ids'   => [$profile->_id],
            ]
        );
    }
}
