<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'umk_reference',
        'health_school_percent',
        'health_employee_percent',
        'naker_school_percent',
        'naker_employee_percent',
    ];
}
