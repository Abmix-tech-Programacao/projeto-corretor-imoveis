<?php

namespace App\Http\Requests;

use App\Models\FilterOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:180'],
            'code' => ['required', 'string', 'max:50', 'unique:properties,code'],
            'property_type' => [
                'required',
                'string',
                'max:80',
                Rule::exists('filter_options', 'value')->where(function ($query): void {
                    $query
                        ->where('group_key', FilterOption::GROUP_PROPERTY_TYPE)
                        ->where('is_active', true);
                }),
            ],
            'purpose' => [
                'required',
                'string',
                Rule::exists('filter_options', 'value')->where(function ($query): void {
                    $query
                        ->where('group_key', FilterOption::GROUP_PURPOSE)
                        ->where('is_active', true);
                }),
            ],
            'menu_category' => [
                'nullable',
                'string',
                Rule::exists('filter_options', 'value')->where(function ($query): void {
                    $query
                        ->where('group_key', FilterOption::GROUP_MENU_CATEGORY)
                        ->where('is_active', true);
                }),
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
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
