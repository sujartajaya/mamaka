<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDevice extends Model
{
    protected $fillable = [
        'username',
        'mac_add',
        'os_client',
        'browser_client',
        'device_client',
        'brand_client',
        'model_client',
        "device_type"
    ];
}
