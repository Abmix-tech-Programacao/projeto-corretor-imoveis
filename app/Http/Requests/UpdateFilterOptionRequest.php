<?php

namespace App\Http\Requests;

use App\Models\FilterOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFilterOptionRequest extends FormRequest
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
            'group_key' => ['required', Rule::in(array_keys(FilterOption::groups()))],
            'label' => ['required', 'string', 'max:120'],
            'value' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $group = (string) $this->input('group_key');
            $value = trim((string) ($this->input('value') ?: $this->input('label')));

            if (in_array($group, [FilterOption::GROUP_BEDROOMS, FilterOption::GROUP_BATHROOMS], true)) {
                if (! ctype_digit($value) || (int) $value <= 0) {
                    $validator->errors()->add('value', 'Para este grupo, o valor deve ser numerico e maior que zero.');
                }
            }

            if ($group === FilterOption::GROUP_PRICE_RANGE) {
                if (! preg_match('/^\d*\|\d*$/', $value)) {
                    $validator->errors()->add('value', 'Faixa de preço deve seguir o formato min|max.');
                    return;
                }

                [$min, $max] = explode('|', $value, 2);
                if ($min === '' && $max === '') {
                    $validator->errors()->add('value', 'Informe pelo menos um limite na faixa de preço.');
                }
            }
        });
    }
}
