<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents; // ไม่ต้องจับ event ของ model ในการทำ seeder ข้อมูล

    /**
     * จำลองข้อมูล ผู้ใช้งาน ในระบบทั้งหมด แบ่งเป็น 3 role
     *
     * 2 company ,
     * 2 admin ,
     * 10 customer
     *
     */
    public function run(): void
    {
        User::factory()->company()->count(2)->create();
        User::factory()->admin()->count(2)->create();
        User::factory()->customer()->count(10)->create();
    }
}
