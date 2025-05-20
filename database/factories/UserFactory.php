<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('secret'), // default pwd for seeded users
            'role'              => UserRole::Student,     // default role
            'status'            => 1,
            'remember_token'    => Str::random(10),
        ];
    }

    /** State: SuperAdmin */
    public function superAdmin(): static
    {
        return $this->state([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@example.com',
            'role'     => UserRole::SuperAdmin,
        ]);
    }

    /** State: Admin */
    public function admin(): static
    {
        return $this->state([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
            'role'  => UserRole::Admin,
        ]);
    }

    /** State: Teacher */
    public function teacher(): static
    {
        return $this->state([
            'name'  => 'Teacher User',
            'email' => 'teacher@example.com',
            'role'  => UserRole::Teacher,
        ]);
    }

    /** State: Student */
    /*
    public function student(): static
    {
        return $this->state([
            'name'  => 'Student User',
            'email' => 'student@example.com',
            'role'  => UserRole::Student,
        ]);
    }
*/
    /** State: Student */
public function student(): static
{
    return $this->state([
        'role'  => UserRole::Student,
        'status'=> 1,
    ]);
}
}