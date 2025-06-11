<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = [
        'user_id',
        'prefix_th',
        'name_th',
        'surname_th',
        'phone_1',
        'phone_2',
        'relationship',
    ];

    public function student()
{
    return $this->belongsTo(User::class, 'user_id');
}
///////----- end Guardian ----- Model
}