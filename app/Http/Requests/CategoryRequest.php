<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('manage-content') ?? true; }

    public function rules(): array
    {
        $id = $this->route('category')?->id ?? null;
        return [
            'name'        => ['required','string','max:120'],
            'slug'        => ['nullable','string','max:140',"unique:categories,slug,{$id}"],
            'description' => ['nullable','string'],
            'is_active'   => ['nullable','boolean'],
        ];
    }

    /** Cast propre du checkbox */
    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => (bool) $this->boolean('is_active')]);
    }
}
