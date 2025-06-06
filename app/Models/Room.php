<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_level_id',
        'name_room_en',
        'name_room_th',
        'advisor_id',
    ];

    /**
     * The class level that this room belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    /**
     * The students assigned to this room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'room_id');
    }

    /**
     * The advisor for this room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    /**
     * The advisors for this room (many-to-many pivot).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function advisors(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'room_advisor',
            'room_id',
            'user_id'
        )->withTimestamps();
    }

    public function enrolledStudents()
    {
        return $this->belongsToMany(
            User::class,
            'room_student',
            'room_id',
            'user_id'
        )->withPivot(['assigned_at', 'left_at'])->withTimestamps();
    }

    // ---------- end Room Model -------
}
