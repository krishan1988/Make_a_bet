<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends model
{
    protected $table = 'player';
    protected $fillable = [
        'balance',
    ];
}
