<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Truncate tables before seeding
        DB::table('users')->truncate();

        // Seed the tables
        \App\Models\User::factory(50)->create();
    }
}
