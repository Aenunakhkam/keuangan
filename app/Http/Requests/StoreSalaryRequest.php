<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id'            => 'required|exists:teachers,id',
            'base_salary'           => 'required|numeric|min:0',
            'allowance'             => 'required|numeric|min:0',
            'transport_per_day'     => 'required|numeric|min:0',
            'days_present'          => 'required|integer|min:0|max:31',
            'transport_allowance'   => 'required|numeric|min:0',
            'deduction'             => 'required|numeric|min:0',
            'deduction_description' => 'nullable|string|max:255',
            'net_salary'            => 'required|numeric|min:0',
            'month'                 => 'required|integer|min:1|max:12',
            'year'                  => 'required|integer',
            'status'                => 'required|in:pending,paid',
        ];
    }
}
