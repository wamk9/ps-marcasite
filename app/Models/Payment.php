<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUlids;

    protected $fillable = [
        'mp_id',
        'user_id',
        'status',
        'course_id',
        'value_payed',
        'status_message'
    ];
}
