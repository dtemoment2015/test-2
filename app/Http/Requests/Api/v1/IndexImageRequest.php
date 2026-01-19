<?php

namespace App\Http\Requests\Api\v1;

use App\DTOs\ImageListFilters;
use Illuminate\Foundation\Http\FormRequest;

class IndexImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function toDTO(): ImageListFilters
    {
        $validated = $this->validated();
        
        return new ImageListFilters(
            page: (int) ($validated['page'] ?? 1),
            perPage: (int) ($validated['per_page'] ?? 15),
        );
    }
}
