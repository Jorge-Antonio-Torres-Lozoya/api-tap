<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CounterSeeder::class,
            SectionSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
