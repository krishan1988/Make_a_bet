<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends model
{
    protected $table = 'balance_transaction';
    protected $fillable = [
        'balance',
    ];
}
