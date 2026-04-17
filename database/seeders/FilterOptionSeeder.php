<?php

namespace Database\Seeders;

use App\Models\FilterOption;
use App\Models\Property;
use Illuminate\Database\Seeder;

class FilterOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedPropertyTypes();
        $this->seedPurposes();
        $this->seedMenuCategories();
        $this->seedRange(FilterOption::GROUP_BEDROOMS, 'Quarto', 1, 10);
        $this->seedRange(FilterOption::GROUP_BATHROOMS, 'Banheiro', 1, 10);
        $this->seedPriceRanges();
    }

    private function seedPropertyTypes(): void
    {
        $defaultTypes = collect(['Apartamento', 'Studio', 'Casa', 'Cobertura']);
        $existingTypes = Property::query()->distinct()->pluck('property_type');

        $types = $defaultTypes
            ->merge($existingTypes)
            ->map(fn ($type): string => trim((string) $type))
            ->filter()
            ->unique()
            ->values();

        foreach ($types as $index => $type) {
            FilterOption::updateOrCreate(
                [
                    'group_key' => FilterOption::GROUP_PROPERTY_TYPE,
                    'value' => $type,
                ],
                [
                    'label' => $type,
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedRange(string $groupKey, string $labelPrefix, int $start, int $end): void
    {
        $position = 0;

        for ($value = $start; $value <= $end; $value++) {
            FilterOption::updateOrCreate(
                [
                    'group_key' => $groupKey,
                    'value' => (string) $value,
                ],
                [
                    'label' => $labelPrefix.' '.$value,
                    'sort_order' => $position,
                    'is_active' => true,
                ]
            );

            $position++;
        }
    }

    private function seedPurposes(): void
    {
        $defaults = [
            ['value' => 'venda', 'label' => 'Venda'],
            ['value' => 'aluguel', 'label' => 'Aluguel'],
        ];

        foreach ($defaults as $index => $item) {
            FilterOption::updateOrCreate(
                [
                    'group_key' => FilterOption::GROUP_PURPOSE,
                    'value' => $item['value'],
                ],
                [
                    'label' => $item['label'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedPriceRanges(): void
    {
        $ranges = [
            ['value' => '0|250000', 'label' => 'Até R$ 250.000'],
            ['value' => '250001|350000', 'label' => 'R$ 250.001 até R$ 350.000'],
            ['value' => '350001|500000', 'label' => 'R$ 350.001 até R$ 500.000'],
            ['value' => '500001|', 'label' => 'Acima de R$ 500.000'],
        ];

        foreach ($ranges as $index => $range) {
            FilterOption::updateOrCreate(
                [
                    'group_key' => FilterOption::GROUP_PRICE_RANGE,
                    'value' => $range['value'],
                ],
                [
                    'label' => $range['label'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedMenuCategories(): void
    {
        $items = [
            ['value' => 'lancamento', 'label' => 'Lançamentos'],
            ['value' => 'breve-lancamento', 'label' => 'Breve Lançamento'],
            ['value' => 'imovel-pronto', 'label' => 'Imóvel Pronto'],
            ['value' => 'para-alugar', 'label' => 'Para Alugar'],
        ];

        foreach ($items as $index => $item) {
            FilterOption::updateOrCreate(
                [
                    'group_key' => FilterOption::GROUP_MENU_CATEGORY,
                    'value' => $item['value'],
                ],
                [
                    'label' => $item['label'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }
}
