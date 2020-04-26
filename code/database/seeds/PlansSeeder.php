<?php

use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Plan::create([
            'title' => 'Freemium (demo)',
            'description' => 'A very limited demo plan for received; app.',
            'storage_limit' => 20480,
            'is_default' => true,
        ]);
    }
}
