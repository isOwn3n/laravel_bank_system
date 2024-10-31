<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'dest_card_id',
        'user_id',
        'amount',
        'fee',
        'is_deposit',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(Account::class);
    }

    public function destCard()
    {
        return $$this->belongsTo(Account::class);
    }
}
