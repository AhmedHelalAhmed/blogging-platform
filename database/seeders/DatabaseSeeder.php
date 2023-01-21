<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $systemUser = User::factory()->create([
            'name' => 'admin',
            'email' => 'no-replay@app.com',
        ]);
        Post::factory(1000)->for($systemUser)->create();
    }
}
