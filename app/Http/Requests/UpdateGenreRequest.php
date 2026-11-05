<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $genreId = $this->route('genre'); 

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('genres', 'name')->ignore($genreId)],
        ];
    }

    public function messages(): array
    {
        return (new StoreGenreRequest())->messages(); 
    }
}