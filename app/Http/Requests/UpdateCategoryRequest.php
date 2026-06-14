<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => ['sometimes', 'string', 'max:50', Rule::unique('categories')->ignore($category)],
            'slug' => ['sometimes', 'string', 'max:50', Rule::unique('categories')->ignore($category)],
        ];
    }
}
