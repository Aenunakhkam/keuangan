<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
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
            'nip' => 'nullable|string|max:20|unique:teachers,nip',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'joined_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'position_ids' => 'nullable|array',
            'position_ids.*' => 'exists:positions,id',
            'teaching_hours' => 'nullable|integer|min:0',
        ];
    }
}
