<?php

namespace App\Http\Requests;

class TaskFilterRequest extends JsonRequest
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
        return [
            'status' => 'sometimes|in:open,in_progress,completed,rejected',
            'assigned_to' => 'sometimes|exists:users,id',
            'created_from' => 'sometimes|date',
            'created_to' => 'sometimes|date|after_or_equal:created_from',
            'due_date_from' => 'sometimes|date',
            'due_date_to' => 'sometimes|date|after_or_equal:due_date_from',
        ];
    }
}
