<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telegram extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'telegram_id',
        'username',
        'phone',
        'rule',
        'verified',
        'verified_at'
    ];
}
