<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'nipty' => 'nullable|string|max:50|unique:teachers,nipty,' . $this->teacher->id,
            'nipy' => 'nullable|string|max:50|unique:teachers,nipy,' . $this->teacher->id,
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'joined_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'position_ids' => 'nullable|array',
            'position_ids.*' => 'exists:positions,id',
            'education' => 'nullable|string|max:100',
            'major' => 'nullable|string|max:150',
            'unit' => 'nullable|string|max:100',
            'service_years' => 'nullable|integer|min:0',
            'service_months' => 'nullable|integer|min:0|max:11',
            'grade' => 'nullable|string|max:50',
            'basic_salary' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
        ];
    }
}
