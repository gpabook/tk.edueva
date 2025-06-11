<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole; // Assuming you have this Enum
use App\Models\BankAccount; // Import BankAccount model
use App\Models\ClassLevel;
use App\Models\Room;
use App\Models\Guardian;
use Ramsey\Uuid\Guid\Guid;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 *
 * @property BankAccount $bankAccount The user's bank account.
 * @property-read string $avatar_url The URL for the user's avatar thumbnail.
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Spatie\MediaLibrary\InteractsWithMedia
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';

    //protected $dateFormat = 'd-m-Y H:i:s'; // Y-m-d\TH:i:s
    protected $dateFormat = 'Y-m-d H:i:s';
    /**
     * The attributes that are mass assignable.
     * Allows these fields to be filled using User::create() or $user->fill().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'national_id',
        'room_id',
        'prefix_th',
        'name',

        'name_th',
        'surname_th',
        'name_en',
        'surname_en',
        'birthday',
        'gender',
        'phone',
        'line_id',
        'address',

        'email',
        'password',
        'role',     // User role (e.g., admin, editor) - linked to UserRole Enum
        'status',   // User status (e.g., active, inactive)
        'avatar',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Prevents these fields from being included in JSON responses or array conversions.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',         // Always hide passwords
        'remember_token',   // Security token for "remember me" functionality
    ];

    protected $appends = ['avatar_url'];
    /**
     * The attributes that should be cast to native types.
     * Ensures data consistency when retrieving attributes.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
        'email_verified_at' => 'datetime', // Casts to a Carbon date/time object
        'password'          => 'hashed',   // Automatically hashes passwords when set (Laravel 10+)
        'role'              => \App\Enums\UserRole::class,
        'status'            => 'integer',  // Casts 'status' to an integer
    ];
}

    protected static function booted()
{
  static::saved(function (User $user) {
    // เซ็ต Role ใน Spatie ให้ตรงกับ enum ที่เซ็ตมา
    $user->syncRoles([$user->role->name]);
  });
}

        // Optionally, an accessor for full URL:

    public function getAvatarUrlAttribute()
            {
                return $this->avatar
                    ? asset('storage/'.$this->avatar)
                    : asset('images/default-avatar.png');
            }

    // --- Bank Account Relationship ---
    /**
     * Get the bank account associated with the user.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class, 'student_id', 'student_id');
    }
    // --- End Bank Account Relationship ---

    // ถ้าต้องการดึงระดับชั้นจาก user เลย
    public function classLevel()
    {
        return $this->hasOneThrough(
            ClassLevel::class,
            Room::class,
            'id',            // rooms.id
            'id',            // class_levels.id
            'room_id',       // users.room_id
            'class_level_id' // rooms.class_level_id
        );
    }


// นักเรียนอยู่ในห้องไหน
public function room()
{
    return $this->belongsTo(Room::class);
}

// ถ้าเป็นครู ก็เอาห้องที่ตัวเองเป็นที่ปรึกษา
public function advisingRooms()
{
    return $this->belongsToMany(
        Room::class,
        'room_advisor',
        'user_id',
        'room_id'
    )->withTimestamps();
}
// ความสัมพันธ์แบบ belongsToMany
public function enrolledRooms()
{
    return $this->belongsToMany(Room::class, 'room_student')
        ->withPivot(['assigned_at', 'left_at'])
        ->with('classLevel');
}

public function currentRoom()
{
    return $this->belongsToMany(Room::class, 'room_student')
        ->wherePivotNull('left_at')
        ->withPivot(['assigned_at', 'left_at'])
        ->with('classLevel')
        ->orderByDesc('assigned_at')
        ->limit(1);
}

public function guardians()
{
    return $this->hasMany(Guardian::class);
}

// ถ้า 1 นักเรียนมี 1 ผู้ปกครอง (One-to-One)
public function guardian()
{
    return $this->hasOne(Guardian::class);
}


// --- end User Model ------
}
