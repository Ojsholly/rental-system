<?php

namespace App\Http\Requests\Book;

use App\Rules\ISBN;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'title' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('books')->ignore(request()->route('book'), 'uuid')],
            'author' => ['sometimes', 'nullable', 'string', 'max:255'],
            'isbn' => ['sometimes', 'nullable', new ISBN(), Rule::unique('books')->ignore(request()->route('book'), 'uuid')],
            'description' => ['sometimes', 'nullable', 'string'],
            'publisher' => ['sometimes', 'nullable', 'string', 'max:255'],
            'published_on' => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
