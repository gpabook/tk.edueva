<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application’s database.
     */
    public function run(): void
    {
        // 1) สร้าง Roles & Permissions ก่อน
     //   $this->call([
     //       RolePermissionSeeder::class,
     //   ]);

        // ล้าง cache ของ Spatie Permission (ถ้าต้องการทันที)
        // \Artisan::call('permission:cache-reset');

        // 2) สร้าง Users ตามสัดส่วน
        // 1 คน: SuperAdmin
      //  User::factory()
      //      ->superadmin()
      //      ->create();

        // 5 คน: Admin
      //  User::factory()
      //      ->admin()
      //      ->count(10)
      //      ->create();

        // 100 คน: Teacher
       // User::factory()
       //     ->teacher()
       //     ->count(90)
      //      ->create();

        // 1,500 คน: Student
        //User::factory()
        //    ->student()
        //    ->count(200)
        //    ->create();

        // 3) สร้าง BankAccount ให้กับ User ทุกคน
        $this->call([
            BankAccountSeeder::class,
        ]);

        // (ถ้ามี Seeder อื่นๆ ให้เพิ่มไว้ในลำดับที่เหมาะสมได้)
    }
}