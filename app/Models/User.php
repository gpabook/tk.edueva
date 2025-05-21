<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole; // Assuming you have this Enum
use App\Models\BankAccount; // Import BankAccount model
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
        'name',
        'email',
        'password',
        'role',     // User role (e.g., admin, editor) - linked to UserRole Enum
        'status',   // User status (e.g., active, inactive)
        'avatar',
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
        'role'              => UserRole::class, // Casts 'role' to the UserRole Enum
        'status'            => 'integer',  // Casts 'status' to an integer
    ];
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
        return $this->hasOne(BankAccount::class);
    }
    // --- End Bank Account Relationship ---
}
