<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'has_health',
        'has_naker',
        'is_full_school',
    ];
}
