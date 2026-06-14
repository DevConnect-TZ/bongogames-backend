<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'username' => ['sometimes', 'string', 'max:50', Rule::unique('users')->ignore($user)],
            'phone' => ['sometimes', 'string', 'max:20'],
            'password' => ['sometimes', 'string', 'min:6'],
            'role' => ['sometimes', 'string', 'in:user,developer,admin'],
            'bio' => ['nullable', 'string', 'max:500'],
        ];
    }
}
