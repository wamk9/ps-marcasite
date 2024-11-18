<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Configurations extends Model
{
    use HasUlids;

    public $timestamps = false;
    protected $fillable = [
        'key',
        'value'
    ];
}
