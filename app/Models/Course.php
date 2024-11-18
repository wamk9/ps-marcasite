<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasUlids;

    public $timestamps = false;
    protected $fillable = [
        'name',
        'category_id',
        'value',
        'vacations',
        'registration_at',
        'registration_until',
        'description',
        'active'
    ];
}
