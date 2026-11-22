<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    const  UPDATED_AT = NULL;
    protected $fillable = [
        'type',
        'amount',
        'before_balance',
        'after_balance',
        'idempotency_key'
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y/m/d H:i:s'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
