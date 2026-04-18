<?php

namespace App\Http\Requests;

use App\Models\FilterOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        $property = $this->route('property');

        return [
            'title' => ['required', 'string', 'max:180'],
            'code' => ['required', 'string', 'max:50', Rule::unique('properties', 'code')->ignore($property?->id)],
            'property_type' => [
                'required',
                'string',
                'max:80',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $allowedFallbacks = ['Apartamento', 'Studio', 'Casa', 'Cobertura'];

                    if ($this->isValidFilterOption(FilterOption::GROUP_PROPERTY_TYPE, $value, $allowedFallbacks)) {
                        return;
                    }

                    $fail('Selecione um tipo de imóvel válido.');
                },
            ],
            'purpose' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $allowedFallbacks = ['venda', 'aluguel'];

                    if ($this->isValidFilterOption(FilterOption::GROUP_PURPOSE, $value, $allowedFallbacks)) {
                        return;
                    }

                    $fail('Selecione uma finalidade válida.');
                },
            ],
            'menu_category' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $allowedFallbacks = ['lancamento', 'breve-lancamento', 'imovel-pronto', 'para-alugar'];

                    if ($value === null || trim((string) $value) === '') {
                        return;
                    }

                    if ($this->isValidFilterOption(FilterOption::GROUP_MENU_CATEGORY, $value, $allowedFallbacks)) {
                        return;
                    }

                    $fail('Selecione uma categoria de menu válida.');
                },
            ],
            'city_location_id' => ['required', 'integer', 'exists:locations,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['required', 'string', 'size:2'],
            'neighborhood' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:190'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_on_request' => ['nullable', 'boolean'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms' => ['required', 'integer', 'min:0', 'max:20'],
            'parking_spaces' => ['required', 'integer', 'min:0', 'max:20'],
            'area' => ['nullable', 'integer', 'min:10', 'max:2000'],
            'description' => ['required', 'string', 'min:20'],
            'features_text' => ['nullable', 'string', 'max:2000'],
            'featured_image_upload' => ['nullable', 'image', 'max:4096'],
            'featured_image_url' => ['nullable', 'url', 'max:500'],
            'gallery_uploads' => ['nullable', 'array'],
            'gallery_uploads.*' => ['image', 'max:4096'],
            'gallery_urls' => ['nullable', 'string', 'max:4000'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:property_images,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'city_location_id.required' => 'Selecione uma cidade.',
            'city_location_id.exists' => 'Selecione uma cidade válida.',
            'location_id.exists' => 'Selecione um bairro/região válido.',
            'description.min' => 'A descrição deve ter pelo menos 20 caracteres.',
        ];
    }

    /**
     * @param array<int, string> $fallbackValues
     */
    private function isValidFilterOption(string $groupKey, mixed $value, array $fallbackValues): bool
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return false;
        }

        $exists = FilterOption::query()
            ->where('group_key', $groupKey)
            ->where('is_active', true)
            ->where('value', $normalized)
            ->exists();

        return $exists || in_array($normalized, $fallbackValues, true);
    }
}
