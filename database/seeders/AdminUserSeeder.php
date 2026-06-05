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
        $email    = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        // Never seed the admin with a hardcoded credential: require both values
        // to be provided per environment via .env, otherwise skip seeding.
        if (blank($email) || blank($password)) {
            $this->command?->warn('Skipping AdminUserSeeder: set ADMIN_EMAIL and ADMIN_PASSWORD in your .env file.');

            return;
        }

        $profile = Profile::updateOrCreate(
            ['name' => 'Administrador'],
            [
                'sections' => SectionSlugEnum::values(),
            ]
        );

        User::firstOrCreate(
            ['username' => $email],
            [
                'name'          => 'Administrador TAP',
                'password'      => Hash::make($password),
                'profile_photo' => null,
                'phone'         => null,
                'profile_ids'   => [$profile->_id],
            ]
        );
    }
}
