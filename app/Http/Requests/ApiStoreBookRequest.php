<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class ApiStoreBookRequest extends FormRequest

{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        
        

        return [
            'title'       => ['required', 'string', 'max:255'],
            'author'      => ['required', 'string', 'max:255'],
            
            'isbn'        => [
                'required', 
                'string', 
                'regex:/^[0-9]{13}$/', 
                Rule::unique('books', 'isbn'),
            ],
            'published_at'=> ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url'   => ['nullable', 'url', 'max:255'],
            
            'genre_ids'   => ['required', 'array'],
            'genre_ids.*' => ['integer', 'exists:genres,id'], 
        ];
    }

    
    public function messages(): array
    {
        return [
            'title.required'        => '書籍のタイトルを入力してください。',
            'author.required'       => '著者を入力してください。',
            'isbn.required'         => 'ISBNコードを入力してください。',
            'isbn.regex'            => 'ISBNはハイフンなしの13桁の数字で入力してください。',
            'isbn.unique'           => 'このISBNコードは既に登録されています。',
            'published_at.required' => '出版日を入力してください。',
            'published_at.date'     => '正しい日付の形式で入力してください。',
            'description.max'       => '説明は1000文字以内で入力してください。',
            'image_url.url'         => '正しいURLの形式で入力してください。',
            'genre_ids.required'    => 'ジャンルは最低1つ選択してください。',
            'genre_ids.*.exists'    => '選択されたジャンルは存在しません。',
        ];
    }
}