<?php

namespace App\Models;

use App\Support\FilterCatalog;
use App\Support\LocationHierarchy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'code',
        'property_type',
        'purpose',
        'menu_category',
        'broker_user_id',
        'city',
        'state',
        'location_id',
        'neighborhood',
        'address',
        'price',
        'bedrooms',
        'bathrooms',
        'parking_spaces',
        'area',
        'is_featured',
        'is_published',
        'description',
        'features',
        'featured_image',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'broker_user_id' => 'integer',
        'location_id' => 'integer',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('position');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'broker_user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $locationSlug = LocationHierarchy::selectedSlug($filters);

        return $query
            ->when($filters['city'] ?? null, fn (Builder $builder, string $city) => $builder->where('city', $city))
            ->when(
                $filters['neighborhood'] ?? null,
                fn (Builder $builder, string $neighborhood) => $builder->where('neighborhood', $neighborhood)
            )
            ->when($locationSlug, function (Builder $builder, string $slug): void {
                $targets = LocationHierarchy::resolveQueryTargets($slug);
                $selectedDepth = (int) ($targets['selected_depth'] ?? 0);
                $locationIds = $targets['location_ids'] ?? [];
                $cities = $targets['cities'] ?? [];
                $neighborhoods = $targets['neighborhoods'] ?? [];

                if ($locationIds !== []) {
                    $builder->where(function (Builder $inner) use (
                        $locationIds,
                        $selectedDepth,
                        $cities,
                        $neighborhoods
                    ): void {
                        $inner->whereIn('location_id', $locationIds);

                        // Fallback para imóveis legados sem location_id.
                        $inner->orWhere(function (Builder $legacy) use ($selectedDepth, $cities, $neighborhoods): void {
                            $legacy->whereNull('location_id');

                            if ($selectedDepth === 0 && $cities !== []) {
                                $legacy->whereIn('city', $cities);
                                return;
                            }

                            if ($neighborhoods !== []) {
                                $legacy->whereIn('neighborhood', $neighborhoods);

                                if ($cities !== []) {
                                    $legacy->whereIn('city', $cities);
                                }

                                return;
                            }

                            if ($cities !== []) {
                                $legacy->whereIn('city', $cities);
                            }
                        });
                    });

                    return;
                }

                if ($selectedDepth === 0 && $cities !== []) {
                    $builder->whereIn('city', $cities);
                    return;
                }

                if ($neighborhoods !== []) {
                    $builder->whereIn('neighborhood', $neighborhoods);

                    if ($cities !== []) {
                        $builder->whereIn('city', $cities);
                    }

                    return;
                }

                if ($cities !== []) {
                    $builder->whereIn('city', $cities);
                }
            })
            ->when(
                $filters['property_type'] ?? null,
                fn (Builder $builder, string $propertyType) => $builder->where('property_type', $propertyType)
            )
            ->when(
                $filters['purpose'] ?? null,
                fn (Builder $builder, string $purpose) => $builder->where('purpose', $purpose)
            )
            ->when(
                $filters['menu_category'] ?? null,
                fn (Builder $builder, string $menuCategory) => $builder->where('menu_category', $menuCategory)
            )
            ->when(
                $filters['price_range'] ?? null,
                function (Builder $builder, string $priceRange): void {
                    $range = FilterCatalog::parsePriceRange($priceRange);

                    if ($range['min'] !== null) {
                        $builder->where('price', '>=', $range['min']);
                    }

                    if ($range['max'] !== null) {
                        $builder->where('price', '<=', $range['max']);
                    }
                }
            )
            ->when(
                $filters['bedrooms'] ?? null,
                fn (Builder $builder, int $bedrooms) => $builder->where('bedrooms', '>=', $bedrooms)
            )
            ->when(
                $filters['bathrooms'] ?? null,
                fn (Builder $builder, int $bathrooms) => $builder->where('bathrooms', '>=', $bathrooms)
            )
            ->when(
                $filters['min_price'] ?? null,
                fn (Builder $builder, float $minPrice) => $builder->where('price', '>=', $minPrice)
            )
            ->when(
                $filters['max_price'] ?? null,
                fn (Builder $builder, float $maxPrice) => $builder->where('price', '<=', $maxPrice)
            )
            ->when(
                $filters['search'] ?? null,
                function (Builder $builder, string $search): void {
                    $builder->where(function (Builder $inner) use ($search): void {
                        $inner
                            ->where('title', 'like', "%{$search}%")
                            ->orWhere('neighborhood', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }
            );
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->price
                ? 'R$ '.number_format((float) $this->price, 2, ',', '.')
                : 'Preço sob consulta'
        );
    }

    protected function featureList(): Attribute
    {
        return Attribute::make(
            get: fn (): array => $this->features
                ? array_values(array_filter(array_map('trim', explode('|', $this->features))))
                : []
        );
    }

    protected function coverImage(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->featured_image
                ?: ($this->relationLoaded('images')
                    ? ($this->images->first()?->path ?: $this->placeholderImage())
                    : ($this->images()->value('path') ?: $this->placeholderImage()))
        );
    }

    private function placeholderImage(): string
    {
        return 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80';
    }
}
