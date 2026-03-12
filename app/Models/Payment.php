<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['student_bill_id', 'amount', 'payment_date', 'payment_method', 'note'];

    public function studentBill()
    {
        return $this->belongsTo(StudentBill::class);
    }
}
