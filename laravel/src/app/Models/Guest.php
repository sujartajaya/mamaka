<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\Radacct;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'country_id',
        'username',
        'password',
        'mac_add',
        'os_client',
        'browser_client'
    ];

    
    public function radacct() 
    {
        return $this->hasMany(Radacct::class,"username");
    }

    public function country() 
    {
        return $this->belongsTo(Country::class);
    }
    
}
