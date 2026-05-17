<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = ['teacher_id', 'base_salary', 'allowance', 'days_present', 'deduction', 'deduction_description', 'net_salary', 'month', 'year', 'status', 'paid_at', 'payment_date'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function salaryDeduction()
    {
        return $this->hasOne(SalaryDeduction::class);
    }
}
