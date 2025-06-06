<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model

{
    use HasFactory;

    protected $fillable = ['student_id','balance'];

    public function user()
    {
        //return $this->belongsTo(User::class);
        return $this->belongsTo(User::class, 'student_id', 'student_id');

    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }
}