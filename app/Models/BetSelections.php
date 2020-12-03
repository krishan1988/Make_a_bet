<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BetSelections extends model
{
    protected $table = 'bet_selections';
    protected $fillable = [
        'amount', 'amount_before',
    ];
}
