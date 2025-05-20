<?php
namespace App\Enums;

enum UserRole: int
{
    case SuperAdmin = 1;
    case Admin      = 2;
    case Teacher    = 3;
    case Student    = 4;

    public function label(): string
    {
        return match($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin      => 'Admin',
            self::Teacher    => 'Teacher',
            self::Student    => 'Student',
        };
    }
    /** 
     * Convert a string like "admin" into the matching enum 
     */
    public static function fromName(string $name): self
    {
        return match(strtolower($name)) {
            'superadmin', 'super-admin' => self::SuperAdmin,
            'admin'                     => self::Admin,
            'teacher'                   => self::Teacher,
            'student'                   => self::Student,
            default => throw new \InvalidArgumentException("Unknown role “{$name}”"),
        };
    }
}