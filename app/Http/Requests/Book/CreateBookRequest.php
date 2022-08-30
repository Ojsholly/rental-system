<?php

namespace App\Http\Requests\Book;

use App\Rules\ISBN;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', 'unique:books'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', new ISBN(), 'unique:books'],
            'description' => ['required', 'string'],
            'publisher' => ['required', 'string', 'max:255'],
            'published_on' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
