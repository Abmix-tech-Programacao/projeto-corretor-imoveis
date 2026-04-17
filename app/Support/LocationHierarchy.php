<?php

namespace App\Support;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class LocationHierarchy
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function tree(): array
    {
        return self::buildTree(self::baseRecords());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function adminRows(): array
    {
        $records = Location::query()
            ->with('parent:id,name')
            ->withCount(['children', 'properties'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'slug', 'depth', 'sort_order']);

        $tree = self::buildTree($records);

        $indexed = [];
        foreach ($records as $record) {
            $indexed[$record->id] = $record;
        }

        $rows = [];
        self::flattenForAdmin($tree, $rows, $indexed);

        return $rows;
    }

    /**
     * @return array<int, array{id: int, label: string, depth: int, city: string, neighborhood: string}>
     */
    public static function optionsForSelect(?int $excludeId = null): array
    {
        $records = self::baseRecords();
        $tree = self::buildTree($records);
        $flat = self::flatById($tree);
        $excluded = $excludeId ? self::collectDescendantIds($excludeId, $flat) : [];

        $options = [];
        self::flattenForSelect($tree, $options, $excluded, null);

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return config('location_hierarchy.labels', [
            'location' => 'Localização',
            'child_location' => 'Localização de todas as crianças',
            'grandchild_location' => 'Localização de todos os netos',
            'great_grandchild_location' => 'Localização de todos os bisnetos',
        ]);
    }

    /**
     * @param array<string, mixed> $filters
     */
    public static function selectedSlug(array $filters): ?string
    {
        foreach (['great_grandchild_location', 'grandchild_location', 'child_location', 'location'] as $key) {
            $slug = trim((string) ($filters[$key] ?? ''));
            if ($slug !== '') {
                return $slug;
            }
        }

        return null;
    }

    /**
     * @return array{location_ids: array<int, int>, cities: array<int, string>, neighborhoods: array<int, string>, selected_depth: int}
     */
    public static function resolveQueryTargets(string $slug): array
    {
        $tree = self::tree();
        $flat = self::flatBySlug($tree);

        if (! isset($flat[$slug])) {
            return [
                'location_ids' => [],
                'cities' => [],
                'neighborhoods' => [],
                'selected_depth' => 0,
            ];
        }

        $descendantSlugs = self::collectDescendantSlugs($slug, $flat);
        $cities = [];
        $neighborhoods = [];
        $locationIds = [];

        foreach ($descendantSlugs as $descendantSlug) {
            $node = $flat[$descendantSlug];
            $root = self::rootNode($descendantSlug, $flat);

            $locationIds[] = (int) $node['id'];

            if ($root !== null) {
                $cities[] = (string) $root['name'];
            }

            if ((int) $node['depth'] > 0) {
                $neighborhoods[] = (string) $node['name'];
            }
        }

        return [
            'location_ids' => array_values(array_unique($locationIds)),
            'cities' => array_values(array_unique(self::withAsciiVariants($cities))),
            'neighborhoods' => array_values(array_unique(self::withAsciiVariants($neighborhoods))),
            'selected_depth' => (int) $flat[$slug]['depth'],
        ];
    }

    /**
     * @return array{city: string|null, neighborhood: string|null}
     */
    public static function resolvePropertyContext(?int $locationId): array
    {
        if (! $locationId) {
            return ['city' => null, 'neighborhood' => null];
        }

        $location = Location::query()->find($locationId);
        if (! $location) {
            return ['city' => null, 'neighborhood' => null];
        }

        $city = $location->name;
        $parent = $location->parent;

        while ($parent) {
            $city = $parent->name;
            $parent = $parent->parent;
        }

        return [
            'city' => $city,
            'neighborhood' => $location->name,
        ];
    }

    public static function canUseParent(int $locationId, ?int $candidateParentId): bool
    {
        if (! $candidateParentId) {
            return true;
        }

        if ($candidateParentId === $locationId) {
            return false;
        }

        $tree = self::tree();
        $flat = self::flatById($tree);
        $descendants = self::collectDescendantIds($locationId, $flat);

        return ! in_array($candidateParentId, $descendants, true);
    }

    /**
     * @return Collection<int, Location>
     */
    private static function baseRecords(): Collection
    {
        return Location::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'slug', 'depth', 'sort_order']);
    }

    /**
     * @param Collection<int, Location> $records
     * @return array<int, array<string, mixed>>
     */
    private static function buildTree(Collection $records): array
    {
        $byParent = [];

        foreach ($records as $record) {
            $key = $record->parent_id ?? 0;
            $byParent[$key][] = $record;
        }

        return self::buildTreeByParent(0, $byParent);
    }

    /**
     * @param array<int, array<int, Location>> $byParent
     * @return array<int, array<string, mixed>>
     */
    private static function buildTreeByParent(int $parentId, array $byParent): array
    {
        $nodes = [];

        foreach ($byParent[$parentId] ?? [] as $record) {
            $nodes[] = [
                'id' => $record->id,
                'name' => $record->name,
                'slug' => $record->slug,
                'depth' => $record->depth,
                'children' => self::buildTreeByParent($record->id, $byParent),
            ];
        }

        return $nodes;
    }

    /**
     * @param array<int, array<string, mixed>> $tree
     * @return array<string, array{id: int, slug: string, name: string, parent: string|null, depth: int, children: array<int, string>}>
     */
    private static function flatBySlug(array $tree): array
    {
        $flat = [];
        self::flattenBySlug($tree, null, $flat);

        return $flat;
    }

    /**
     * @param array<int, array<string, mixed>> $tree
     * @param array<string, array{id: int, slug: string, name: string, parent: string|null, depth: int, children: array<int, string>}> $flat
     */
    private static function flattenBySlug(array $tree, ?string $parentSlug, array &$flat): void
    {
        foreach ($tree as $node) {
            $slug = (string) ($node['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $children = $node['children'] ?? [];
            $flat[$slug] = [
                'id' => (int) ($node['id'] ?? 0),
                'slug' => $slug,
                'name' => (string) ($node['name'] ?? ''),
                'parent' => $parentSlug,
                'depth' => (int) ($node['depth'] ?? 0),
                'children' => array_values(array_filter(array_map(
                    fn ($child): string => (string) ($child['slug'] ?? ''),
                    is_array($children) ? $children : []
                ))),
            ];

            self::flattenBySlug(is_array($children) ? $children : [], $slug, $flat);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $tree
     * @return array<int, array{id: int, parent_id: int|null, depth: int, children: array<int, int>}>
     */
    private static function flatById(array $tree): array
    {
        $flat = [];
        self::flattenById($tree, null, $flat);

        return $flat;
    }

    /**
     * @param array<int, array<string, mixed>> $tree
     * @param array<int, array{id: int, parent_id: int|null, depth: int, children: array<int, int>}> $flat
     */
    private static function flattenById(array $tree, ?int $parentId, array &$flat): void
    {
        foreach ($tree as $node) {
            $id = (int) ($node['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $children = $node['children'] ?? [];
            $childIds = array_values(array_filter(array_map(
                fn ($child): int => (int) ($child['id'] ?? 0),
                is_array($children) ? $children : []
            )));

            $flat[$id] = [
                'id' => $id,
                'parent_id' => $parentId,
                'depth' => (int) ($node['depth'] ?? 0),
                'children' => $childIds,
            ];

            self::flattenById(is_array($children) ? $children : [], $id, $flat);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $tree
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, Location> $indexed
     */
    private static function flattenForAdmin(array $tree, array &$rows, array $indexed): void
    {
        foreach ($tree as $node) {
            $id = (int) ($node['id'] ?? 0);
            if ($id <= 0 || ! isset($indexed[$id])) {
                continue;
            }

            $record = $indexed[$id];
            $rows[] = [
                'id' => $id,
                'name' => (string) ($node['name'] ?? ''),
                'slug' => (string) ($node['slug'] ?? ''),
                'depth' => (int) ($node['depth'] ?? 0),
                'children_count' => (int) $record->children_count,
                'properties_count' => (int) $record->properties_count,
                'parent_name' => $record->parent?->name,
            ];

            self::flattenForAdmin($node['children'] ?? [], $rows, $indexed);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $tree
     * @param array<int, array{id: int, label: string, depth: int, city: string, neighborhood: string}> $options
     * @param array<int, int> $excluded
     */
    private static function flattenForSelect(array $tree, array &$options, array $excluded, ?string $city): void
    {
        foreach ($tree as $node) {
            $id = (int) ($node['id'] ?? 0);
            $depth = (int) ($node['depth'] ?? 0);
            $name = (string) ($node['name'] ?? '');
            $currentCity = $depth === 0 ? $name : ($city ?? $name);

            if ($id > 0 && ! in_array($id, $excluded, true)) {
                $options[] = [
                    'id' => $id,
                    'depth' => $depth,
                    'label' => str_repeat('- ', $depth).$name,
                    'city' => $currentCity,
                    'neighborhood' => $name,
                ];
            }

            self::flattenForSelect($node['children'] ?? [], $options, $excluded, $currentCity);
        }
    }

    /**
     * @param array<string, array{id: int, slug: string, name: string, parent: string|null, depth: int, children: array<int, string>}> $flat
     * @return array<int, string>
     */
    private static function collectDescendantSlugs(string $slug, array $flat): array
    {
        $queue = [$slug];
        $descendants = [];

        while ($queue !== []) {
            $current = array_shift($queue);
            if (! $current || ! isset($flat[$current])) {
                continue;
            }

            $descendants[] = $current;

            foreach ($flat[$current]['children'] as $childSlug) {
                $queue[] = $childSlug;
            }
        }

        return array_values(array_unique($descendants));
    }

    /**
     * @param array<int, array{id: int, parent_id: int|null, depth: int, children: array<int, int>}> $flat
     * @return array<int, int>
     */
    private static function collectDescendantIds(int $id, array $flat): array
    {
        $queue = [$id];
        $descendants = [];

        while ($queue !== []) {
            $current = array_shift($queue);
            if (! $current || ! isset($flat[$current])) {
                continue;
            }

            $descendants[] = $current;

            foreach ($flat[$current]['children'] as $childId) {
                $queue[] = $childId;
            }
        }

        return array_values(array_unique($descendants));
    }

    /**
     * @param array<string, array{id: int, slug: string, name: string, parent: string|null, depth: int, children: array<int, string>}> $flat
     * @return array{id: int, slug: string, name: string, parent: string|null, depth: int, children: array<int, string>}|null
     */
    private static function rootNode(string $slug, array $flat): ?array
    {
        if (! isset($flat[$slug])) {
            return null;
        }

        $current = $flat[$slug];

        while ($current['parent'] !== null && isset($flat[$current['parent']])) {
            $current = $flat[$current['parent']];
        }

        return $current;
    }

    /**
     * @param array<int, string> $values
     * @return array<int, string>
     */
    private static function withAsciiVariants(array $values): array
    {
        $variants = [];

        foreach ($values as $value) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                continue;
            }

            $variants[] = $trimmed;
            $ascii = trim(Str::ascii($trimmed));
            if ($ascii !== '' && $ascii !== $trimmed) {
                $variants[] = $ascii;
            }
        }

        return $variants;
    }
}
