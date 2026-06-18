<?php

namespace App\Http\Requests\Producer;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price'       => ['required', 'numeric', 'min:0.01', 'decimal:0,2'],
            'unit'        => ['required', 'string', 'max:50'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_available'=> ['nullable', 'boolean'],
            'photo'       => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $this->merge(['price' => str_replace(',', '.', $this->price)]);
        }

        $this->merge(['is_available' => $this->boolean('is_available')]);
    }
}
