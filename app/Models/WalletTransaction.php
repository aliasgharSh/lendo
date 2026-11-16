<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'before_balance',
        'after_balance',
    ];
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
