<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
            'nis' => 'required|string|max:20|unique:students,nis,' . $this->student->id,
            'class_room_id' => 'required|exists:class_rooms,id',
            'gender' => 'required|in:L,P',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:255',
        ];
    }
}
