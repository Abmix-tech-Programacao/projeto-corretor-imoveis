<?php

namespace App\Support;

use App\Models\FilterOption;
use Illuminate\Support\Collection;

class FilterCatalog
{
    /**
     * @return Collection<int, FilterOption>
     */
    public static function active(string $groupKey): Collection
    {
        return FilterOption::query()
            ->group($groupKey)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    /**
     * @return Collection<int, array{value: string, label: string}>
     */
    public static function propertyTypes(): Collection
    {
        return self::active(FilterOption::GROUP_PROPERTY_TYPE)
            ->map(fn (FilterOption $option): array => [
                'value' => $option->value,
                'label' => $option->label,
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{value: string, label: string}>
     */
    public static function purposes(): Collection
    {
        return self::active(FilterOption::GROUP_PURPOSE)
            ->map(fn (FilterOption $option): array => [
                'value' => $option->value,
                'label' => $option->label,
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{value: string, label: string}>
     */
    public static function menuCategories(): Collection
    {
        return self::active(FilterOption::GROUP_MENU_CATEGORY)
            ->map(fn (FilterOption $option): array => [
                'value' => $option->value,
                'label' => $option->label,
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{value: string, label: string}>
     */
    public static function priceRanges(): Collection
    {
        return self::active(FilterOption::GROUP_PRICE_RANGE)
            ->map(fn (FilterOption $option): array => [
                'value' => $option->value,
                'label' => $option->label,
            ])
            ->values();
    }

    /**
     * @return array<int, int>
     */
    public static function numericOptions(string $groupKey): array
    {
        $values = self::active($groupKey)
            ->pluck('value')
            ->map(fn (string $value): int => (int) $value)
            ->filter(fn (int $value): bool => $value > 0)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $values;
    }

    /**
     * @return array{min: float|null, max: float|null}
     */
    public static function parsePriceRange(?string $value): array
    {
        $raw = trim((string) $value);
        if ($raw === '' || ! str_contains($raw, '|')) {
            return ['min' => null, 'max' => null];
        }

        [$minRaw, $maxRaw] = explode('|', $raw, 2);

        $min = is_numeric(trim($minRaw)) ? (float) trim($minRaw) : null;
        $max = is_numeric(trim($maxRaw)) ? (float) trim($maxRaw) : null;

        return ['min' => $min, 'max' => $max];
    }
}
