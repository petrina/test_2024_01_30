<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => 'required|array',
            'translations.*.language' => 'required|string',
            'translations.*.title' => 'required|string',
            'translations.*.description' => 'required|string',
            'translations.*.content' => 'required|string',
            'tag' => 'required|string',
        ];
    }
}
