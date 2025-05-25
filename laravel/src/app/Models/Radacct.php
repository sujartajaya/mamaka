<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Guest;

class Radacct extends Model
{
    protected $table = 'radacct';

    public $timestamps = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function guest() 
    {
        return $this->belongsTo(Guest::class,'username');
    }
}
