<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'price' => ['sometimes', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'trailer_url' => ['nullable', 'url', 'max:500'],
            'trailer_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:102400'],
            'screenshots' => ['nullable', 'array', 'max:10'],
            'screenshots.*' => ['image', 'max:5120'],
            'version' => ['sometimes', 'string', 'max:50'],
            'changelog' => ['nullable', 'string'],
            'download_link' => ['nullable', 'url', 'max:500'],
        ];
    }
}
