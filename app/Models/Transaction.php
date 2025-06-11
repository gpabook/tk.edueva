<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'staff_id',
        'printed_at',
        'print_line_no',
        'description'];

    protected $casts = [
            'is_printed' => 'boolean',
        ];

    public function account()
    {
        return $this->belongsTo(BankAccount::class, 'account_id');
    }
    public function staff()
{
    return $this->belongsTo(User::class, 'staff_id');
}

}
