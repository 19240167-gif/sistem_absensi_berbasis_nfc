<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        // Demo users hanya di-seed di local/testing environment
        if (app()->environment('local', 'testing')) {
            $this->call([
                DemoUserSeeder::class,
            ]);
        }
    }
}
