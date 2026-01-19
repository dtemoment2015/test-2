<?php

namespace App\Http\Requests\Api\v1;

use App\DTOs\RegisterData;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8', // можно было не добавлять, но для того чтобы в документации Scribe было видно что это поле обязательно
        ];
    }

    public function toDTO(): RegisterData
    {
        $validated = $this->validated();
        
        return new RegisterData(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
        );
    }
}
