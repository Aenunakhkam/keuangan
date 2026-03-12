<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $fillable = ['name', 'default_amount', 'period'];

    public function bills()
    {
        return $this->hasMany(StudentBill::class);
    }
}
