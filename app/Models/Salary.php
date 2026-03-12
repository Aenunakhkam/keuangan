<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = ['teacher_id', 'base_salary', 'allowance', 'transport_per_day', 'days_present', 'transport_allowance', 'deduction', 'deduction_description', 'net_salary', 'month', 'year', 'status', 'paid_at', 'payment_date'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
