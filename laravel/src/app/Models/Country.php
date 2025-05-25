<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Guest;

class Country extends Model
{
    public function guest() 
    {
        return $this->hasMany(Guest::class);
    }
}
