<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bet extends model
{
    protected $table = 'bet';
    protected $fillable = [
        'stake_amount',
    ];
}
