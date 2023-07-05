<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents; // ไม่ต้องจับ event ของ model ในการทำ seeder ข้อมูล

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // For create super admin
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt(12345),
            'user_role' => 'SuperAdmin'
        ]);

        $this->call([
            UserSeeder::class,
            CategorySeeder::class
        ]);
    }
}
