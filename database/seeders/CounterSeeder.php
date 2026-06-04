<?php

namespace Database\Seeders;

use App\Models\Counter;
use Illuminate\Database\Seeder;

class CounterSeeder extends Seeder
{
    public function run(): void
    {
        $models = ['PRD', 'USR', 'PRF'];

        foreach ($models as $model) {
            Counter::firstOrCreate(
                ['model' => $model],
                ['seq'   => 0]
            );
        }
    }
}
