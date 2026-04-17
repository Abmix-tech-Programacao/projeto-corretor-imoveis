<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterOption extends Model
{
    use HasFactory;

    public const GROUP_PROPERTY_TYPE = 'property_type';
    public const GROUP_PURPOSE = 'purpose';
    public const GROUP_MENU_CATEGORY = 'menu_category';
    public const GROUP_BEDROOMS = 'bedrooms';
    public const GROUP_BATHROOMS = 'bathrooms';
    public const GROUP_PRICE_RANGE = 'price_range';

    protected $fillable = [
        'group_key',
        'value',
        'label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * @return array<string, string>
     */
    public static function groups(): array
    {
        return [
            self::GROUP_PROPERTY_TYPE => 'Tipo de Imóvel',
            self::GROUP_PURPOSE => 'Finalidade',
            self::GROUP_MENU_CATEGORY => 'Menu do Header',
            self::GROUP_BEDROOMS => 'Quartos',
            self::GROUP_BATHROOMS => 'Banheiros',
            self::GROUP_PRICE_RANGE => 'Faixa de Preço',
        ];
    }

    public function scopeGroup(Builder $query, string $groupKey): Builder
    {
        return $query->where('group_key', $groupKey);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
