<?php

namespace App\Http\Requests\Api\v1;

use App\DTOs\LoginData;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function toDTO(): LoginData
    {
        $validated = $this->validated();
        
        return new LoginData(
            email: $validated['email'],
            password: $validated['password'],
        );
    }
}
