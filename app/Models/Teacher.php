<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id', 
        'name', 
        'nipty', 
        'nipy', 
        'birth_place', 
        'birth_date', 
        'education', 
        'major',
        'unit', 
        'service_years', 
        'service_months', 
        'grade', 
        'basic_salary', 
        'teaching_hours',
        'discipline_percentage',
        'other_allowance', 
        'joined_date',
        'gender',
        'bpjs_category_id'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'joined_date' => 'date',
        'service_years' => 'integer',
        'service_months' => 'integer',
        'basic_salary' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'discipline_percentage' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class);
    }

    public function bpjsCategory()
    {
        return $this->belongsTo(BpjsCategory::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }
}
