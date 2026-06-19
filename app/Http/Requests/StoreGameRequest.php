<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'cover' => ['nullable', 'image', 'max:10240'],
            'thumbnail' => ['nullable', 'image', 'max:10240'],
            'trailer_url' => ['nullable', 'url', 'max:500'],
            'screenshots' => ['nullable', 'array', 'max:10'],
            'screenshots.*' => ['image', 'max:10240'],
            'version' => ['required', 'string', 'max:50'],
            'changelog' => ['nullable', 'string'],
            'download_link' => ['nullable', 'url', 'max:500'],
        ];
    }
}
