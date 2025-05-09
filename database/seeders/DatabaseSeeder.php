<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Dobriyan',
            'email' => 'asdbsd@dev.com',
            'password' => bcrypt('password')
        ]);
        User::factory()->create([
            'name' => 'Sad',
            'email' => 'sad@dev.com',
            'password' => bcrypt('password')
        ]);
        User::factory()->create([
            'name' => 'Happy',
            'email' => 'happy@dev.com',
            'password' => bcrypt('password')
        ]);
        User::factory()->create([
            'name' => 'John',
            'email' => 'john@dev.com',
            'password' => bcrypt('password')
        ]);
    }
}
