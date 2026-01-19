<?php

namespace App\Http\Requests\Api\v1;

use App\DTOs\StoreImageData;
use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:png,jpeg,jpg|max:5120',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'image' => [
                'description' => 'Изображение (PNG, JPEG, JPG, максимум 5MB)',
                'type' => 'file',
            ],
        ];
    }

    public function toDTO(): StoreImageData
    {
        return new StoreImageData(
            image: $this->file('image'),
        );
    }
}
