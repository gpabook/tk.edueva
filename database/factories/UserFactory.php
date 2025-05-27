<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
    // pick two datetimes so updated_at is always >= created_at
    $created = $this->faker->dateTimeBetween('-1 year', 'now');
    $updated = $this->faker->dateTimeBetween($created, 'now');

        return [
            'student_id'     => $this->faker->unique()->regexify('[0-9]{6}'),     // random 6 หลัก
            'name'              => $this->faker->unique()->name(),   // ← unique name
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('secret'),
            'status'            => 1,
            'remember_token'    => Str::random(10),
          //  'created_at'   => $created,
          //  'updated_at'   => $updated,
        ];
    }


    public function superadmin(): static
    {
        return $this->state([
            'role'  => UserRole::SuperAdmin,
        ]);
    }

    public function admin(): static
    {
        return $this->state([
            'role'  => UserRole::Admin,
        ]);
    }

    public function teacher(): static
    {
        return $this->state([
            'role'  => UserRole::Teacher,
        ]);
    }

    public function student(): static
    {
        return $this->state([
            'role'   => UserRole::Student,
        ]);
    }
}
