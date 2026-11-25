<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class UpdateBookRequest extends FormRequest

{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        
        $bookId = $this->route('book'); 

        return [
            'title'       => ['required', 'string', 'max:255'],
            'author'      => ['required', 'string', 'max:255'],
            
            'isbn'        => [
                'required', 
                'string', 
                'regex:/^[0-9]{13}$/', 
                Rule::unique('books', 'isbn')->ignore($bookId),
            ],
            'published_date'=> ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url'   => ['nullable', 'url', 'max:255'],
            
            'genres'   => ['required', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'], 
        ];
    }

    
    public function messages(): array
    {
       return (new StoreBookRequest())->messages();
    }
}