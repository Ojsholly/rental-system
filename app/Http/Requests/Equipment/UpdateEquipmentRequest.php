<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'manufacturer' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'serial_number' => ['sometimes', 'nullable', 'string', 'max:255',
                Rule::unique('equipments', 'serial_number')->ignore(request()->route('equipment'), 'uuid')],
            'model_number' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
