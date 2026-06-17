<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateDeveloperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $developer = $this->route('developer');

        return [
            'username' => ['sometimes', 'string', 'max:50', Rule::unique('users')->ignore($developer)],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($developer)],
            'phone' => ['sometimes', 'string', 'max:20'],
            'password' => ['sometimes', 'string', 'min:6'],
            'bio' => ['nullable', 'string', 'max:500'],
        ];
    }
}
