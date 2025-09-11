<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        // If user is a manager, allow all fields
        if ($user && $user->hasRole('manager')) {
            return [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'sometimes|required|in:pending,completed,canceled',
                'due_date' => 'nullable|date',
                'assigned_to' => 'nullable|exists:users,id',
            ];
        }

        // If user is a regular user, only allow status updates
        return [
            'status' => 'sometimes|required|in:pending,completed,canceled',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The status must be one of: pending, completed, canceled.',
        ];
    }
}
