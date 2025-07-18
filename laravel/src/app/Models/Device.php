<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public $incrementing = false;
    
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string'
    ];
    protected $fillable = [
        'guest_id',
        'mac_add',
        'os_client',
        'browser_client'
    ];

}
