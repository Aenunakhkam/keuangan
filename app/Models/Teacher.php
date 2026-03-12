<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'position_id', 'teaching_hours', 'name', 'nip', 'phone', 'address', 'joined_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }
}
