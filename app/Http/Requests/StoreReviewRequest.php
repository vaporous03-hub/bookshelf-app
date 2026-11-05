<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating'      => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string', 'max:500'], 
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価（星の数）を選択してください。',
            'rating.min'      => '評価は星1〜星5の間で選択してください。',
            'rating.max'      => '評価は星1〜星5の間で選択してください。',
            'review_text.max' => 'レビュー内容は500文字以内で入力してください。',
        ];
    }
}